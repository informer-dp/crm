<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class SalaryPayment extends Model {
    protected $fillable = ["period_id","account_id","transaction_id","amount","user_id","paid_by"];
    protected $casts = ["amount"=>"decimal:2"];
    public function period(): BelongsTo { return $this->belongsTo(SalaryPeriod::class); }
    public function account(): BelongsTo { return $this->belongsTo(Account::class); }
    public function transaction(): BelongsTo { return $this->belongsTo(Transaction::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function paidBy(): BelongsTo { return $this->belongsTo(User::class,"paid_by"); }
}