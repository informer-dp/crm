<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Expense extends Model {
    protected $fillable = ["order_id","category_id","supplier_id","account_id","transaction_id","description","amount","is_paid","expense_date","due_date","created_by"];
    protected $casts = ["amount"=>"decimal:2","is_paid"=>"boolean","expense_date"=>"date","due_date"=>"date"];
    public function category(): BelongsTo { return $this->belongsTo(ExpenseCategory::class); }
    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function account(): BelongsTo { return $this->belongsTo(Account::class); }
    public function transaction(): BelongsTo { return $this->belongsTo(Transaction::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class,"created_by"); }
    public function scopeUnpaid($query) { return $query->where("is_paid", false); }
    public function scopeOverdue($query) { return $query->where("is_paid",false)->where("due_date","<",now()); }
    public function order(): BelongsTo{return $this->belongsTo(Order::class); }
}