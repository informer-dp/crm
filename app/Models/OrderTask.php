<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTask extends Model
{
    protected $fillable = [
        'order_id', 'assigned_to', 'created_by',
        'title', 'description', 'status', 'priority',
        'due_at', 'completed_at',
    ];

    protected $casts = [
        'due_at'       => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_at', '<', now())
                     ->where('status', '!=', 'done');
    }

    public function markDone(): void
    {
        $this->update(['status' => 'done', 'completed_at' => now()]);
    }
}
