<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $fillable = ['name', 'device_type_id', 'logo', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function deviceType(): BelongsTo
    {
        return $this->belongsTo(DeviceType::class);
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

    public function scopeForType($query, int $deviceTypeId)
    {
        return $query->where(function ($q) use ($deviceTypeId) {
            $q->where('device_type_id', $deviceTypeId)
              ->orWhereNull('device_type_id'); // універсальні бренди
        });
    }
}
