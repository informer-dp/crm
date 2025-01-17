<?php

// app/Models/OrderEstimation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderEstimation extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'item_name', 'quantity', 'price_per_unit'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
