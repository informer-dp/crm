<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'name',
        'phone',
        'email',
        'tax_code',
        'contact_person',
        'contact_phone',
        'city',
        'address',
        'notes',
        'is_vip',
        'is_blacklisted',
        'blacklist_reason',
    ];

    protected $casts = [
        'is_vip'         => 'boolean',
        'is_blacklisted' => 'boolean',
    ];

    // ──────────────────────────────────────────
    // Відносини
    // ──────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function contactPreferences(): HasMany
    {
        return $this->hasMany(ClientContactPreference::class);
    }

    public function warrantyClaims(): HasMany
    {
        return $this->hasMany(WarrantyClaim::class);
    }

    // ──────────────────────────────────────────
    // Скоупи
    // ──────────────────────────────────────────

    public function scopeVip($query)
    {
        return $query->where('is_vip', true);
    }

    public function scopeBlacklisted($query)
    {
        return $query->where('is_blacklisted', true);
    }

    public function scopeIndividuals($query)
    {
        return $query->where('type', 'individual');
    }

    public function scopeLegal($query)
    {
        return $query->where('type', 'legal');
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('tax_code', 'like', "%{$term}%");
        });
    }

    // ──────────────────────────────────────────
    // Хелпери
    // ──────────────────────────────────────────

    public function getIsLegalAttribute(): bool
    {
        return $this->type === 'legal';
    }

    public function getActiveOrdersCountAttribute(): int
    {
        return $this->orders()
            ->whereNotIn('status', ['issued', 'cancelled'])
            ->count();
    }

    /** Preferred канал сповіщення */
    public function preferredContact(): ?ClientContactPreference
    {
        return $this->contactPreferences()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->first();
    }
}
