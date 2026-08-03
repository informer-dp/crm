<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderEngineer extends Model
{
    protected $fillable = ['order_id', 'user_id', 'is_primary', 'notes'];

    protected $casts = ['is_primary' => 'boolean'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function engineer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
