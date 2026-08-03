<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    // Всі можливі статуси
    const STATUS_NEW           = 'new';
    const STATUS_DIAGNOSED     = 'diagnosed';
    const STATUS_APPROVED      = 'approved';
    const STATUS_IN_PROGRESS   = 'in_progress';
    const STATUS_WAITING_PARTS = 'waiting_parts';
    const STATUS_READY         = 'ready';
    const STATUS_ISSUED        = 'issued';
    const STATUS_CANCELLED     = 'cancelled';

    // Типи заявок
    const TYPE_REPAIR      = 'repair';
    const TYPE_EXPRESS     = 'express';
    const TYPE_DIAGNOSTIC  = 'diagnostic';
    const TYPE_MAINTENANCE = 'maintenance';

    // Статуси що блокують редагування кошторису
    const LOCKED_STATUSES = [self::STATUS_ISSUED, self::STATUS_CANCELLED];

    // Активні статуси (заявка ще в роботі)
    const ACTIVE_STATUSES = [
        self::STATUS_NEW,
        self::STATUS_DIAGNOSED,
        self::STATUS_APPROVED,
        self::STATUS_IN_PROGRESS,
        self::STATUS_WAITING_PARTS,
        self::STATUS_READY,
    ];

    protected $fillable = [
        'number',
        'client_id',
        'device_id',
        'manager_id',
        'type',
        'priority',
        'status',
        'malfunction',
        'diagnosis',
        'notes',
        'check_code',
        'prepayment',
        'estimated_date',
        'issued_at',
        'cancelled_at',
        'cancel_reason',
    ];

    protected $casts = [
        'prepayment'     => 'decimal:2',
        'estimated_date' => 'date',
        'issued_at'      => 'datetime',
        'cancelled_at'   => 'datetime',
    ];

    // ──────────────────────────────────────────
    // Відносини
    // ──────────────────────────────────────────

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /** Pivot-таблиця виконавців */
    public function orderEngineers(): HasMany
    {
        return $this->hasMany(OrderEngineer::class);
    }

    /** Інженери через pivot */
    public function engineers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'order_engineers')
                    ->withPivot('is_primary', 'notes')
                    ->withTimestamps();
    }

    /** Головний інженер */
    public function primaryEngineer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'order_id')
                    ->join('order_engineers', 'users.id', '=', 'order_engineers.user_id')
                    ->where('order_engineers.is_primary', true);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at');
    }

    public function estimate(): HasOne
    {
        return $this->hasOne(Estimate::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(OrderTask::class)->orderByDesc('priority');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(OrderComment::class)->orderBy('created_at');
    }

    /** Коментарі видимі клієнту */
    public function publicComments(): HasMany
    {
        return $this->hasMany(OrderComment::class)
                    ->where('is_internal', false)
                    ->orderBy('created_at');
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(OrderReceipt::class);
    }

    public function warranty(): HasOne
    {
        return $this->hasOne(Warranty::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function subcontractorOrders(): HasMany
    {
        return $this->hasMany(SubcontractorOrder::class);
    }

    // ──────────────────────────────────────────
    // Скоупи
    // ──────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->whereIn('status', self::ACTIVE_STATUSES);
    }

    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }

    public function scopeByStatus($query, string|array $status)
    {
        return is_array($status)
            ? $query->whereIn('status', $status)
            : $query->where('status', $status);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForEngineer($query, int $userId)
    {
        return $query->whereHas('orderEngineers', fn($q) =>
            $q->where('user_id', $userId)
        );
    }

    public function scopeSearch($query, string $term)
{
    return $query->where(function ($q) use ($term) {
        $q->where('number', 'like', "%{$term}%")
          ->orWhere('malfunction', 'like', "%{$term}%")
          ->orWhereHas('client', fn($c) =>
              $c->where('name', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")
          )
          ->orWhereHas('device', fn($d) =>
              $d->where('serial_number', 'like', "%{$term}%")
                ->orWhere('imei', 'like', "%{$term}%")
                ->orWhereHas('model', fn($m) =>
                    $m->where('name', 'like', "%{$term}%")
                )
                ->orWhereHas('brand', fn($b) =>
                    $b->where('name', 'like', "%{$term}%")
                )
          );
    });
}

    // ──────────────────────────────────────────
    // Хелпери
    // ──────────────────────────────────────────

    public function isLocked(): bool
    {
        return in_array($this->status, self::LOCKED_STATUSES);
    }

    public function isActive(): bool
    {
        return in_array($this->status, self::ACTIVE_STATUSES);
    }

    public function isUrgent(): bool
    {
        return $this->priority === 'urgent';
    }

    public function canTransitionTo(string $newStatus): bool
    {
        $transitions = [
            self::STATUS_NEW           => [self::STATUS_DIAGNOSED, self::STATUS_IN_PROGRESS, self::STATUS_CANCELLED],
            self::STATUS_DIAGNOSED     => [self::STATUS_APPROVED, self::STATUS_WAITING_PARTS, self::STATUS_CANCELLED],
            self::STATUS_APPROVED      => [self::STATUS_IN_PROGRESS, self::STATUS_WAITING_PARTS, self::STATUS_CANCELLED],
            self::STATUS_IN_PROGRESS   => [self::STATUS_READY, self::STATUS_WAITING_PARTS, self::STATUS_CANCELLED],
            self::STATUS_WAITING_PARTS => [self::STATUS_IN_PROGRESS, self::STATUS_CANCELLED],
            self::STATUS_READY         => [self::STATUS_ISSUED, self::STATUS_CANCELLED],
            self::STATUS_ISSUED        => [],
            self::STATUS_CANCELLED     => [],
        ];

        return in_array($newStatus, $transitions[$this->status] ?? []);
    }

    /** Загальна оплачена сума */
    public function getPaidAmountAttribute(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    /** Залишок до оплати */
    public function getRemainingAmountAttribute(): float
    {
        $total = $this->estimate?->total ?? 0;
        return max(0, (float)$total - $this->paid_amount);
    }

    /** Мітки статусу українською */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_NEW           => 'Нова',
            self::STATUS_DIAGNOSED     => 'Діагностика',
            self::STATUS_APPROVED      => 'Узгоджено',
            self::STATUS_IN_PROGRESS   => 'В роботі',
            self::STATUS_WAITING_PARTS => 'Очікує деталей',
            self::STATUS_READY         => 'Готове',
            self::STATUS_ISSUED        => 'Видане',
            self::STATUS_CANCELLED     => 'Скасоване',
            default                    => $this->status,
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            self::TYPE_REPAIR      => 'Ремонт',
            self::TYPE_EXPRESS     => 'Експрес',
            self::TYPE_DIAGNOSTIC  => 'Діагностика',
            self::TYPE_MAINTENANCE => 'Обслуговування',
            default                => $this->type,
        };
    }

    // ──────────────────────────────────────────
    // Генерація номера замовлення
    // ──────────────────────────────────────────

    public static function generateNumber(): string
    {
        $prefix = config('crm.order_prefix', 'SC');
        $year   = date('Y');
        $last   = static::whereYear('created_at', $year)->max('id') ?? 0;
        return $prefix . '-' . $year . '-' . str_pad($last + 1, 5, '0', STR_PAD_LEFT);
    }

    // ──────────────────────────────────────────
    // Boot
    // ──────────────────────────────────────────

    protected static function booted(): void
    {
        // Автоматично генеруємо номер і check_code при створенні
        static::creating(function (Order $order) {
            if (empty($order->number)) {
                $order->number = static::generateNumber();
            }
            if (empty($order->check_code)) {
                $order->check_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            }
        });

        // Пишемо в історію статусів при зміні
        static::updating(function (Order $order) {
            if ($order->isDirty('status')) {
                OrderStatusHistory::create([
                    'order_id'    => $order->id,
                    'user_id'     => auth()->id(),
                    'status_from' => $order->getOriginal('status'),
                    'status_to'   => $order->status,
                ]);
            }
        });
    }
}
