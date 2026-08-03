<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeviceType extends Model
{
    protected $fillable = ['name', 'icon', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function brands(): HasMany
    {
        return $this->hasMany(Brand::class);
    }

    public function models(): HasMany
    {
        return $this->hasMany(DeviceModel::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
