<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class DonorDevice extends Model {
    protected $fillable = ["device_type_id","brand_id","model_id","condition","purchase_price","allocated_cost"];
    protected $casts = ["purchase_price"=>"decimal:2","allocated_cost"=>"decimal:2"];
    public function deviceType(): BelongsTo { return $this->belongsTo(DeviceType::class); }
    public function brand(): BelongsTo { return $this->belongsTo(Brand::class); }
    public function model(): BelongsTo { return $this->belongsTo(DeviceModel::class,"model_id"); }
    public function getIsAmortizedAttribute(): bool { return $this->allocated_cost >= $this->purchase_price; }
    public function getRemainingValueAttribute(): float { return max(0, (float)$this->purchase_price - (float)$this->allocated_cost); }
}