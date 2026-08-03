<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'name', 'type', 'contact_person',
        'phone', 'email', 'website',
        'notes', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function partPrices(): HasMany
    {
        return $this->hasMany(PartSupplierPrice::class);
    }

    public function debts(): HasMany
    {
        return $this->hasMany(SupplierDebt::class);
    }

    public function subcontractOrders(): HasMany
    {
        return $this->hasMany(SubcontractorOrder::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSubcontractors($query)
    {
        return $query->where('type', 'subcontractor');
    }

    public function getTotalDebtAttribute(): float
    {
        return (float) $this->debts()
            ->whereIn('status', ['unpaid', 'partial'])
            ->sum('amount_remaining');
    }
}
