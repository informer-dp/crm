<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
class Warranty extends Model {
    protected $fillable = ["order_id","number","issued_at","expires_at","status","covered_works","terms","issued_by","printed_at"];
    protected $casts = ["issued_at"=>"date","expires_at"=>"date","printed_at"=>"datetime"];
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function issuedBy(): BelongsTo { return $this->belongsTo(User::class,"issued_by"); }
    public function claims(): HasMany { return $this->hasMany(WarrantyClaim::class); }
    public function isActive(): bool { return $this->status==="active" && $this->expires_at->isFuture(); }
    public function getDaysRemainingAttribute(): int { return max(0,(int)now()->diffInDays($this->expires_at,false)); }
}