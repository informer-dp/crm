<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'position',
        'salary_type',
        'base_salary',
        'bonus_percent',
        'telegram_chat_id',
        'avatar',
        'color',
        'notes',
    ];

    protected $casts = [
        'base_salary'    => 'decimal:2',
        'bonus_percent'  => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : asset('images/default-avatar.png');
    }
}
