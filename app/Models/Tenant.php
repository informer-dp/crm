<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Tenant extends Model {
    protected $fillable = ["name","slug","plan","is_active","trial_ends_at","plan_expires_at"];
    protected $casts = ["is_active"=>"boolean","trial_ends_at"=>"date","plan_expires_at"=>"date"];
    public function settings(): HasMany { return $this->hasMany(Setting::class); }
    public function isOnTrial(): bool { return $this->trial_ends_at && $this->trial_ends_at->isFuture(); }
    public function isActive(): bool { return $this->is_active && ($this->plan_expires_at === null || $this->plan_expires_at->isFuture()); }
}