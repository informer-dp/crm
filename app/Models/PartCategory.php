<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
class PartCategory extends Model {
    protected $fillable = ["parent_id","name","is_active"];
    protected $casts = ["is_active"=>"boolean"];
    public function parent(): BelongsTo { return $this->belongsTo(PartCategory::class,"parent_id"); }
    public function children(): HasMany { return $this->hasMany(PartCategory::class,"parent_id"); }
    public function parts(): HasMany { return $this->hasMany(Part::class,"category_id"); }
}