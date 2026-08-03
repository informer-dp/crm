<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EstimateWork extends Model
{
    protected $fillable = [
        'estimate_id',
        'name',
        'work_type',
        'engineer_id',
        'subcontractor_id',
        'price',
        'cost',
        'quantity',
        'total',
        'is_warranty',
        'status',
    ];

    protected $casts = [
        'price'       => 'decimal:2',
        'cost'        => 'decimal:2',
        'total'       => 'decimal:2',
        'is_warranty' => 'boolean',
    ];

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(Estimate::class);
    }

    public function engineer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'engineer_id');
    }

    public function subcontractor(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'subcontractor_id');
    }

    public function warrantyRule(): HasOne
    {
        return $this->hasOne(EstimateWorkWarranty::class);
    }

    public function isOwn(): bool
    {
        return $this->work_type === 'own';
    }

    public function isSubcontract(): bool
    {
        return $this->work_type === 'subcontract';
    }

    public function getMarginAttribute(): float
    {
        return (float)$this->total - (float)$this->cost;
    }

    /** Перерахунок total при зміні ціни або кількості */
    protected static function booted(): void
    {
        static::saving(function (EstimateWork $work) {
            $work->total = $work->price * $work->quantity;
        });

        static::saved(function (EstimateWork $work) {
            $work->estimate->recalculate();
        });

        static::deleted(function (EstimateWork $work) {
            $work->estimate->recalculate();
        });
    }
}
