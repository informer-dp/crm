<div>
    {{-- Шапка --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Зарплата</h1>
            <p class="text-base-content/60 text-sm mt-1">Нарахування та виплати</p>
        </div>
        <div class="flex gap-2">
            <input wire:model.live="selectedMonth" type="month"
                   class="input input-bordered input-sm"/>
            <button wire:click="openCalculate()"
                    class="btn btn-primary btn-sm gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Розрахувати
            </button>
        </div>
    </div>

    {{-- Вкладки --}}
    <div class="tabs tabs-bordered mb-4">
        <button wire:click="$set('activeTab', 'periods')"
                class="tab {{ $activeTab === 'periods' ? 'tab-active' : '' }}">
            Розрахункові періоди
        </button>
        <button wire:click="$set('activeTab', 'staff')"
                class="tab {{ $activeTab === 'staff' ? 'tab-active' : '' }}">
            Співробітники
        </button>
        <button wire:click="$set('activeTab', 'allowances')"
                class="tab {{ $activeTab === 'allowances' ? 'tab-active' : '' }}">
            Надбавки
            @if($allowances->count())
                <span class="badge badge-ghost badge-sm ml-1">{{ $allowances->count() }}</span>
            @endif
        </button>
        <button wire:click="$set('activeTab', 'bonuses')"
                class="tab {{ $activeTab === 'bonuses' ? 'tab-active' : '' }}">
            Бонуси
            @if($unpaidBonuses->count())
                <span class="badge badge-warning badge-sm ml-1">{{ $unpaidBonuses->count() }}</span>
            @endif
        </button>
    </div>

    {{-- Розрахункові періоди --}}
    @if($activeTab === 'periods')
    <div class="card bg-base-100 shadow-sm">
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Співробітник</th>
                        <th>Період</th>
                        <th class="text-right">Ставка</th>
                        <th class="text-right">Бонус</th>
                        <th class="text-right">Надбавки</th>
                        <th class="text-right">Разові</th>
                        <th class="text-right">Нараховано</th>
                        <th class="text-right">Виплачено</th>
                        <th>Статус</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($periods as $period)
                    <tr class="hover">
                        <td>
                            <div class="font-medium text-sm">{{ $period->user->name }}</div>
                            <div class="text-xs text-base-content/60">{{ $period->user->profile?->position }}</div>
                        </td>
                        <td class="text-sm">
                            {{ \Carbon\Carbon::parse($period->period_from)->format('d.m') }} —
                            {{ \Carbon\Carbon::parse($period->period_to)->format('d.m.Y') }}
                        </td>
                        <td class="text-right text-sm">{{ number_format($period->base_earned, 0, '.', ' ') }} ₴</td>
                        <td class="text-right text-sm text-primary">{{ number_format($period->bonus_earned, 0, '.', ' ') }} ₴</td>
                        <td class="text-right text-sm">{{ number_format($period->allowances_total, 0, '.', ' ') }} ₴</td>
                        <td class="text-right text-sm">
                            @if($period->bonuses_total > 0)
                                <span class="text-success">+{{ number_format($period->bonuses_total, 0, '.', ' ') }}</span>
                            @endif
                            @if($period->deductions_total > 0)
                                <span class="text-error">-{{ number_format($period->deductions_total, 0, '.', ' ') }}</span>
                            @endif
                            @if(!$period->bonuses_total && !$period->deductions_total)—@endif
                        </td>
                        <td class="text-right font-bold">{{ number_format($period->total_accrued, 0, '.', ' ') }} ₴</td>
                        <td class="text-right text-success">{{ number_format($period->total_paid, 0, '.', ' ') }} ₴</td>
                        <td>
                            @if($period->status === 'draft')
                                <span class="badge badge-warning badge-sm">Чернетка</span>
                            @elseif($period->status === 'approved')
                                <span class="badge badge-info badge-sm">Затверджено</span>
                            @else
                                <span class="badge badge-success badge-sm">Виплачено</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex gap-1">
                                @if($period->status === 'draft')
                                    <button wire:click="approvePeriod({{ $period->id }})"
                                            wire:confirm="Затвердити розрахунок?"
                                            class="btn btn-ghost btn-xs text-info">
                                        Затвердити
                                    </button>
                                @endif
                                @if(in_array($period->status, ['approved']) || $period->total_paid < $period->total_accrued)
                                    <button wire:click="openPayment({{ $period->id }})"
                                            class="btn btn-ghost btn-xs text-success">
                                        Виплатити
                                    </button>
                                @endif
                                <button wire:click="openBonus({{ $period->user_id }}, {{ $period->id }})"
                                        class="btn btn-ghost btn-xs">
                                    + Бонус
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-8 text-base-content/40">
                            Розрахункових періодів за цей місяць немає.
                            <button wire:click="openCalculate()" class="link link-primary ml-1">Розрахувати</button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Співробітники і їх налаштування --}}
    @if($activeTab === 'staff')
    <div class="card bg-base-100 shadow-sm">
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Співробітник</th>
                        <th>Тип ставки</th>
                        <th class="text-right">Ставка</th>
                        <th>Бонус</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($engineers as $user)
                    @php $salary = $user->currentSalarySetting(); @endphp
                    <tr class="hover">
                        <td>
                            <div class="font-medium text-sm">{{ $user->name }}</div>
                            <div class="text-xs text-base-content/60">{{ $user->roles->first()?->name }}</div>
                        </td>
                        <td class="text-sm">
                            {{ $salary?->base_type === 'fixed' ? 'Фіксована' : 'Без ставки' }}
                        </td>
                        <td class="text-right text-sm font-medium">
                            {{ $salary?->base_amount > 0 ? number_format($salary->base_amount, 0, '.', ' ') . ' ₴' : '—' }}
                        </td>
                        <td class="text-sm">
                            @if($salary?->bonus_type !== 'none' && $salary?->bonus_percent > 0)
                                {{ $salary->bonus_percent }}%
                                {{ $salary->bonus_type === 'percent_works' ? 'від робіт' : 'від заявок' }}
                            @else
                                <span class="text-base-content/40">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex gap-1">
                                <button wire:click="openCalculate({{ $user->id }})"
                                        class="btn btn-ghost btn-xs">
                                    Розрахувати
                                </button>
                                <button wire:click="openBonus({{ $user->id }})"
                                        class="btn btn-ghost btn-xs">
                                    + Бонус
                                </button>
                                <button wire:click="openAllowance({{ $user->id }})"
                                        class="btn btn-ghost btn-xs">
                                    + Надбавка
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Надбавки --}}
    @if($activeTab === 'allowances')
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-bold">Постійні надбавки</h2>
                <button wire:click="openAllowance()" class="btn btn-primary btn-sm">
                    + Додати надбавку
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Співробітник</th>
                            <th>Назва</th>
                            <th class="text-right">Сума</th>
                            <th>Тип</th>
                            <th>З дати</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allowances as $allowance)
                        <tr>
                            <td class="text-sm font-medium">{{ $allowance->user->name }}</td>
                            <td class="text-sm">{{ $allowance->name }}</td>
                            <td class="text-right text-sm font-medium">{{ number_format($allowance->amount, 0, '.', ' ') }}</td>
                            <td class="text-sm">{{ $allowance->type === 'percent' ? '%' : '₴' }}</td>
                            <td class="text-sm text-base-content/60">
                                {{ \Carbon\Carbon::parse($allowance->effective_from)->format('d.m.Y') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 text-base-content/40">Надбавок немає</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Бонуси --}}
    @if($activeTab === 'bonuses')
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h2 class="font-bold">Разові бонуси та утримання</h2>
                    <p class="text-xs text-base-content/60 mt-1">Не прив'язані до розрахункового періоду — будуть включені в наступний розрахунок</p>
                </div>
                <button wire:click="openBonus()" class="btn btn-primary btn-sm">
                    + Додати
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Співробітник</th>
                            <th>Опис</th>
                            <th>Тип</th>
                            <th class="text-right">Сума</th>
                            <th>Дата</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($unpaidBonuses as $bonus)
                        <tr>
                            <td class="text-sm font-medium">{{ $bonus->user->name }}</td>
                            <td class="text-sm">{{ $bonus->name }}</td>
                            <td>
                                @if($bonus->type === 'bonus')
                                    <span class="badge badge-success badge-sm">Премія</span>
                                @else
                                    <span class="badge badge-error badge-sm">Утримання</span>
                                @endif
                            </td>
                            <td class="text-right font-medium {{ $bonus->type === 'bonus' ? 'text-success' : 'text-error' }}">
                                {{ $bonus->type === 'bonus' ? '+' : '-' }}{{ number_format($bonus->amount, 0, '.', ' ') }} ₴
                            </td>
                            <td class="text-sm text-base-content/60">{{ $bonus->created_at->format('d.m.Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 text-base-content/40">Неприв'язаних бонусів немає</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Модал розрахунку --}}
    @if($showCalculateForm)
    <div style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.6)">
        <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:440px;margin:16px">
            <h3 style="font-size:18px;font-weight:bold;margin-bottom:16px">Розрахувати зарплату</h3>

            <div style="display:flex;flex-direction:column;gap:12px">
                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Співробітник *</label>
                    <select wire:model="calculateUserId"
                            style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                        <option value="">Оберіть співробітника...</option>
                        @foreach($engineers as $eng)
                            <option value="{{ $eng->id }}">{{ $eng->name }}</option>
                        @endforeach
                    </select>
                    @error('calculateUserId')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                    <div>
                        <label style="font-size:13px;display:block;margin-bottom:4px">З дати *</label>
                        <input wire:model="periodFrom" type="date"
                               style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                    </div>
                    <div>
                        <label style="font-size:13px;display:block;margin-bottom:4px">По дату *</label>
                        <input wire:model="periodTo" type="date"
                               style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                    </div>
                </div>
                @error('periodFrom')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
            </div>

            <div style="display:flex;gap:8px;margin-top:16px">
                <button wire:click="calculateSalary"
                        style="flex:1;padding:10px;background:#6366f1;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:500">
                    Розрахувати
                </button>
                <button wire:click="$set('showCalculateForm', false)"
                        style="padding:10px 16px;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer">
                    Скасувати
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Модал бонусу --}}
    @if($showBonusForm)
    <div style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.6)">
        <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:400px;margin:16px">
            <h3 style="font-size:18px;font-weight:bold;margin-bottom:16px">Бонус / Утримання</h3>

            <div style="display:flex;flex-direction:column;gap:12px">
                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Співробітник *</label>
                    <select wire:model="bonusUserId"
                            style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                        <option value="">Оберіть...</option>
                        @foreach($engineers as $eng)
                            <option value="{{ $eng->id }}">{{ $eng->name }}</option>
                        @endforeach
                    </select>
                    @error('bonusUserId')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Тип</label>
                    <div style="display:flex;gap:16px">
                        <label style="display:flex;align-items:center;gap:6px;cursor:pointer">
                            <input type="radio" wire:model="bonusType" value="bonus"/>
                            <span style="font-size:14px;color:green">Премія</span>
                        </label>
                        <label style="display:flex;align-items:center;gap:6px;cursor:pointer">
                            <input type="radio" wire:model="bonusType" value="deduction"/>
                            <span style="font-size:14px;color:red">Утримання</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Опис *</label>
                    <input wire:model="bonusName" type="text"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"
                           placeholder="Премія за місяць, Аванс..."/>
                    @error('bonusName')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Сума *</label>
                    <input wire:model="bonusAmount" type="number" min="0"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                    @error('bonusAmount')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                </div>
            </div>

            <div style="display:flex;gap:8px;margin-top:16px">
                <button wire:click="saveBonus"
                        style="flex:1;padding:10px;background:#6366f1;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:500">
                    Зберегти
                </button>
                <button wire:click="$set('showBonusForm', false)"
                        style="padding:10px 16px;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer">
                    Скасувати
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Модал надбавки --}}
    @if($showAllowanceForm)
    <div style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.6)">
        <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:400px;margin:16px">
            <h3 style="font-size:18px;font-weight:bold;margin-bottom:16px">Постійна надбавка</h3>

            <div style="display:flex;flex-direction:column;gap:12px">
                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Співробітник *</label>
                    <select wire:model="allowanceUserId"
                            style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                        <option value="">Оберіть...</option>
                        @foreach($engineers as $eng)
                            <option value="{{ $eng->id }}">{{ $eng->name }}</option>
                        @endforeach
                    </select>
                    @error('allowanceUserId')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Назва *</label>
                    <input wire:model="allowanceName" type="text"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"
                           placeholder="Надбавка за шкідливість..."/>
                    @error('allowanceName')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                    <div>
                        <label style="font-size:13px;display:block;margin-bottom:4px">Сума *</label>
                        <input wire:model="allowanceAmount" type="number" min="0"
                               style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                        @error('allowanceAmount')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label style="font-size:13px;display:block;margin-bottom:4px">Тип</label>
                        <select wire:model="allowanceType"
                                style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                            <option value="fixed">Фіксована (₴)</option>
                            <option value="percent">Відсоток (%)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Діє з *</label>
                    <input wire:model="allowanceFrom" type="date"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                </div>
            </div>

            <div style="display:flex;gap:8px;margin-top:16px">
                <button wire:click="saveAllowance"
                        style="flex:1;padding:10px;background:#6366f1;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:500">
                    Зберегти
                </button>
                <button wire:click="$set('showAllowanceForm', false)"
                        style="padding:10px 16px;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer">
                    Скасувати
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Модал виплати --}}
    @if($showPaymentForm)
    <div style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.6)">
        <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:380px;margin:16px">
            <h3 style="font-size:18px;font-weight:bold;margin-bottom:16px">Виплата зарплати</h3>

            <div style="display:flex;flex-direction:column;gap:12px">
                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Сума *</label>
                    <input wire:model="paymentAmount" type="number" min="0"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px;font-size:16px"/>
                    @error('paymentAmount')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Рахунок *</label>
                    <select wire:model="paymentAccountId"
                            style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">
                                {{ $account->name }} ({{ number_format($account->balance, 0, '.', ' ') }} ₴)
                            </option>
                        @endforeach
                    </select>
                    @error('paymentAccountId')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                </div>
            </div>

            <div style="display:flex;gap:8px;margin-top:16px">
                <button wire:click="savePayment"
                        style="flex:1;padding:10px;background:#22c55e;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:500">
                    Виплатити
                </button>
                <button wire:click="$set('showPaymentForm', false)"
                        style="padding:10px 16px;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer">
                    Скасувати
                </button>
            </div>
        </div>
    </div>
    @endif

</div>