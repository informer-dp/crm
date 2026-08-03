<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class SupplierDebtPayment extends Model {
    protected $fillable = ["debt_id","account_id","transaction_id","amount","user_id"];
    protected $casts = ["amount"=>"decimal:2"];
    public function debt(): BelongsTo { return $this->belongsTo(SupplierDebt::class); }
    public function account(): BelongsTo { return $this->belongsTo(Account::class); }
    public function transaction(): BelongsTo { return $this->belongsTo(Transaction::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}