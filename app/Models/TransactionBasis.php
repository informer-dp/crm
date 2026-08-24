<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionBasis extends Model
{
    protected $fillable = [
        'group', 'name', 'flow_type', 'is_active', 'sort_order'
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'basis_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForIncome($query)
    {
        return $query->whereIn('flow_type', ['income', 'both']);
    }

    public function scopeForExpense($query)
    {
        return $query->whereIn('flow_type', ['expense', 'both']);
    }

    public function scopeForInternal($query)
    {
        return $query->where('flow_type', 'internal');
    }
}