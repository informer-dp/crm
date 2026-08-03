<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class WarrantyRule extends Model {
    protected $fillable = ["name","duration_days","conditions","exclusions","is_active"];
    protected $casts = ["is_active"=>"boolean"];
    public function estimateWorkWarranties(): HasMany { return $this->hasMany(EstimateWorkWarranty::class); }
    public function scopeActive($query) { return $query->where("is_active", true); }
}