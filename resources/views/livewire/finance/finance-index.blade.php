<div>
    {{-- Шапка --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Фінанси</h1>
        <div class="flex gap-2">
            <button wire:click="$set('showTransferForm', true)"
                    class="btn btn-ghost btn-sm gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                Переказ
            </button>
            <button wire:click="$set('showExpenseForm', true)"
                    class="btn btn-error btn-sm gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                </svg>
                Витрата
            </button>
        </div>
    </div>

    {{-- Рахунки --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        @foreach($accounts as $account)
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-base-content/60 text-sm">{{ $account->name }}</p>
                        <p class="text-2xl font-bold mt-1 {{ $account->balance < 0 ? 'text-error' : '' }}">
                            {{ number_format($account->balance, 0, '.', ' ') }} ₴
                        </p>
                    </div>
                    <div class="text-base-content/20">
                        @if($account->type === 'cash')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        @elseif($account->type === 'terminal')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                            </svg>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Загальний баланс і статистика за період --}}
    <div class="card bg-primary text-primary-content shadow-sm mb-6">
        <div class="card-body p-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <p class="text-primary-content/70 text-sm">Загальний баланс</p>
                    <p class="text-3xl font-bold">{{ number_format($totalBalance, 0, '.', ' ') }} ₴</p>
                </div>
                <div>
                    <p class="text-primary-content/70 text-sm">Надходження за період</p>
                    <p class="text-2xl font-bold text-success">+{{ number_format($periodIncome, 0, '.', ' ') }} ₴</p>
                </div>
                <div>
                    <p class="text-primary-content/70 text-sm">Витрати за період</p>
                    <p class="text-2xl font-bold text-error">-{{ number_format($periodExpenses, 0, '.', ' ') }} ₴</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Фільтр дат --}}
    <div class="card bg-base-100 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="flex flex-wrap gap-3 items-center">
                <div class="flex items-center gap-2">
                    <label class="text-sm">З:</label>
                    <input wire:model.live="dateFrom" type="date" class="input input-bordered input-sm"/>
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-sm">По:</label>
                    <input wire:model.live="dateTo" type="date" class="input input-bordered input-sm"/>
                </div>
                <div class="flex gap-2">
                    <button wire:click="$set('dateFrom', '{{ now()->startOfMonth()->format('Y-m-d') }}'); $set('dateTo', '{{ now()->format('Y-m-d') }}')"
                            class="btn btn-ghost btn-xs">Цей місяць</button>
                    <button wire:click="$set('dateFrom', '{{ now()->startOfWeek()->format('Y-m-d') }}'); $set('dateTo', '{{ now()->format('Y-m-d') }}')"
                            class="btn btn-ghost btn-xs">Цей тиждень</button>
                    <button wire:click="$set('dateFrom', '{{ now()->format('Y-m-d') }}'); $set('dateTo', '{{ now()->format('Y-m-d') }}')"
                            class="btn btn-ghost btn-xs">Сьогодні</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Вкладки --}}
    <div class="tabs tabs-bordered mb-4">
        <button wire:click="$set('activeTab', 'transactions')"
                class="tab {{ $activeTab === 'transactions' ? 'tab-active' : '' }}">
            Транзакції
        </button>
        <button wire:click="$set('activeTab', 'expenses')"
                class="tab {{ $activeTab === 'expenses' ? 'tab-active' : '' }}">
            Витрати
            @if($expenses->where('is_paid', false)->count() > 0)
                <span class="badge badge-error badge-sm ml-1">{{ $expenses->where('is_paid', false)->count() }}</span>
            @endif
        </button>
    </div>

    {{-- Транзакції --}}
    @if($activeTab === 'transactions')
    <div class="card bg-base-100 shadow-sm">
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Опис</th>
                        <th>Рахунок</th>
                        <th>Тип</th>
                        <th class="text-right">Сума</th>
                        <th class="text-right">Баланс</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                    <tr class="hover">
                        <td class="text-sm">{{ \Carbon\Carbon::parse($tx->transaction_date)->format('d.m.Y') }}</td>
                        <td class="text-sm">{{ $tx->description ?? '—' }}</td>
                        <td class="text-sm">{{ $tx->account->name }}</td>
                        <td>
                            @if($tx->type === 'income')
                                <span class="badge badge-success badge-sm">Надходження</span>
                            @elseif($tx->type === 'expense')
                                <span class="badge badge-error badge-sm">Витрата</span>
                            @else
                                <span class="badge badge-ghost badge-sm">Переказ</span>
                            @endif
                        </td>
                        <td class="text-right font-medium {{ $tx->type === 'income' ? 'text-success' : ($tx->type === 'expense' ? 'text-error' : '') }}">
                            {{ $tx->type === 'income' ? '+' : '-' }}{{ number_format($tx->amount, 0, '.', ' ') }} ₴
                        </td>
                        <td class="text-right text-sm text-base-content/60">
                            {{ number_format($tx->balance_after, 0, '.', ' ') }} ₴
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-base-content/40">
                            Транзакцій за цей період немає
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
        <div class="p-4 border-t border-base-200">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
    @endif

    {{-- Витрати --}}
    @if($activeTab === 'expenses')
    <div class="card bg-base-100 shadow-sm">
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Опис</th>
                        <th>Категорія</th>
                        <th>Статус</th>
                        <th class="text-right">Сума</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                    <tr class="hover">
                        <td class="text-sm">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d.m.Y') }}</td>
                        <td class="text-sm">{{ $expense->description }}</td>
                        <td class="text-sm text-base-content/60">{{ $expense->category->name }}</td>
                        <td>
                            @if($expense->is_paid)
                                <span class="badge badge-success badge-sm">Оплачено</span>
                            @else
                                <span class="badge badge-warning badge-sm">Не оплачено</span>
                            @endif
                        </td>
                        <td class="text-right font-medium text-error">
                            -{{ number_format($expense->amount, 0, '.', ' ') }} ₴
                        </td>
                        <td>
                            @if(!$expense->is_paid)
                                <button class="btn btn-ghost btn-xs text-success">
                                    Оплатити
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-base-content/40">
                            Витрат за цей період немає
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Форма витрати --}}
    @if($showExpenseForm)
    <div style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.6)">
        <div class="absolute inset-0 bg-black/60" wire:click="$set('showExpenseForm', false)"></div>
        <div class="relative bg-base-100 rounded-2xl shadow-xl p-6 w-full max-w-md mx-4">
            <h3 class="font-bold text-lg mb-4">Нова витрата</h3>

            <div class="flex flex-col gap-3">
                <div class="form-control">
                    <label class="label"><span class="label-text">Опис *</span></label>
                    <input wire:model="expenseDescription" type="text"
                           class="input input-bordered input-sm"
                           placeholder="Оренда офісу, інтернет..."/>
                    @error('expenseDescription')<span class="text-error text-xs">{{ $message }}</span>@enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Сума *</span></label>
                        <input wire:model="expenseAmount" type="number" min="0"
                               class="input input-bordered input-sm"/>
                        @error('expenseAmount')<span class="text-error text-xs">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Дата *</span></label>
                        <input wire:model="expenseDate" type="date"
                               class="input input-bordered input-sm"/>
                    </div>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Категорія *</span></label>
                    <select wire:model="expenseCategoryId" class="select select-bordered select-sm">
                        <option value="">Оберіть категорію...</option>
                        @foreach($expenseCategories as $cat)
                            <optgroup label="{{ $cat->name }}">
                                @foreach($cat->children as $child)
                                    <option value="{{ $child->id }}">{{ $child->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('expenseCategoryId')<span class="text-error text-xs">{{ $message }}</span>@enderror
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Рахунок</span></label>
                    <select wire:model="expenseAccountId" class="select select-bordered select-sm">
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">
                                {{ $account->name }} ({{ number_format($account->balance, 0, '.', ' ') }} ₴)
                            </option>
                        @endforeach
                    </select>
                </div>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input wire:model.live="expenseIsPaid" type="checkbox" class="checkbox checkbox-sm"/>
                    <span class="text-sm">Вже оплачено</span>
                </label>
            </div>

            <div class="flex gap-2 mt-4">
                <button wire:click="saveExpense" class="btn btn-error flex-1">Зберегти витрату</button>
                <button wire:click="$set('showExpenseForm', false)" class="btn btn-ghost">Скасувати</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Форма переказу --}}
    @if($showTransferForm)
    <div style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.6)">
        <div class="absolute inset-0 bg-black/60" wire:click="$set('showTransferForm', false)"></div>
        <div class="relative bg-base-100 rounded-2xl shadow-xl p-6 w-full max-w-md mx-4">
            <h3 class="font-bold text-lg mb-4">Переказ між рахунками</h3>

            <div class="flex flex-col gap-3">
                <div class="form-control">
                    <label class="label"><span class="label-text">З рахунку *</span></label>
                    <select wire:model="transferFromId" class="select select-bordered select-sm">
                        <option value="">Оберіть рахунок...</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">
                                {{ $account->name }} ({{ number_format($account->balance, 0, '.', ' ') }} ₴)
                            </option>
                        @endforeach
                    </select>
                    @error('transferFromId')<span class="text-error text-xs">{{ $message }}</span>@enderror
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">На рахунок *</span></label>
                    <select wire:model="transferToId" class="select select-bordered select-sm">
                        <option value="">Оберіть рахунок...</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                    @error('transferToId')<span class="text-error text-xs">{{ $message }}</span>@enderror
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Сума *</span></label>
                    <input wire:model="transferAmount" type="number" min="0"
                           class="input input-bordered input-sm"/>
                    @error('transferAmount')<span class="text-error text-xs">{{ $message }}</span>@enderror
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Примітка</span></label>
                    <input wire:model="transferNotes" type="text"
                           class="input input-bordered input-sm"
                           placeholder="Необов'язково"/>
                </div>
            </div>

            <div class="flex gap-2 mt-4">
                <button wire:click="saveTransfer" class="btn btn-primary flex-1">Перекласти</button>
                <button wire:click="$set('showTransferForm', false)" class="btn btn-ghost">Скасувати</button>
            </div>
        </div>
    </div>
    @endif

</div>