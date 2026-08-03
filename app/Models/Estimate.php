<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estimate extends Model
{
    protected $fillable = [
        'order_id',
        'works_total',
        'parts_total',
        'discount',
        'discount_type',
        'total',
        'is_approved',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'works_total' => 'decimal:2',
        'parts_total' => 'decimal:2',
        'discount'    => 'decimal:2',
        'total'       => 'decimal:2',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    // ──────────────────────────────────────────
    // Відносини
    // ──────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function works(): HasMany
    {
        return $this->hasMany(EstimateWork::class)->orderBy('id');
    }

    public function parts(): HasMany
    {
        return $this->hasMany(EstimatePart::class)->orderBy('id');
    }

    public function ownWorks(): HasMany
    {
        return $this->hasMany(EstimateWork::class)
                    ->where('work_type', 'own');
    }

    public function subcontractWorks(): HasMany
    {
        return $this->hasMany(EstimateWork::class)
                    ->where('work_type', 'subcontract');
    }

    // ──────────────────────────────────────────
    // Хелпери
    // ──────────────────────────────────────────

    /** Чи можна редагувати (не issued/cancelled) */
    public function isEditable(): bool
    {
        return !$this->order->isLocked();
    }

    /** Перерахунок підсумків на основі рядків */
    public function recalculate(): void
    {
        $worksTotal = $this->works()->sum('total');
        $partsTotal = $this->parts()->sum('total');
        $subtotal   = $worksTotal + $partsTotal;

        $discount = $this->discount_type === 'percent'
            ? $subtotal * ($this->discount / 100)
            : $this->discount;

        $this->update([
            'works_total' => $worksTotal,
            'parts_total' => $partsTotal,
            'total'       => max(0, $subtotal - $discount),
        ]);
    }

    /** Загальна маржа (ціна - собівартість) */
    public function getMarginAttribute(): float
    {
        $revenue = (float)$this->total;
        $cost    = $this->works()->sum('cost') + $this->parts()->sum('cost');
        return $revenue - (float)$cost;
    }

    public function getMarginPercentAttribute(): float
    {
        if (!$this->total) return 0;
        return round($this->margin / $this->total * 100, 1);
    }
}
