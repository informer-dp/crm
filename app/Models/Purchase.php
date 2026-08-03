<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Support\Facades\DB;
class Purchase extends Model {
    protected $fillable = ["supplier_id","order_id","status","source_url","shipping_cost","notes","created_by"];
    protected $casts = ["shipping_cost"=>"decimal:2"];
    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class,"created_by"); }
    public function items(): HasMany { return $this->hasMany(PurchaseItem::class); }
    public function getTotalCostAttribute(): float { return (float)$this->items()->sum(DB::raw("qty_received * unit_cost")) + (float)$this->shipping_cost; }
}