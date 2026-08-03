<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Part extends Model
{
    protected $fillable = [
        'category_id', 'name', 'sku', 'unit',
        'retail_price', 'stock_qty', 'min_stock_qty',
        'track_stock', 'is_active', 'notes',
    ];

    protected $casts = [
        'retail_price'  => 'decimal:2',
        'track_stock'   => 'boolean',
        'is_active'     => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(PartCategory::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function supplierPrices(): HasMany
    {
        return $this->hasMany(PartSupplierPrice::class);
    }

    public function estimateParts(): HasMany
    {
        return $this->hasMany(EstimatePart::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->where('track_stock', true)
                     ->whereColumn('stock_qty', '<=', 'min_stock_qty');
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'like', "%{$term}%")
                     ->orWhere('sku', 'like', "%{$term}%");
    }

    public function isLowStock(): bool
    {
        return $this->track_stock && $this->stock_qty <= $this->min_stock_qty;
    }

    public function isOutOfStock(): bool
    {
        return $this->track_stock && $this->stock_qty <= 0;
    }

    /** Preferred постачальник */
    public function preferredSupplierPrice(): ?PartSupplierPrice
    {
        return $this->supplierPrices()->where('is_preferred', true)->first();
    }
}
