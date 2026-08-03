<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class SalaryBonus extends Model {
    protected $fillable = ["user_id","period_id","name","amount","type","created_by"];
    protected $casts = ["amount"=>"decimal:2"];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function period(): BelongsTo { return $this->belongsTo(SalaryPeriod::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class,"created_by"); }
}