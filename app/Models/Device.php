<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Device extends Model
{
    protected $fillable = [
        'device_type_id',
        'brand_id',
        'model_id',
        'serial_number',
        'imei',
        'color',
        'appearance',
        'production_year',
    ];

    // ──────────────────────────────────────────
    // Відносини
    // ──────────────────────────────────────────

    public function deviceType(): BelongsTo
    {
        return $this->belongsTo(DeviceType::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(DeviceModel::class, 'model_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(DevicePhoto::class);
    }

    /** Всі заявки по цьому пристрою (незалежно від клієнта) */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** Поточна активна заявка */
    public function activeOrder(): HasOne
    {
        return $this->hasOne(Order::class)
            ->whereNotIn('status', ['issued', 'cancelled'])
            ->latest();
    }

    // ──────────────────────────────────────────
    // Хелпери
    // ──────────────────────────────────────────

    /** Apple iPhone 15 Pro */
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->brand?->name,
            $this->model?->name,
        ]);
        return implode(' ', $parts) ?: 'Невідомий пристрій';
    }

    /** Для відображення в списках */
    public function getDisplayNameAttribute(): string
    {
        $name = $this->full_name;
        if ($this->serial_number) {
            $name .= ' [SN: ' . $this->serial_number . ']';
        } elseif ($this->imei) {
            $name .= ' [IMEI: ' . $this->imei . ']';
        }
        return $name;
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('serial_number', 'like', "%{$term}%")
              ->orWhere('imei', 'like', "%{$term}%")
              ->orWhereHas('model', fn($m) => $m->where('name', 'like', "%{$term}%"))
              ->orWhereHas('brand', fn($b) => $b->where('name', 'like', "%{$term}%"));
        });
    }
}
