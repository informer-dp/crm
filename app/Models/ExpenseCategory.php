<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
class ExpenseCategory extends Model {
    protected $fillable = ["parent_id","name","type","is_active"];
    protected $casts = ["is_active"=>"boolean"];
    public function parent(): BelongsTo { return $this->belongsTo(ExpenseCategory::class,"parent_id"); }
    public function children(): HasMany { return $this->hasMany(ExpenseCategory::class,"parent_id"); }
    public function expenses(): HasMany { return $this->hasMany(Expense::class,"category_id"); }
}