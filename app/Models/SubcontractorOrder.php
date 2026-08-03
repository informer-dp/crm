<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany};
class SubcontractorOrder extends Model {
    protected $fillable = ["supplier_id","order_id","status","cost","sent_at","expected_at","received_at","notes"];
    protected $casts = ["cost"=>"decimal:2","sent_at"=>"date","expected_at"=>"date","received_at"=>"date"];
    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function works(): BelongsToMany { return $this->belongsToMany(EstimateWork::class,"subcontractor_order_works","subcontractor_order_id","estimate_work_id"); }
}