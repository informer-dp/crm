<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo};
class Transaction extends Model {
    protected $fillable = ["account_id","type","amount","balance_after","reference_type","reference_id","user_id","description","transaction_date"];
    protected $casts = ["amount"=>"decimal:2","balance_after"=>"decimal:2","transaction_date"=>"date"];
    public function account(): BelongsTo { return $this->belongsTo(Account::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function reference(): MorphTo { return $this->morphTo(); }
}