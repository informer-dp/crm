<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
class SupplierDebt extends Model {
    protected $fillable = ["supplier_id","purchase_id","amount_total","amount_paid","amount_remaining","status","due_date"];
    protected $casts = ["amount_total"=>"decimal:2","amount_paid"=>"decimal:2","amount_remaining"=>"decimal:2","due_date"=>"date"];
    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function purchase(): BelongsTo { return $this->belongsTo(Purchase::class); }
    public function payments(): HasMany { return $this->hasMany(SupplierDebtPayment::class,"debt_id"); }
    public function scopeUnpaid($query) { return $query->whereIn("status",["unpaid","partial"]); }
}