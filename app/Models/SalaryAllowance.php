<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class SalaryAllowance extends Model {
    protected $fillable = ["user_id","name","amount","type","is_active","effective_from"];
    protected $casts = ["amount"=>"decimal:2","is_active"=>"boolean","effective_from"=>"date"];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function scopeActive($query) { return $query->where("is_active", true); }
}