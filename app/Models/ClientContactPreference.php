<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ClientContactPreference extends Model {
    protected $fillable = ["client_id","channel","contact","is_verified","is_active","sort_order"];
    protected $casts = ["is_verified"=>"boolean","is_active"=>"boolean"];
    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function scopeActive($query) { return $query->where("is_active", true); }
}