<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstimatePart extends Model
{
    protected $fillable = [
        'estimate_id',
        'part_id',
        'name',
        'price',
        'cost',
        'quantity',
        'total',
        'is_own_part',
    ];

    protected $casts = [
        'price'       => 'decimal:2',
        'cost'        => 'decimal:2',
        'total'       => 'decimal:2',
        'is_own_part' => 'boolean',
    ];

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(Estimate::class);
    }

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class);
    }

    public function getMarginAttribute(): float
    {
        return (float)$this->total - (float)$this->cost;
    }

    protected static function booted(): void
    {
        static::saving(function (EstimatePart $part) {
            $part->total = $part->price * $part->quantity;
        });

        static::saved(function (EstimatePart $part) {
            $part->estimate->recalculate();
        });

        static::deleted(function (EstimatePart $part) {
            $part->estimate->recalculate();
        });
    }
}
