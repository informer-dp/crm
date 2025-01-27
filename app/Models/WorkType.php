<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'device_id']; // Дозвольте заповнення device_id

    public function device()
    {
        return $this->belongsTo(Device::class); // Зв'язок з моделлю Device
    }
}
