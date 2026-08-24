<div>
    {{-- Шапка --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Фінанси</h1>
        <div style="display:flex;gap:8px">
            <button wire:click="openForm('income')"
                    style="padding:8px 16px;background:#16a34a;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:500;font-size:14px">
                + Надходження
            </button>
            <button wire:click="openForm('expense')"
                    style="padding:8px 16px;background:#dc2626;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:500;font-size:14px">
                − Витрата
            </button>
            <button wire:click="openForm('transfer')"
                    style="padding:8px 16px;background:#6366f1;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:500;font-size:14px">
                ⇄ Переказ
            </button>
        </div>
    </div>

    {{-- Рахунки --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:16px">
        @foreach($accounts as $account)
        <div style="background:white;border-radius:12px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,0.1)">
            <p style="font-size:13px;color:#666;margin-bottom:4px">{{ $account->name }}</p>
            <p style="font-size:26px;font-weight:bold;color:{{ $account->balance < 0 ? '#dc2626' : '#000' }}">
                {{ number_format($account->balance, 0, '.', ' ') }} ₴
            </p>
        </div>
        @endforeach
    </div>

    {{-- Загальний баланс --}}
    <div style="background:#6366f1;border-radius:12px;padding:16px;margin-bottom:16px;color:white">
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px">
            <div>
                <p style="font-size:13px;opacity:0.8;margin-bottom:4px">Загальний баланс</p>
                <p style="font-size:28px;font-weight:bold">{{ number_format($totalBalance, 0, '.', ' ') }} ₴</p>
            </div>
            <div>
                <p style="font-size:13px;opacity:0.8;margin-bottom:4px">Надходження за період</p>
                <p style="font-size:24px;font-weight:bold;color:#86efac">+{{ number_format($periodIncome, 0, '.', ' ') }} ₴</p>
            </div>
            <div>
                <p style="font-size:13px;opacity:0.8;margin-bottom:4px">Витрати за період</p>
                <p style="font-size:24px;font-weight:bold;color:#fca5a5">-{{ number_format($periodExpense, 0, '.', ' ') }} ₴</p>
            </div>
        </div>
    </div>

    {{-- Фільтр дат --}}
    <div style="background:white;border-radius:12px;padding:12px 16px;margin-bottom:16px;box-shadow:0 1px 3px rgba(0,0,0,0.1)">
        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center">
            <div style="display:flex;align-items:center;gap:8px">
                <label style="font-size:13px;color:#555">З:</label>
                <input wire:model.live="dateFrom" type="date"
                       style="padding:6px 10px;border:1px solid #ddd;border-radius:8px;font-size:13px"/>
            </div>
            <div style="display:flex;align-items:center;gap:8px">
                <label style="font-size:13px;color:#555">По:</label>
                <input wire:model.live="dateTo" type="date"
                       style="padding:6px 10px;border:1px solid #ddd;border-radius:8px;font-size:13px"/>
            </div>
            <button wire:click="$set('dateFrom', '{{ now()->startOfMonth()->format('Y-m-d') }}'); $set('dateTo', '{{ now()->format('Y-m-d') }}')"
                    style="padding:6px 12px;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer;font-size:13px">
                Цей місяць
            </button>
            <button wire:click="$set('dateFrom', '{{ now()->startOfWeek()->format('Y-m-d') }}'); $set('dateTo', '{{ now()->format('Y-m-d') }}')"
                    style="padding:6px 12px;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer;font-size:13px">
                Цей тиждень
            </button>
            <button wire:click="$set('dateFrom', '{{ now()->format('Y-m-d') }}'); $set('dateTo', '{{ now()->format('Y-m-d') }}')"
                    style="padding:6px 12px;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer;font-size:13px">
                Сьогодні
            </button>
        </div>
    </div>

    {{-- Вкладки --}}
    <div style="display:flex;gap:4px;margin-bottom:16px;border-bottom:2px solid #e5e7eb">
        @foreach(['income' => 'Надходження', 'expense' => 'Витрати', 'transfer' => 'Перекази'] as $tab => $label)
        <button wire:click="$set('activeTab', '{{ $tab }}')"
                style="padding:8px 16px;font-size:14px;font-weight:500;border:none;cursor:pointer;border-radius:8px 8px 0 0;
                       {{ $activeTab === $tab ? 'background:#6366f1;color:white;margin-bottom:-2px' : 'background:transparent;color:#6b7280' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- Таблиця транзакцій --}}
    @php
        $rows = match($activeTab) {
            'income'   => $income,
            'expense'  => $expense,
            default    => $transfer,
        };
        $colorMap = [
            'income'   => '#16a34a',
            'expense'  => '#dc2626',
            'transfer' => '#6366f1',
        ];
        $signMap = ['income' => '+', 'expense' => '−', 'transfer' => '⇄'];
    @endphp

    <div style="background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.1);overflow:hidden">
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb">
                    <th style="padding:10px 16px;text-align:left;font-size:12px;color:#666;font-weight:600">Дата</th>
                    <th style="padding:10px 16px;text-align:left;font-size:12px;color:#666;font-weight:600">Стаття / Опис</th>
                    <th style="padding:10px 16px;text-align:left;font-size:12px;color:#666;font-weight:600">Контрагент</th>
                    <th style="padding:10px 16px;text-align:left;font-size:12px;color:#666;font-weight:600">Заявка</th>
                    <th style="padding:10px 16px;text-align:left;font-size:12px;color:#666;font-weight:600">Рахунок</th>
                    <th style="padding:10px 16px;text-align:right;font-size:12px;color:#666;font-weight:600">Сума</th>
                    <th style="padding:10px 16px;text-align:right;font-size:12px;color:#666;font-weight:600">Баланс</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $tx)
                <tr style="border-bottom:1px solid #f3f4f6;cursor:pointer"
                    wire:click="openTransaction({{ $tx->id }})"
                    onmouseover="this.style.background='#f9fafb'"
                    onmouseout="this.style.background='white'">

                    <td style="padding:10px 16px;font-size:13px;white-space:nowrap">
                        {{ $tx->transaction_date->format('d.m.Y') }}
                    </td>

                    <td style="padding:10px 16px">
                        @if($tx->basis)
                            <div style="font-size:13px;font-weight:500">{{ $tx->basis->name }}</div>
                        @endif
                        @if($tx->description)
                            <div style="font-size:12px;color:#666">{{ $tx->description }}</div>
                        @endif
                    </td>

                    <td style="padding:10px 16px;font-size:13px">
                        {{ $tx->counterparty_label }}
                    </td>

                    <td style="padding:10px 16px">
                        @if($tx->order)
                            <a href="{{ route('orders.show', $tx->order) }}"
                               onclick="event.stopPropagation()"
                               style="font-size:13px;font-family:monospace;color:#6366f1;text-decoration:none;font-weight:500">
                                {{ $tx->order->number }}
                            </a>
                        @else
                            <span style="color:#ccc">—</span>
                        @endif
                    </td>

                    <td style="padding:10px 16px;font-size:13px;color:#666">
                        {{ $tx->account->name }}
                    </td>

                    <td style="padding:10px 16px;text-align:right;font-weight:bold;font-size:14px;color:{{ $colorMap[$tx->type] ?? '#000' }}">
                        {{ $signMap[$tx->type] ?? '' }}{{ number_format($tx->amount, 0, '.', ' ') }} ₴
                    </td>

                    <td style="padding:10px 16px;text-align:right;font-size:12px;color:#999">
                        {{ number_format($tx->balance_after, 0, '.', ' ') }} ₴
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding:40px;text-align:center;color:#999;font-size:14px">
                        Операцій за цей період немає
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($rows->hasPages())
        <div style="padding:12px 16px;border-top:1px solid #e5e7eb">
            {{ $rows->links() }}
        </div>
        @endif
    </div>

    {{-- ════════════════════════════════
         ФОРМА НОВОЇ ТРАНЗАКЦІЇ
    ════════════════════════════════ --}}
    @if($showTransactionForm)
    <div style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;display:flex;align-items:flex-start;justify-content:center;background:rgba(0,0,0,0.6);overflow-y:auto;padding:20px">
        <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:540px;margin:auto">

            {{-- Заголовок --}}
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
                <div>
                    <h3 style="font-size:18px;font-weight:bold">
                        {{ match($formType) {
                            'income'   => '+ Нове надходження',
                            'expense'  => '− Нова витрата',
                            default    => '⇄ Переказ між рахунками',
                        } }}
                    </h3>
                </div>
                <div style="display:flex;gap:4px">
                    @foreach(['income' => '+ Надходження', 'expense' => '− Витрата', 'transfer' => '⇄ Переказ'] as $t => $l)
                    <button wire:click="$set('formType', '{{ $t }}')"
                            style="padding:4px 10px;border:none;border-radius:6px;cursor:pointer;font-size:12px;
                                   {{ $formType === $t ? 'background:#6366f1;color:white' : 'background:#f3f4f6;color:#666' }}">
                        {{ $l }}
                    </button>
                    @endforeach
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:14px">

                {{-- Рахунок --}}
                <div style="display:grid;grid-template-columns:{{ $formType === 'transfer' ? '1fr 1fr' : '1fr 1fr' }};gap:12px">
                    <div>
                        <label style="font-size:13px;display:block;margin-bottom:4px">
                            {{ $formType === 'transfer' ? 'З рахунку *' : 'Рахунок *' }}
                        </label>
                        <select wire:model="formAccountId"
                                style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">
                                    {{ $account->name }} ({{ number_format($account->balance, 0, '.', ' ') }} ₴)
                                </option>
                            @endforeach
                        </select>
                        @error('formAccountId')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                    </div>

                    @if($formType === 'transfer')
                    <div>
                        <label style="font-size:13px;display:block;margin-bottom:4px">На рахунок *</label>
                        <select wire:model="formToAccountId"
                                style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                            <option value="">Оберіть...</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                            @endforeach
                        </select>
                        @error('formToAccountId')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                    </div>
                    @else
                    <div>
                        <label style="font-size:13px;display:block;margin-bottom:4px">Спосіб оплати</label>
                        <select wire:model="formPaymentMethod"
                                style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                            <option value="cash">Готівка</option>
                            <option value="terminal">Термінал</option>
                            <option value="transfer">Переказ</option>
                        </select>
                    </div>
                    @endif
                </div>

                {{-- Сума і дата --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <div>
                        <label style="font-size:13px;display:block;margin-bottom:4px">Сума *</label>
                        <input wire:model="formAmount" type="number" min="0" step="0.01"
                               style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px;font-size:16px"/>
                        @error('formAmount')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label style="font-size:13px;display:block;margin-bottom:4px">Дата *</label>
                        <input wire:model="formDate" type="date"
                               style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                        @error('formDate')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                    </div>
                </div>

                {{-- Стаття руху коштів --}}
                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Стаття руху коштів *</label>
                    <select wire:model="formBasisId"
                            style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                        <option value="">Оберіть статтю...</option>
                        @php
                            $bases = match($formType) {
                                'income'   => $incomeBases,
                                'expense'  => $expenseBases,
                                default    => collect(['Внутрішні платежі' => $transferBases]),
                            };
                        @endphp
                        @foreach($bases as $group => $items)
                            <optgroup label="{{ $group }}">
                                @foreach($items as $basis)
                                    <option value="{{ $basis->id }}">{{ $basis->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('formBasisId')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                </div>

                @if($formType !== 'transfer')

                {{-- Контрагент --}}
                <div>
                    <label style="font-size:13px;display:block;margin-bottom:6px">Контрагент</label>
                    <div style="display:flex;gap:8px;margin-bottom:8px">
                        @foreach([
                            'client'   => 'Клієнт',
                            'supplier' => 'Постачальник',
                            'free'     => 'Довільний',
                            'none'     => 'Без контрагента',
                        ] as $ct => $cl)
                        <button wire:click="$set('counterpartyType', '{{ $ct }}')"
                                style="padding:4px 10px;border:none;border-radius:6px;cursor:pointer;font-size:12px;
                                       {{ $counterpartyType === $ct ? 'background:#6366f1;color:white' : 'background:#f3f4f6;color:#666' }}">
                            {{ $cl }}
                        </button>
                        @endforeach
                    </div>

                    @if($counterpartyType === 'client')
                        @if($formClientId)
                            <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:#f0fdf4;border:1px solid #86efac;border-radius:8px">
                                <span style="font-size:14px;font-weight:500">{{ $clientSearch }}</span>
                                <button wire:click="clearClient"
                                        style="background:none;border:none;cursor:pointer;color:#666">✕</button>
                            </div>
                        @else
                            <div style="position:relative">
                                <input wire:model.live.debounce.300ms="clientSearch"
                                       type="text"
                                       style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"
                                       placeholder="Пошук клієнта за ім'ям або телефоном..."
                                       autocomplete="off"/>
                                @if($showClientDropdown)
                                <div style="position:absolute;z-index:100;width:100%;background:white;border:1px solid #ddd;border-radius:8px;margin-top:4px;box-shadow:0 4px 12px rgba(0,0,0,0.1);max-height:200px;overflow-y:auto">
                                    @forelse(\App\Models\Client::search($clientSearch)->limit(5)->get() as $cl)
                                        <button wire:click="selectClient({{ $cl->id }})"
                                                style="width:100%;text-align:left;padding:8px 12px;border:none;background:none;cursor:pointer;display:flex;justify-content:space-between"
                                                onmouseover="this.style.background='#f5f5f5'"
                                                onmouseout="this.style.background='none'">
                                            <span style="font-weight:500;font-size:13px">{{ $cl->name }}</span>
                                            <span style="color:#666;font-size:12px">{{ $cl->phone }}</span>
                                        </button>
                                    @empty
                                        <div style="padding:8px 12px;color:#666;font-size:13px">Не знайдено</div>
                                    @endforelse
                                </div>
                                @endif
                            </div>
                        @endif

                    @elseif($counterpartyType === 'supplier')
                        <select wire:model="formSupplierId"
                                style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                            <option value="">Оберіть постачальника...</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>

                    @elseif($counterpartyType === 'free')
                        <input wire:model="formCounterpartyName" type="text"
                               style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"
                               placeholder="Введіть назву контрагента..."/>
                    @endif
                </div>

                {{-- Прив'язка до заявки --}}
                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">
                        Прив'язати до заявки (необов'язково)
                    </label>
                    @if($formOrderId)
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px">
                            <span style="font-size:14px;font-weight:500;font-family:monospace">{{ $orderSearch }}</span>
                            <button wire:click="clearOrder"
                                    style="background:none;border:none;cursor:pointer;color:#666">✕</button>
                        </div>
                    @else
                        <div style="position:relative">
                            <input wire:model.live.debounce.300ms="orderSearch"
                                   type="text"
                                   style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"
                                   placeholder="Номер заявки або ім'я клієнта..."
                                   autocomplete="off"/>
                            @if($showOrderDropdown)
                            <div style="position:absolute;z-index:100;width:100%;background:white;border:1px solid #ddd;border-radius:8px;margin-top:4px;box-shadow:0 4px 12px rgba(0,0,0,0.1);max-height:200px;overflow-y:auto">
                                @forelse(\App\Models\Order::search($orderSearch)->with('client')->limit(5)->get() as $ord)
                                    <button wire:click="selectOrder({{ $ord->id }}, '{{ $ord->number }}')"
                                            style="width:100%;text-align:left;padding:8px 12px;border:none;background:none;cursor:pointer;display:flex;justify-content:space-between"
                                            onmouseover="this.style.background='#f5f5f5'"
                                            onmouseout="this.style.background='none'">
                                        <span style="font-weight:500;font-size:13px;font-family:monospace">{{ $ord->number }}</span>
                                        <span style="color:#666;font-size:12px">{{ $ord->client->name }}</span>
                                    </button>
                                @empty
                                    <div style="padding:8px 12px;color:#666;font-size:13px">Не знайдено</div>
                                @endforelse
                            </div>
                            @endif
                        </div>
                    @endif
                </div>

                @endif {{-- кінець if !== transfer --}}

                {{-- Опис --}}
                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Опис (необов'язково)</label>
                    <input wire:model="formDescription" type="text"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"
                           placeholder="Додаткові деталі..."/>
                </div>

            </div>

            {{-- Кнопки --}}
            <div style="display:flex;gap:8px;margin-top:20px">
                <button wire:click="saveTransaction"
                        wire:loading.attr="disabled"
                        style="flex:1;padding:10px;background:{{ match($formType) { 'income' => '#16a34a', 'expense' => '#dc2626', default => '#6366f1' } }};color:white;border:none;border-radius:8px;cursor:pointer;font-weight:500;font-size:15px">
                    <span wire:loading wire:target="saveTransaction">Збереження...</span>
                    <span wire:loading.remove wire:target="saveTransaction">Зберегти</span>
                </button>
                <button wire:click="$set('showTransactionForm', false)"
                        style="padding:10px 16px;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer">
                    Скасувати
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ════════════════════════════════
         КАРТКА ТРАНЗАКЦІЇ
    ════════════════════════════════ --}}
    @if($showTransactionModal && $viewingTransaction)
    <div style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.6)"
         wire:click.self="$set('showTransactionModal', false)">
        <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:480px;margin:16px;max-height:90vh;overflow-y:auto">

            {{-- Шапка --}}
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                <div>
                    <h3 style="font-size:18px;font-weight:bold">
                        {{ $viewingTransaction->type_label }}
                    </h3>
                    <span style="font-size:12px;color:#666">#{{ $viewingTransaction->id }}</span>
                </div>
                <div style="display:flex;gap:8px">
                    @if(!$editingTransaction)
                    <button wire:click="startEdit"
                            style="padding:6px 12px;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer;font-size:13px">
                        ✎ Редагувати
                    </button>
                    @endif
                    <button wire:click="$set('showTransactionModal', false)"
                            style="padding:6px 12px;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer">
                        ✕
                    </button>
                </div>
            </div>

            {{-- Сума --}}
            <div style="text-align:center;padding:16px;background:{{ match($viewingTransaction->type) { 'income' => '#f0fdf4', 'expense' => '#fef2f2', default => '#f5f3ff' } }};border-radius:12px;margin-bottom:16px">
                <p style="font-size:32px;font-weight:bold;color:{{ match($viewingTransaction->type) { 'income' => '#16a34a', 'expense' => '#dc2626', default => '#6366f1' } }}">
                    {{ match($viewingTransaction->type) { 'income' => '+', 'expense' => '−', default => '⇄' } }}{{ number_format($viewingTransaction->amount, 0, '.', ' ') }} ₴
                </p>
                <p style="font-size:12px;color:#666;margin-top:4px">
                    Баланс після: {{ number_format($viewingTransaction->balance_after, 0, '.', ' ') }} ₴
                </p>
            </div>

            @if($editingTransaction)
            {{-- Режим редагування --}}
            <div style="display:flex;flex-direction:column;gap:12px">
                <div>
                    <label style="font-size:13px;color:#555;display:block;margin-bottom:4px">Стаття руху коштів</label>
                    <select wire:model="editBasisId"
                            style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                        <option value="">— Не вказано —</option>
                        @foreach($allBases as $group => $items)
                            <optgroup label="{{ $group }}">
                                @foreach($items as $basis)
                                    <option value="{{ $basis->id }}" {{ $editBasisId == $basis->id ? 'selected' : '' }}>
                                        {{ $basis->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:13px;color:#555;display:block;margin-bottom:4px">Опис</label>
                    <input wire:model="editDescription" type="text"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                </div>
                <div>
                    <label style="font-size:13px;color:#555;display:block;margin-bottom:4px">Дата операції</label>
                    <input wire:model="editDate" type="date"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                    @error('editDate')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                </div>
                <div style="display:flex;gap:8px">
                    <button wire:click="saveEdit"
                            style="flex:1;padding:8px;background:#6366f1;color:white;border:none;border-radius:8px;cursor:pointer">
                        Зберегти
                    </button>
                    <button wire:click="$set('editingTransaction', false)"
                            style="padding:8px 16px;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer">
                        Скасувати
                    </button>
                </div>
            </div>

            @else
            {{-- Режим перегляду --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">

                <div style="background:#f9fafb;padding:10px;border-radius:8px">
                    <p style="font-size:11px;color:#888;margin-bottom:2px">Рахунок</p>
                    <p style="font-size:14px;font-weight:500">{{ $viewingTransaction->account->name }}</p>
                </div>

                <div style="background:#f9fafb;padding:10px;border-radius:8px">
                    <p style="font-size:11px;color:#888;margin-bottom:2px">Спосіб</p>
                    <p style="font-size:14px;font-weight:500">
                        {{ match($viewingTransaction->payment_method) {
                            'cash' => 'Готівка',
                            'terminal' => 'Термінал',
                            'transfer' => 'Переказ',
                            default => '—'
                        } }}
                    </p>
                </div>

                <div style="background:#f9fafb;padding:10px;border-radius:8px">
                    <p style="font-size:11px;color:#888;margin-bottom:2px">Дата операції</p>
                    <p style="font-size:14px;font-weight:500">
                        {{ $viewingTransaction->transaction_date->format('d.m.Y') }}
                    </p>
                </div>

                <div style="background:#f9fafb;padding:10px;border-radius:8px">
                    <p style="font-size:11px;color:#888;margin-bottom:2px">Створено</p>
                    <p style="font-size:13px">{{ $viewingTransaction->created_at->format('d.m.Y H:i') }}</p>
                    @if($viewingTransaction->user)
                    <p style="font-size:12px;color:#666">{{ $viewingTransaction->user->name }}</p>
                    @endif
                </div>

                @if($viewingTransaction->basis)
                <div style="background:#f9fafb;padding:10px;border-radius:8px;grid-column:span 2">
                    <p style="font-size:11px;color:#888;margin-bottom:2px">Стаття руху коштів</p>
                    <p style="font-size:13px;color:#666">{{ $viewingTransaction->basis->group }}</p>
                    <p style="font-size:14px;font-weight:500">{{ $viewingTransaction->basis->name }}</p>
                </div>
                @endif

                @if($viewingTransaction->description)
                <div style="background:#f9fafb;padding:10px;border-radius:8px;grid-column:span 2">
                    <p style="font-size:11px;color:#888;margin-bottom:2px">Опис</p>
                    <p style="font-size:14px">{{ $viewingTransaction->description }}</p>
                </div>
                @endif

                {{-- Контрагент --}}
                @if($viewingTransaction->counterparty_label !== '—')
                <div style="background:#f9fafb;padding:10px;border-radius:8px;grid-column:span 2">
                    <p style="font-size:11px;color:#888;margin-bottom:2px">Контрагент</p>
                    <p style="font-size:14px;font-weight:500">{{ $viewingTransaction->counterparty_label }}</p>
                </div>
                @endif

                {{-- Прив'язана заявка --}}
                @if($viewingTransaction->order)
                <div style="background:#eff6ff;padding:10px;border-radius:8px;border:1px solid #bfdbfe;grid-column:span 2">
                    <p style="font-size:11px;color:#3b82f6;margin-bottom:4px">Заявка</p>
                    <a href="{{ route('orders.show', $viewingTransaction->order) }}"
                       style="font-size:16px;font-weight:bold;color:#1d4ed8;text-decoration:none;font-family:monospace"
                       target="_blank">
                        {{ $viewingTransaction->order->number }} ↗
                    </a>
                    <p style="font-size:12px;color:#555;margin-top:2px">
                        {{ $viewingTransaction->order->client->name }}
                        · {{ $viewingTransaction->order->device->full_name ?? '' }}
                    </p>
                </div>
                @endif

            </div>
            @endif

        </div>
    </div>
    @endif

</div>