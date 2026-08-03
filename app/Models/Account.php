<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Account extends Model {
    protected $fillable = ["name","type","balance","currency","is_active"];
    protected $casts = ["balance"=>"decimal:2","is_active"=>"boolean"];
    public function transactions(): HasMany { return $this->hasMany(Transaction::class); }
    public function orderPayments(): HasMany { return $this->hasMany(OrderPayment::class); }
    public function salaryPayments(): HasMany { return $this->hasMany(SalaryPayment::class); }
    public function scopeActive($query) { return $query->where("is_active", true); }
}