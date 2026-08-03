<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class EstimateWorkWarranty extends Model {
    protected $table = "estimate_works_warranty";
    protected $fillable = ["estimate_work_id","warranty_rule_id","duration_days"];
    public function estimateWork(): BelongsTo { return $this->belongsTo(EstimateWork::class); }
    public function warrantyRule(): BelongsTo { return $this->belongsTo(WarrantyRule::class); }
}