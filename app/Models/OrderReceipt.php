<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class OrderReceipt extends Model {
    protected $fillable = ["order_id","type","number","amount","payment_method","account_id","transaction_id","issued_by","notes","printed_at"];
    protected $casts = ["amount"=>"decimal:2","printed_at"=>"datetime"];
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function account(): BelongsTo { return $this->belongsTo(Account::class); }
    public function transaction(): BelongsTo { return $this->belongsTo(Transaction::class); }
    public function issuedBy(): BelongsTo { return $this->belongsTo(User::class,"issued_by"); }
    public function isPrinted(): bool { return !is_null($this->printed_at); }
}