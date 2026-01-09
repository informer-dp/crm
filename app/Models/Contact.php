<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'notes'
    ];

    public function counterparties()
    {
        return $this->hasMany(Counterparty::class);
    }
}

