<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class DevicePhoto extends Model {
    protected $fillable = ["device_id","path","type"];
    public function device(): BelongsTo { return $this->belongsTo(Device::class); }
    public function getUrlAttribute(): string { return asset("storage/" . $this->path); }
}