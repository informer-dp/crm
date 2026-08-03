<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class PurchaseItem extends Model {
    protected $fillable = ["purchase_id","part_id","name","qty_ordered","qty_received","qty_returned","unit_cost","unit_price","donor_device_id"];
    protected $casts = ["unit_cost"=>"decimal:2","unit_price"=>"decimal:2"];
    public function purchase(): BelongsTo { return $this->belongsTo(Purchase::class); }
    public function part(): BelongsTo { return $this->belongsTo(Part::class); }
    public function donorDevice(): BelongsTo { return $this->belongsTo(DonorDevice::class); }
    public function getMarginAttribute(): float { return ((float)$this->unit_price - (float)$this->unit_cost) * $this->qty_received; }
}