<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Counterparty extends Model
{
    protected $fillable = [
        'contact_id',
        'type',
        'group_id',
        'personal_discount',
        'is_active'
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
    public function group()
{
    return $this->belongsTo(CounterpartyGroup::class, 'group_id');
}



}

