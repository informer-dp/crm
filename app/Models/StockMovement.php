<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    protected $fillable = [
        'part_id', 'type', 'qty',
        'unit_cost', 'unit_price',
        'reference_type', 'reference_id',
        'user_id', 'notes',
    ];

    protected $casts = [
        'unit_cost'  => 'decimal:2',
        'unit_price' => 'decimal:2',
    ];

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isIncoming(): bool
    {
        return $this->qty > 0;
    }

    /** Після збереження оновлюємо stock_qty у Part */
    protected static function booted(): void
    {
        static::created(function (StockMovement $movement) {
            $movement->part->increment('stock_qty', $movement->qty);
        });
    }
}
