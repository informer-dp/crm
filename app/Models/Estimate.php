<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estimate extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'description', 'part_cost', 'labor_cost', 'total_cost'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
