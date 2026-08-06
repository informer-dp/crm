<div>
    {{-- Шапка --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Користувачі</h1>
            <p class="text-base-content/60 text-sm mt-1">Управління персоналом і доступами</p>
        </div>
        <button wire:click="openCreate" class="btn btn-primary gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Новий співробітник
        </button>
    </div>

    {{-- Фільтри --}}
    <div class="card bg-base-100 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="flex flex-wrap gap-3">
                <label class="input input-bordered flex items-center gap-2 flex-1 min-w-48">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input wire:model.live.debounce.300ms="search"
                           type="text" placeholder="Ім'я або email..." class="grow"/>
                </label>
                <select wire:model.live="roleFilter" class="select select-bordered w-44">
                    <option value="">Всі ролі</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Таблиця --}}
    <div class="card bg-base-100 shadow-sm">
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Співробітник</th>
                        <th>Роль</th>
                        <th>Посада</th>
                        <th>Зарплата</th>
                        <th>Статус</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="hover">
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="avatar placeholder">
                                    <div class="rounded-full w-9 text-white"
                                         style="background-color: {{ $user->profile?->color ?? '#6366f1' }}">
                                        <span class="text-sm">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="font-medium">{{ $user->name }}</div>
                                    <div class="text-xs text-base-content/60">{{ $user->email }}</div>
                                    @if($user->profile?->phone)
                                        <div class="text-xs text-base-content/60">{{ $user->profile->phone }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @foreach($user->roles as $role)
                                <span class="badge badge-ghost badge-sm">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td class="text-sm">{{ $user->profile?->position ?? '—' }}</td>
                        <td class="text-sm">
                            @php $salary = $user->currentSalarySetting(); @endphp
                            @if($salary)
                                @if($salary->base_type === 'fixed' && $salary->base_amount > 0)
                                    <span>{{ number_format($salary->base_amount, 0, '.', ' ') }} ₴</span>
                                @endif
                                @if($salary->bonus_type !== 'none' && $salary->bonus_percent > 0)
                                    <span class="text-primary">+ {{ $salary->bonus_percent }}%</span>
                                @endif
                            @else
                                <span class="text-base-content/40">—</span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge badge-success badge-sm">Активний</span>
                            @else
                                <span class="badge badge-ghost badge-sm">Неактивний</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex gap-1">
                                <button wire:click="openEdit({{ $user->id }})"
                                        class="btn btn-ghost btn-xs">
                                    Редагувати
                                </button>
                                @if($user->id !== auth()->id())
                                <button wire:click="toggleActive({{ $user->id }})"
                                        wire:confirm="{{ $user->is_active ? 'Деактивувати користувача?' : 'Активувати користувача?' }}"
                                        class="btn btn-ghost btn-xs {{ $user->is_active ? 'text-error' : 'text-success' }}">
                                    {{ $user->is_active ? 'Деактивувати' : 'Активувати' }}
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-base-content/40">
                            Користувачів не знайдено
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="p-4 border-t border-base-200">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    {{-- Форма створення/редагування --}}
    @if($showForm)
    <div style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;display:flex;align-items:flex-start;justify-content:center;background:rgba(0,0,0,0.6);overflow-y:auto;padding:20px">
        <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:560px;margin:auto">

            <h3 style="font-size:18px;font-weight:bold;margin-bottom:20px">
                {{ $editingId ? 'Редагування співробітника' : 'Новий співробітник' }}
            </h3>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">

                {{-- Ім'я --}}
                <div style="grid-column:span 2">
                    <label style="font-size:13px;display:block;margin-bottom:4px">Ім'я *</label>
                    <input wire:model="name" type="text"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"
                           placeholder="Іван Петренко"/>
                    @error('name')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                </div>

                {{-- Email --}}
                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Email *</label>
                    <input wire:model="email" type="email"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                    @error('email')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                </div>

                {{-- Пароль --}}
                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">
                        Пароль {{ $editingId ? '(залиш порожнім щоб не змінювати)' : '*' }}
                    </label>
                    <input wire:model="password" type="password"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                    @error('password')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                </div>

                {{-- Телефон --}}
                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Телефон</label>
                    <input wire:model="phone" type="text"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"
                           placeholder="+380501234567"/>
                </div>

                {{-- Посада --}}
                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Посада</label>
                    <input wire:model="position" type="text"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"
                           placeholder="Інженер, Менеджер..."/>
                </div>

                {{-- Роль --}}
                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Роль *</label>
                    <select wire:model.live="selectedRole"
                            style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                        <option value="">Оберіть роль...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                    @error('selectedRole')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                </div>

                {{-- Колір --}}
                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Колір у календарі</label>
                    <div style="display:flex;gap:8px;align-items:center">
                        <input wire:model="color" type="color"
                               style="width:48px;height:36px;border:1px solid #ddd;border-radius:8px;padding:2px;cursor:pointer"/>
                        <span style="font-size:13px;color:#666">{{ $color }}</span>
                    </div>
                </div>

                {{-- Активний --}}
                <div style="grid-column:span 2">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                        <input wire:model="isActive" type="checkbox"
                               style="width:16px;height:16px"/>
                        <span style="font-size:14px">Активний (має доступ до системи)</span>
                    </label>
                </div>

            </div>

            {{-- Зарплатні налаштування --}}
            @if($selectedRole && $selectedRole !== 'client')
            <div style="margin-top:20px;padding-top:16px;border-top:1px solid #eee">
                <p style="font-size:14px;font-weight:600;margin-bottom:12px">Нарахування зарплати</p>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <div>
                        <label style="font-size:13px;display:block;margin-bottom:4px">Тип ставки</label>
                        <select wire:model.live="salaryBaseType"
                                style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                            <option value="fixed">Фіксована ставка</option>
                            <option value="none">Без ставки</option>
                        </select>
                    </div>

                    @if($salaryBaseType === 'fixed')
                    <div>
                        <label style="font-size:13px;display:block;margin-bottom:4px">Сума ставки (₴/міс)</label>
                        <input wire:model="salaryBaseAmount" type="number" min="0"
                               style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"
                               placeholder="10000"/>
                    </div>
                    @endif

                    <div>
                        <label style="font-size:13px;display:block;margin-bottom:4px">Бонус</label>
                        <select wire:model.live="salaryBonusType"
                                style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                            <option value="none">Без бонусу</option>
                            <option value="percent_orders">% від заявок</option>
                            <option value="percent_works">% від виконаних робіт</option>
                        </select>
                    </div>

                    @if($salaryBonusType !== 'none')
                    <div>
                        <label style="font-size:13px;display:block;margin-bottom:4px">Відсоток (%)</label>
                        <input wire:model="salaryBonusPercent" type="number" min="0" max="100" step="0.5"
                               style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"
                               placeholder="10"/>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Кнопки --}}
            <div style="display:flex;gap:8px;margin-top:20px">
                <button wire:click="save"
                        style="flex:1;padding:10px;background:#6366f1;color:white;border:none;border-radius:8px;font-size:15px;cursor:pointer;font-weight:500">
                    {{ $editingId ? 'Зберегти зміни' : 'Створити співробітника' }}
                </button>
                <button wire:click="$set('showForm', false)"
                        style="padding:10px 16px;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer">
                    Скасувати
                </button>
            </div>

        </div>
    </div>
    @endif

</div>