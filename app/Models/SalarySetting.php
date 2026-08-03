<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class SalarySetting extends Model {
    protected $fillable = ["user_id","base_type","base_amount","bonus_type","bonus_percent","effective_from","effective_to"];
    protected $casts = ["base_amount"=>"decimal:2","bonus_percent"=>"decimal:2","effective_from"=>"date","effective_to"=>"date"];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function scopeCurrent($query) { return $query->whereNull("effective_to")->orWhere("effective_to",">=",now()); }
}