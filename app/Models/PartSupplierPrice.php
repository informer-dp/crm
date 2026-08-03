<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class PartSupplierPrice extends Model {
    public $timestamps = false;
    protected $fillable = ["part_id","supplier_id","unit_cost","supplier_sku","is_preferred","updated_at"];
    protected $casts = ["unit_cost"=>"decimal:2","is_preferred"=>"boolean","updated_at"=>"datetime"];
    public function part(): BelongsTo { return $this->belongsTo(Part::class); }
    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
}