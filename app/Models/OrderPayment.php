<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class OrderPayment extends Model {
    protected $fillable = ["order_id","account_id","transaction_id","amount","payment_method","type","user_id"];
    protected $casts = ["amount"=>"decimal:2"];
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function account(): BelongsTo { return $this->belongsTo(Account::class); }
    public function transaction(): BelongsTo { return $this->belongsTo(Transaction::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}