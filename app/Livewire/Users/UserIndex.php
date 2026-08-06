<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UserIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $roleFilter = '';

    // ── Форма створення/редагування ───────────
    public bool $showForm = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $phone = '';
    public string $position = '';
    public string $selectedRole = '';
    public string $color = '#6366f1';
    public bool $isActive = true;

    // ── Зарплатні налаштування ────────────────
    public string $salaryBaseType = 'fixed';
    public string $salaryBaseAmount = '';
    public string $salaryBonusType = 'none';
    public string $salaryBonusPercent = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingRoleFilter(): void { $this->resetPage(); }

    protected function rules(): array
    {
        $passwordRule = $this->editingId ? 'nullable|min:6' : 'required|min:6';

        return [
            'name'         => 'required|min:2',
            'email'        => 'required|email|unique:users,email,' . ($this->editingId ?? 'NULL'),
            'password'     => $passwordRule,
            'selectedRole' => 'required|exists:roles,name',
            'phone'        => 'nullable',
            'position'     => 'nullable',
            'color'        => 'nullable',
            'salaryBaseAmount'   => 'nullable|numeric|min:0',
            'salaryBonusPercent' => 'nullable|numeric|min:0|max:100',
        ];
    }

    protected $messages = [
        'name.required'         => 'Введіть ім\'я',
        'email.required'        => 'Введіть email',
        'email.unique'          => 'Цей email вже використовується',
        'password.required'     => 'Введіть пароль',
        'password.min'          => 'Пароль мінімум 6 символів',
        'selectedRole.required' => 'Оберіть роль',
    ];

    public function openCreate(): void
    {
        $this->reset([
            'editingId', 'name', 'email', 'password', 'phone',
            'position', 'selectedRole', 'color', 'isActive',
            'salaryBaseType', 'salaryBaseAmount',
            'salaryBonusType', 'salaryBonusPercent',
        ]);
        $this->color = '#6366f1';
        $this->isActive = true;
        $this->salaryBaseType = 'fixed';
        $this->salaryBonusType = 'none';
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $user = User::with(['profile', 'roles'])->findOrFail($id);

        $this->editingId    = $user->id;
        $this->name         = $user->name;
        $this->email        = $user->email;
        $this->password     = '';
        $this->phone        = $user->profile?->phone ?? '';
        $this->position     = $user->profile?->position ?? '';
        $this->color        = $user->profile?->color ?? '#6366f1';
        $this->isActive     = $user->is_active;
        $this->selectedRole = $user->roles->first()?->name ?? '';

        // Поточні налаштування зарплати
        $salary = $user->currentSalarySetting();
        $this->salaryBaseType      = $salary?->base_type ?? 'fixed';
        $this->salaryBaseAmount    = $salary?->base_amount ?? '';
        $this->salaryBonusType     = $salary?->bonus_type ?? 'none';
        $this->salaryBonusPercent  = $salary?->bonus_percent ?? '';

        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingId) {
            // Оновлення
            $user = User::findOrFail($this->editingId);
            $user->update([
                'name'      => $this->name,
                'email'     => $this->email,
                'is_active' => $this->isActive,
            ]);

            if ($this->password) {
                $user->update(['password' => Hash::make($this->password)]);
            }
        } else {
            // Створення
            $user = User::create([
                'name'      => $this->name,
                'email'     => $this->email,
                'password'  => Hash::make($this->password),
                'is_active' => $this->isActive,
            ]);
        }

        // Роль
        $user->syncRoles([$this->selectedRole]);

        // Профіль
        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone'    => $this->phone ?: null,
                'position' => $this->position ?: null,
                'color'    => $this->color,
            ]
        );

        // Зарплатні налаштування
        if ($this->selectedRole !== 'client') {
            $current = $user->currentSalarySetting();

            $newSettings = [
                'base_type'      => $this->salaryBaseType,
                'base_amount'    => (float)($this->salaryBaseAmount ?: 0),
                'bonus_type'     => $this->salaryBonusType,
                'bonus_percent'  => (float)($this->salaryBonusPercent ?: 0),
                'effective_from' => now()->startOfMonth()->toDateString(),
                'effective_to'   => null,
            ];

            if ($current) {
                // Закриваємо старе і відкриваємо нове якщо щось змінилось
                if (
                    $current->base_amount != $newSettings['base_amount'] ||
                    $current->bonus_percent != $newSettings['bonus_percent'] ||
                    $current->base_type !== $newSettings['base_type'] ||
                    $current->bonus_type !== $newSettings['bonus_type']
                ) {
                    $current->update(['effective_to' => now()->toDateString()]);
                    $user->salarySettings()->create($newSettings);
                }
            } else {
                $user->salarySettings()->create($newSettings);
            }
        }

        $this->showForm = false;
        $this->resetPage();
    }

    public function toggleActive(int $id): void
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) return; // не можна деактивувати себе
        $user->update(['is_active' => !$user->is_active]);
    }

    public function render()
    {
        $users = User::with(['profile', 'roles'])
            ->when($this->search, fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
            )
            ->when($this->roleFilter, fn($q) => $q->role($this->roleFilter))
            ->orderBy('name')
            ->paginate(20);

        $roles = Role::orderBy('name')->get();

        return view('livewire.users.user-index', compact('users', 'roles'))
            ->extends('layouts.app')
            ->section('content');
    }
}