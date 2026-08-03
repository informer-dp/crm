<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];

    // ──────────────────────────────────────────
    // Відносини
    // ──────────────────────────────────────────

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /** Заявки які прийняв цей менеджер */
    public function managedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'manager_id');
    }

    /** Заявки де цей інженер є виконавцем */
    public function engineerOrders(): HasMany
    {
        return $this->hasMany(OrderEngineer::class);
    }

    public function salarySettings(): HasMany
    {
        return $this->hasMany(SalarySetting::class);
    }

    public function salaryPeriods(): HasMany
    {
        return $this->hasMany(SalaryPeriod::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(OrderTask::class, 'assigned_to');
    }

    // ──────────────────────────────────────────
    // Скоупи
    // ──────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeEngineers($query)
    {
        return $query->role('engineer');
    }

    public function scopeManagers($query)
    {
        return $query->role('manager');
    }

    // ──────────────────────────────────────────
    // Хелпери
    // ──────────────────────────────────────────

    /** Поточне налаштування зарплати */
    public function currentSalarySetting(): ?SalarySetting
    {
        return $this->salarySettings()
            ->whereNull('effective_to')
            ->orWhere('effective_to', '>=', now())
            ->orderByDesc('effective_from')
            ->first();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isEngineer(): bool
    {
        return $this->hasRole('engineer');
    }

    public function isManager(): bool
    {
        return $this->hasRole('manager');
    }
}
