<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Називаємо DeviceModel щоб не конфліктувати з базовим Model Laravel
class DeviceModel extends Model
{
    protected $table = 'models';

    protected $fillable = ['brand_id', 'device_type_id', 'name', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function deviceType(): BelongsTo
    {
        return $this->belongsTo(DeviceType::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class, 'model_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForBrand($query, int $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    /** Повна назва: Apple iPhone 15 Pro */
    public function getFullNameAttribute(): string
    {
        return $this->brand->name . ' ' . $this->name;
    }
}
