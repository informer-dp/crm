<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CounterpartyGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
        'default_discount',
    ];

    public function counterparties()
    {
        return $this->hasMany(Counterparty::class, 'group_id');
    }
}

