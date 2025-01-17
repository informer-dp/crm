<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', 
        'device_type', 
        'device_brand', 
        'device_model', 
        'serial_number', 
        'problem_description', 
        'status'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function parts()
    {
        return $this->belongsToMany(Part::class, 'order_parts')
                    ->withPivot('quantity');
    }

    public function estimate()
    {
        return $this->hasOne(Estimate::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
    public function estimations()
{
    return $this->hasMany(OrderEstimation::class);
}
}
