<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatusHistory extends Model
{
    protected $table = 'order_status_history'; // додай цей рядок

    protected $fillable = ['order_id', 'user_id', 'status_from', 'status_to', 'comment'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusFromLabelAttribute(): string
    {
        return $this->status_from
            ? (new Order)->setRawAttributes(['status' => $this->status_from])->status_label
            : '—';
    }

    public function getStatusToLabelAttribute(): string
    {
        return (new Order)->setRawAttributes(['status' => $this->status_to])->status_label;
    }
}