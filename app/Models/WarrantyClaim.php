<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class WarrantyClaim extends Model {
    protected $fillable = ["warranty_id","new_order_id","client_id","claim_date","description","status","resolution","handled_by"];
    protected $casts = ["claim_date"=>"date"];
    public function warranty(): BelongsTo { return $this->belongsTo(Warranty::class); }
    public function newOrder(): BelongsTo { return $this->belongsTo(Order::class,"new_order_id"); }
    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function handledBy(): BelongsTo { return $this->belongsTo(User::class,"handled_by"); }
}