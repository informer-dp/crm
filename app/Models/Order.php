<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;


class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'counterparty_id', 
        'device_id', 
        'brand_id', 
        'device_model', 
        'serial_number', 
        'equipment',
        'problem_description', 
        'status_id'
    ];

    protected $attributes = [
        'status_id' => 1,
    ];
    public function counterparty()
{
    return $this->belongsTo(Counterparty::class);
}


    public function client()
{
    return $this->belongsTo(Counterparty::class, 'counterparty_id');
}

public function device()
{
    return $this->belongsTo(Device::class);
}


    public function parts()
    {
        return $this->belongsToMany(Part::class, 'order_parts')
                    ->withPivot('quantity');
    }

    public function estimates()
    {
        return $this->hasOne(Estimate::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
    public function estimations()
{
    return $this->hasMany(Estimate::class);
}

public function brand()
{
    return $this->belongsTo(\App\Models\Brand::class);
}
public function status()
{
    return $this->belongsTo(OrderStatus::class);
}
public function activities()
{
    return $this->morphMany(Activity::class, 'subject')
        ->latest();
}


}