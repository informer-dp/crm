<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
class SalaryPeriod extends Model {
    protected $fillable = ["user_id","period_from","period_to","base_earned","bonus_earned","allowances_total","bonuses_total","deductions_total","total_accrued","total_paid","status","approved_by"];
    protected $casts = ["period_from"=>"date","period_to"=>"date","base_earned"=>"decimal:2","bonus_earned"=>"decimal:2","allowances_total"=>"decimal:2","bonuses_total"=>"decimal:2","deductions_total"=>"decimal:2","total_accrued"=>"decimal:2","total_paid"=>"decimal:2"];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function approvedBy(): BelongsTo { return $this->belongsTo(User::class,"approved_by"); }
    public function bonuses(): HasMany { return $this->hasMany(SalaryBonus::class,"period_id"); }
    public function payments(): HasMany { return $this->hasMany(SalaryPayment::class,"period_id"); }
    public function getBalanceAttribute(): float { return (float)$this->total_accrued - (float)$this->total_paid; }
}