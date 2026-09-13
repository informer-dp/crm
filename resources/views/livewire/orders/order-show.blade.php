<div>
    {{-- Шапка --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('orders.index') }}" class="btn btn-ghost btn-sm gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Назад
        </a>
        <div class="flex-1">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold font-mono">{{ $order->number }}</h1>
                @if($order->isUrgent())
                    <span class="badge badge-error">Терміново</span>
                @endif
                @php
                    $statusColors = [
                        'new'           => 'badge-info',
                        'diagnosed'     => 'badge-warning',
                        'approved'      => 'badge-warning',
                        'in_progress'   => 'badge-primary',
                        'waiting_parts' => 'badge-ghost',
                        'ready'         => 'badge-success',
                        'issued'        => 'badge-neutral',
                        'cancelled'     => 'badge-error',
                    ];
                @endphp
                <span class="badge {{ $statusColors[$order->status] ?? 'badge-ghost' }} badge-lg">
                    {{ $order->status_label }}
                </span>
            </div>
            <p class="text-base-content/60 text-sm mt-1">
                {{ $order->type_label }} · Створено {{ $order->created_at->format('d.m.Y H:i') }}
            </p>
        </div>
    <div class="flex gap-2">
        
            
    {{-- Друк квитанції прийому --}}
    <a href="{{ route('orders.print', [$order, 'acceptance']) }}"
       target="_blank"
       class="btn btn-ghost btn-sm gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Квитанція
    </a>
    @if($order->estimate)
    <a href="{{ route('orders.print', [$order, 'final']) }}"
       target="_blank"
       class="btn btn-ghost btn-sm gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Рахунок
    </a>
    @endif
    @if($order->status === 'issued' || $order->status === 'ready')
<a href="{{ route('orders.print.warranty', $order) }}"
   target="_blank"
   class="btn btn-ghost btn-sm gap-1">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
    </svg>
    Гарантія
</a>
@endif
    </div>
        <div class="flex gap-2">
            @if(!$order->isLocked())
                <a href="{{ route('orders.edit', $order) }}" class="btn btn-outline btn-sm">
                    Редагувати
                </a>
            @endif
        </div>
    </div>

    @if($order->status === 'issued' || $order->status === 'cancelled')
    <button wire:click="duplicate"
            wire:confirm="Створити нову заявку на основі цієї?"
            class="btn btn-outline btn-sm gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
        </svg>
        Нова заявка на основі
    </button>
    @endif

   {{-- Кнопки зміни статусу --}}
    @if(!$order->isLocked())
    <div class="flex flex-wrap gap-2 mb-6">
        @foreach(\App\Models\Order::ACTIVE_STATUSES as $status)
            @if($order->canTransitionTo($status))
                @php
                    $btnColors = [
                        'diagnosed'     => 'btn-info',
                        'approved'      => 'btn-warning',
                        'in_progress'   => 'btn-primary',
                        'waiting_parts' => 'btn-ghost',
                        'ready'         => 'btn-success',
                        'issued'        => 'btn-neutral',
                    ];
                    $labels = [
                        'new'           => 'Нова',
                        'diagnosed'     => '→ Діагностика',
                        'approved'      => '→ Узгоджено',
                        'in_progress'   => '→ В роботу',
                        'waiting_parts' => '→ Очікує деталей',
                        'ready'         => '→ Готове',
                        'issued'        => '→ Видати',
                    ];
                @endphp
                <button wire:click="openStatusModal('{{ $status }}')"
                        class="btn btn-sm {{ $btnColors[$status] ?? 'btn-ghost' }}">
                    {{ $labels[$status] ?? $status }}
                </button>
            @endif
        @endforeach
        @if($order->canTransitionTo('issued'))
            <button wire:click="openStatusModal('issued')"
                    class="btn btn-sm btn-neutral">
                → Видати
            </button>
        @endif
        @if($order->canTransitionTo('cancelled'))
            <button wire:click="openStatusModal('cancelled')"
                    class="btn btn-sm btn-error btn-outline">
                Скасувати
            </button>
        @endif
    </div>
    @endif

    {{-- Inline форма зміни статусу --}}
    @if($showStatusModal)
    <div class="card bg-base-100 shadow-sm mb-4 border-2 border-primary">
        <div class="card-body py-3">
            <div class="flex items-center gap-3">
                <div class="flex-1">
                    <textarea wire:model="statusComment"
                              class="textarea textarea-bordered textarea-sm w-full"
                              rows="1"
                              placeholder="Коментар до зміни статусу (необов'язково)..."></textarea>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    <button wire:click="changeStatus" class="btn btn-primary btn-sm">
                        Підтвердити
                    </button>
                    <button wire:click="$set('showStatusModal', false)" class="btn btn-ghost btn-sm">
                        ✕
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Ліва колонка --}}
        <div class="lg:col-span-2 flex flex-col gap-4">

            {{-- Пристрій --}}
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-base font-bold text-primary">Пристрій</h2>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-base-content/60">Тип</p>
                            <p class="font-medium">{{ $order->device->deviceType->name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-base-content/60">Модель</p>
                            <p class="font-medium">{{ $order->device->full_name }}</p>
                        </div>
                        @if($order->device->serial_number)
                        <div>
                            <p class="text-base-content/60">Серійний номер</p>
                            <p class="font-medium font-mono">{{ $order->device->serial_number }}</p>
                        </div>
                        @endif
                        @if($order->device->imei)
                        <div>
                            <p class="text-base-content/60">IMEI</p>
                            <p class="font-medium font-mono">{{ $order->device->imei }}</p>
                        </div>
                        @endif
                        @if($order->device->appearance)
                        <div class="col-span-2">
                            <p class="text-base-content/60">Зовнішній вигляд</p>
                            <p>{{ $order->device->appearance }}</p>
                        </div>
                        @endif
                        {{-- Комплектація --}}
                        @if($order->device->equipment && count($order->device->equipment) > 0)
                        @php
                            $equipmentLabels = [
                                'charger'     => 'Зарядний пристрій',
                                'cable'       => 'Кабель',
                                'case'        => 'Чохол',
                                'glass'       => 'Захисне скло',
                                'sim'         => 'SIM-карта',
                                'memory_card' => 'Карта пам\'яті',
                                'bag'         => 'Сумка',
                                'other'       => 'Інше',
                            ];
                        @endphp
                        <div class="col-span-2">
                            <p class="text-base-content/60">Комплектація</p>
                            <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:4px">
                                @foreach($order->device->equipment as $item)
                                <span style="padding:3px 10px;background:#eff6ff;border:1px solid #c7d2fe;border-radius:9999px;font-size:12px;color:#4338ca">
                                    ✓ {{ $equipmentLabels[$item] ?? $item }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Несправність і діагноз --}}
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-base font-bold text-primary">Несправність</h2>
                    <div class="flex flex-col gap-3 text-sm">
                        <div>
                            <p class="text-base-content/60 mb-1">Скарга клієнта</p>
                            <p class="bg-base-200 rounded-lg p-3">{{ $order->malfunction }}</p>
                        </div>
                        @if($order->diagnosis)
                        <div>
                            <p class="text-base-content/60 mb-1">Висновок інженера</p>
                            <p class="bg-base-200 rounded-lg p-3">{{ $order->diagnosis }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Кошторис --}}
<div class="card bg-base-100 shadow-sm">
    <div class="card-body">
        <div class="flex items-center justify-between mb-2">
            <h2 class="card-title text-base font-bold text-primary">Кошторис</h2>
            @if(!$order->isLocked())
                <button wire:click="initEstimate" class="btn btn-ghost btn-xs gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ $order->estimate ? 'Редагувати' : 'Створити кошторис' }}
                </button>
            @endif
        </div>

        @if($order->estimate)

            {{-- Роботи --}}
            @if($order->estimate->works->count())
            <div class="mb-3">
                <p class="text-xs font-medium text-base-content/60 mb-2 uppercase tracking-wide">Роботи</p>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Назва</th>
                            <th class="text-right w-24">Ціна</th>
                            <th class="text-right w-16">К-сть</th>
                            <th class="text-right w-24">Сума</th>
                            @if(!$order->isLocked())<th class="w-8"></th>@endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->estimate->works as $work)
                        <tr>
                            <td>
                                <span>{{ $work->name }}</span>
                                @if($work->is_warranty)
                                    <span class="badge badge-ghost badge-xs ml-1">гарантія</span>
                                @endif
                                @if($work->work_type === 'subcontract')
                                    <span class="badge badge-warning badge-xs ml-1">підряд</span>
                                @endif
                            </td>
                            <td class="text-right">{{ number_format($work->price, 0, '.', ' ') }} ₴</td>
                            <td class="text-right">{{ $work->quantity }}</td>
                            <td class="text-right font-medium">{{ number_format($work->total, 0, '.', ' ') }} ₴</td>
                            @if(!$order->isLocked())
                            <td>
                                <button wire:click="removeWork({{ $work->id }})"
                                        wire:confirm="Видалити цей рядок?"
                                        class="btn btn-ghost btn-xs text-error">✕</button>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            {{-- Запчастини --}}
            @if($order->estimate->parts->count())
            <div class="mb-3">
                <p class="text-xs font-medium text-base-content/60 mb-2 uppercase tracking-wide">Запчастини</p>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Назва</th>
                            <th class="text-right w-24">Ціна</th>
                            <th class="text-right w-16">К-сть</th>
                            <th class="text-right w-24">Сума</th>
                            @if(!$order->isLocked())<th class="w-8"></th>@endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->estimate->parts as $part)
                        <tr>
                            <td>
                                <span>{{ $part->name }}</span>
                                @if($part->is_own_part)
                                    <span class="badge badge-ghost badge-xs ml-1">клієнта</span>
                                @endif
                            </td>
                            <td class="text-right">{{ number_format($part->price, 0, '.', ' ') }} ₴</td>
                            <td class="text-right">{{ $part->quantity }}</td>
                            <td class="text-right font-medium">{{ number_format($part->total, 0, '.', ' ') }} ₴</td>
                            @if(!$order->isLocked())
                            <td>
                                <button wire:click="removePart({{ $part->id }})"
                                        wire:confirm="Видалити цей рядок?"
                                        class="btn btn-ghost btn-xs text-error">✕</button>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            {{-- Підсумок --}}
            <div class="divider my-1"></div>
            <div class="flex flex-col gap-1 text-sm">
                <div class="flex justify-between">
                    <span class="text-base-content/60">Роботи:</span>
                    <span>{{ number_format($order->estimate->works_total, 0, '.', ' ') }} ₴</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-base-content/60">Запчастини:</span>
                    <span>{{ number_format($order->estimate->parts_total, 0, '.', ' ') }} ₴</span>
                </div>
                @if(!$order->isLocked())
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-base-content/60 text-sm">Знижка:</span>
                    <input type="number" min="0"
                           value="{{ $order->estimate->discount }}"
                           wire:change="updateDiscount($event.target.value, '{{ $order->estimate->discount_type }}')"
                           class="input input-bordered input-xs w-24"/>
                    <select wire:change="updateDiscount('{{ $order->estimate->discount }}', $event.target.value)"
                            class="select select-bordered select-xs w-20">
                        <option value="fixed" {{ $order->estimate->discount_type === 'fixed' ? 'selected' : '' }}>₴</option>
                        <option value="percent" {{ $order->estimate->discount_type === 'percent' ? 'selected' : '' }}>%</option>
                    </select>
                </div>
                @elseif($order->estimate->discount > 0)
                <div class="flex justify-between text-error">
                    <span>Знижка:</span>
                    <span>-{{ number_format($order->estimate->discount, 0, '.', ' ') }}
                        {{ $order->estimate->discount_type === 'percent' ? '%' : '₴' }}
                    </span>
                </div>
                @endif
                <div class="flex justify-between font-bold text-base mt-1">
                    <span>Разом:</span>
                    <span>{{ number_format($order->estimate->total, 0, '.', ' ') }} ₴</span>
                </div>
            </div>

            {{-- Форма додавання рядків --}}
            {{-- Модал додавання роботи --}}
        @if($showEstimateForm && !$order->isLocked())
        <div style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.6)">
            <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:480px;margin:16px">
                <h3 style="font-size:18px;font-weight:bold;margin-bottom:16px">Додати роботу або запчастину</h3>

                {{-- Перемикач --}}
                <div style="display:flex;gap:4px;background:#f3f4f6;padding:4px;border-radius:8px;margin-bottom:16px">
                    <button wire:click="$set('estimateTab', 'work')"
                            style="flex:1;padding:6px;border:none;border-radius:6px;cursor:pointer;font-size:13px;
                                {{ ($estimateTab ?? 'work') === 'work' ? 'background:white;box-shadow:0 1px 2px rgba(0,0,0,0.1);font-weight:500' : 'background:transparent;color:#666' }}">
                        🔧 Робота
                    </button>
                    <button wire:click="$set('estimateTab', 'part')"
                            style="flex:1;padding:6px;border:none;border-radius:6px;cursor:pointer;font-size:13px;
                                {{ ($estimateTab ?? 'work') === 'part' ? 'background:white;box-shadow:0 1px 2px rgba(0,0,0,0.1);font-weight:500' : 'background:transparent;color:#666' }}">
                        🔩 Запчастина
                    </button>
                </div>

                @if(($estimateTab ?? 'work') === 'work')
                {{-- Форма роботи --}}
                <div style="display:flex;flex-direction:column;gap:12px">
                    <div>
                        <label style="font-size:13px;display:block;margin-bottom:4px">Назва роботи *</label>
                        <input wire:model="workName" type="text"
                            style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"
                            placeholder="Заміна дисплею, діагностика..."/>
                        @error('workName')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px">
                        <div>
                            <label style="font-size:13px;display:block;margin-bottom:4px">Ціна *</label>
                            <input wire:model="workPrice" type="number" min="0"
                                style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                            @error('workPrice')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label style="font-size:13px;display:block;margin-bottom:4px">К-сть</label>
                            <input wire:model="workQuantity" type="number" min="1"
                                style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                        </div>
                        <div>
                            <label style="font-size:13px;display:block;margin-bottom:4px">Інженер</label>
                            <select wire:model="workEngineerId"
                                    style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                                <option value="">—</option>
                                @foreach($engineers as $eng)
                                    <option value="{{ $eng->id }}">{{ $eng->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                        <input wire:model="workIsWarranty" type="checkbox" style="width:16px;height:16px"/>
                        <span style="font-size:14px">Гарантійна робота (безкоштовно)</span>
                    </label>
                </div>
                <div style="display:flex;gap:8px;margin-top:16px">
                    <button wire:click="addWork"
                            style="flex:1;padding:10px;background:#6366f1;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:500">
                        Додати роботу
                    </button>
                    <button wire:click="$set('showEstimateForm', false)"
                            style="padding:10px 16px;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer">
                        Закрити
                    </button>
                </div>

                @else
                {{-- Форма запчастини --}}
                <div style="display:flex;flex-direction:column;gap:12px">
                    <div>
                        <label style="font-size:13px;display:block;margin-bottom:4px">Назва запчастини *</label>
                        <input wire:model="partName" type="text"
                            style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"
                            placeholder="Акумулятор, дисплей..."/>
                        @error('partName')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px">
                        <div>
                            <label style="font-size:13px;display:block;margin-bottom:4px">Ціна клієнта *</label>
                            <input wire:model="partPrice" type="number" min="0"
                                style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                            @error('partPrice')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label style="font-size:13px;display:block;margin-bottom:4px">Собівартість</label>
                            <input wire:model="partCost" type="number" min="0"
                                style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                        </div>
                        <div>
                            <label style="font-size:13px;display:block;margin-bottom:4px">К-сть</label>
                            <input wire:model="partQuantity" type="number" min="1"
                                style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                        </div>
                    </div>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                        <input wire:model="partIsOwn" type="checkbox" style="width:16px;height:16px"/>
                        <span style="font-size:14px">Запчастина клієнта</span>
                    </label>
                </div>
                <div style="display:flex;gap:8px;margin-top:16px">
                    <button wire:click="addPart"
                            style="flex:1;padding:10px;background:#6366f1;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:500">
                        Додати запчастину
                    </button>
                    <button wire:click="$set('showEstimateForm', false)"
                            style="padding:10px 16px;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer">
                        Закрити
                    </button>
                </div>
                @endif

            </div>
        </div>
        @endif

        @else
            <p class="text-base-content/40 text-sm">Кошторис ще не створено</p>
        @endif
    </div>
</div>
            {{-- Витрати по заявці --}}
            @php $orderExpenses = $this->getOrderExpenses(); @endphp
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="card-title text-base font-bold text-primary">Витрати по заявці</h2>
                        <span class="text-sm text-base-content/60">
                            {{ $orderExpenses->count() }} позицій
                        </span>
                    </div>

                    @if($orderExpenses->count())
                    <div class="overflow-x-auto">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Дата</th>
                                    <th>Опис</th>
                                    <th>Стаття</th>
                                    <th>Контрагент</th>
                                    <th>Статус</th>
                                    <th class="text-right">Сума</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orderExpenses as $expense)
                                <tr>
                                    <td class="text-xs">{{ \Carbon\Carbon::parse($expense->transaction_date)->format('d.m.Y') }}</td>
                                    <td class="text-sm">{{ $expense->description }}</td>
                                    <td class="text-xs text-base-content/60">{{ $expense->basis?->name ?? '—' }}</td>
                                    <td class="text-xs text-base-content/60">{{ $expense->supplier?->name ?? '—' }}</td>
                                    <td>
                                        <span class="badge badge-success badge-xs">Оплачено</span>
                                    </td>
                                    <td class="text-right font-medium text-error text-sm">
                                        -{{ number_format($expense->amount, 0, '.', ' ') }} ₴
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-right font-bold text-sm">Всього витрат:</td>
                                    <td class="text-right font-bold text-error">
                                        -{{ number_format($orderExpenses->sum('amount'), 0, '.', ' ') }} ₴
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                        <p class="text-sm text-base-content/40">Витрат по цій заявці немає</p>
                    @endif
                </div>
            </div>
            {{-- Коментарі --}}
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-base font-bold text-primary">Коментарі</h2>
                    @forelse($order->comments as $comment)
                    <div class="flex gap-3 text-sm {{ $comment->is_internal ? 'opacity-70' : '' }}">
                        <div class="avatar placeholder flex-shrink-0">
                            <div class="bg-neutral text-neutral-content rounded-full w-8">
                                <span class="text-xs">{{ substr($comment->user?->name ?? '?', 0, 1) }}</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="font-medium">{{ $comment->user?->name ?? 'Система' }}</span>
                                @if($comment->is_internal)
                                    <span class="badge badge-ghost badge-xs">внутрішній</span>
                                @endif
                                <span class="text-base-content/40 text-xs">{{ $comment->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                            <p class="mt-1">{{ $comment->body }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-base-content/40 text-sm">Коментарів немає</p>
                    @endforelse
                    {{-- Форма додавання коментаря --}}
            <div class="mt-4 pt-4 border-t border-base-200">
                <div class="form-control mb-2">
                    <textarea wire:model="newComment"
                            class="textarea textarea-bordered textarea-sm"
                            rows="2"
                            placeholder="Додати коментар..."></textarea>
                    @error('newComment')<span class="text-error text-xs">{{ $message }}</span>@enderror
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="commentIsInternal" type="checkbox" class="checkbox checkbox-sm"/>
                        <span class="text-xs">Тільки для персоналу</span>
                    </label>
                    <button wire:click="addComment" class="btn btn-primary btn-sm">
                        Додати
                    </button>
                </div>
            </div>
                </div>
            </div>
        </div>

        {{-- Права колонка --}}
        <div class="flex flex-col gap-4">

            {{-- Клієнт --}}
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-base font-bold text-primary">Клієнт</h2>
                    <div class="flex flex-col gap-2 text-sm">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#999999">
                                <path d="M367-527q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Zm80-80h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm296.5-343.5Q560-607 560-640t-23.5-56.5Q513-720 480-720t-56.5 23.5Q400-673 400-640t23.5 56.5Q447-560 480-560t56.5-23.5ZM480-640Zm0 400Z"/>
                            </svg>
                            <a href="{{ route('clients.show', $order->client) }}"
                               class="font-bold link link-hover ">
                                {{ $order->client->name }}
                            </a>
                            @if($order->client->is_vip)
                                <span class="badge badge-warning badge-xs ml-1">VIP</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-base-content/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 7V5z"/>
                            </svg>
                            <a href="tel:{{ $order->client->phone }}" class="link link-hover">{{ $order->client->phone }}</a>
                        </div>
                        @if($order->client->email)
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-base-content/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ $order->client->email }}</span>
                        </div>
                        @endif
                        <div class="mt-1">
                            <p class="text-base-content/60 text-xs">Всього заявок: {{ $order->client->orders()->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Деталі заявки --}}
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-base font-bold text-primary">Деталі</h2>
                    <div class="flex flex-col gap-2 text-sm">
                        @if($order->manager)
                        <div class="flex justify-between">
                            <span class="text-base-content/60">Прийняв:</span>
                            <span>{{ $order->manager->name }}</span>
                        </div>
                        @endif
                        @if($order->engineers->count())
                        <div class="flex justify-between">
                            <span class="text-base-content/60">Інженер:</span>
                            <span>{{ $order->engineers->first()->name }}</span>
                        </div>
                        @endif
                        @if($order->estimated_date)
                        <div class="flex justify-between">
                            <span class="text-base-content/60">Очікувана дата:</span>
                            <span class="{{ $order->estimated_date->isPast() && !$order->isLocked() ? 'text-error font-medium' : '' }}">
                                {{ $order->estimated_date->format('d.m.Y') }}
                            </span>
                        </div>
                        @endif
                        @if($order->prepayment > 0)
                        <div class="flex justify-between">
                            <span class="text-base-content/60">Передоплата:</span>
                            <span class="font-medium">{{ number_format($order->prepayment, 0, '.', ' ') }} ₴</span>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-base-content/60">Код перевірки:</span>
                            <span class="font-mono">{{ $order->check_code }}</span>
                        </div>
                    </div>
                </div>
            </div>

           {{-- Оплата --}}
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body ">
                
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-bold text-primary ">Оплата</h2>
                    <div class="flex gap-1">
                        <button wire:click="$set('showExpenseForm', true)"
                                class="btn btn-error btn-sm gap-1">
                            − Витрата
                        </button>
                        @if(!$order->isLocked())
                        <button wire:click="$set('showPaymentForm', true)"
                                class="btn btn-success btn-sm gap-1">
                            + Прийняти
                        </button>
                        @endif
                    </div>
                </div>

                @if($order->estimate)
                <div class="flex flex-col gap-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-base-content/60">Сума:</span>
                        <span class="font-bold">{{ number_format($order->estimate->total, 0, '.', ' ') }} ₴</span>
                    </div>
                    @if($order->prepayment > 0)
                    <div class="flex justify-between">
                        <span class="text-base-content/60">Передоплата:</span>
                        <span class="text-success">{{ number_format($order->prepayment, 0, '.', ' ') }} ₴</span>
                    </div>
                    @endif
                    @if($order->paid_amount > 0)
                    <div class="flex justify-between">
                        <span class="text-base-content/60">Оплачено:</span>
                        <span class="text-success font-medium">{{ number_format($order->paid_amount, 0, '.', ' ') }} ₴</span>
                    </div>
                    @endif
                    <div class="divider my-0"></div>
                    <div class="flex justify-between font-bold {{ $order->remaining_amount > 0 ? 'text-error' : 'text-success' }}">
                        <span>{{ $order->remaining_amount > 0 ? 'Залишок:' : 'Сплачено повністю' }}</span>
                        @if($order->remaining_amount > 0)
                        <span>{{ number_format($order->remaining_amount, 0, '.', ' ') }} ₴</span>
                        @endif
                    </div>
                </div>
                @else
                    <p class="text-sm text-base-content/40">Кошторис не створено</p>
                @endif

                {{-- Список оплат --}}
                @if($order->payments->count())
                <div class="mt-3 pt-3 border-t border-base-200 flex flex-col gap-2">
                    @foreach($order->payments as $payment)
                    <div class="flex justify-between text-xs">
                        <div class="flex items-center gap-1">
                            <span class="badge badge-ghost badge-xs">
                                {{ match($payment->type) {
                                    'prepayment' => 'Аванс',
                                    'final' => 'Оплата',
                                    'refund' => 'Повернення',
                                    default => $payment->type
                                } }}
                            </span>
                            <span class="text-base-content/40">{{ $payment->created_at->format('d.m H:i') }}</span>
                        </div>
                        <span class="font-medium {{ $payment->type === 'refund' ? 'text-error' : 'text-success' }}">
                            {{ $payment->type === 'refund' ? '-' : '+' }}{{ number_format($payment->amount, 0, '.', ' ') }} ₴
                        </span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
            {{-- Призначення інженера --}}
@if(!$order->isLocked())
<div class="card bg-base-100 shadow-sm">
    <div class="card-body">
        <div class="flex items-center justify-between">
            <h2 class="card-title text-base">Інженер</h2>
            <button wire:click="openEngineerModal" class="btn btn-ghost btn-xs">
                {{ $order->engineers->count() ? 'Змінити' : 'Призначити' }}
            </button>
        </div>
        @if($order->engineers->count())
            <div class="flex items-center gap-2">
                <div class="avatar placeholder">
                    <div class="bg-primary text-primary-content rounded-full w-8">
                        <span class="text-xs">{{ substr($order->engineers->first()->name, 0, 1) }}</span>
                    </div>
                </div>
                <span class="text-sm font-medium">{{ $order->engineers->first()->name }}</span>
            </div>
        @else
            <p class="text-base-content/40 text-sm">Не призначено</p>
        @endif
    </div>
</div>
@endif

            {{-- Історія статусів --}}
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-base font-bold text-primary">Історія</h2>
                    <ol class="relative border-l border-base-300 ml-2">
                        @foreach($order->statusHistory as $history)
                        <li class="mb-4 ml-4">
                            <div class="absolute w-2 h-2 bg-base-300 rounded-full mt-1.5 -left-1"></div>
                            <div class="text-xs text-base-content/40">{{ $history->created_at->format('d.m.Y H:i') }}</div>
                            <div class="text-sm font-medium">{{ $history->status_to_label }}</div>
                            @if($history->comment)
                            <div class="text-xs text-base-content/60">{{ $history->comment }}</div>
                            @endif
                        </li>
                        @endforeach
                    </ol>
                </div>
            </div>

        </div>
    </div>
   {{-- Модал зміни статусу --}}

@if($showStatusModal)
<div style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.6)">
    <div class="absolute inset-0 bg-black/60" wire:click="$set('showStatusModal', false)"></div>
    <div class="relative bg-base-100 rounded-2xl shadow-xl p-6 w-full max-w-md mx-4">
        <h3 class="font-bold text-lg mb-4">Змінити статус</h3>
        <div class="form-control">
            <label class="label"><span class="label-text">Коментар (необов'язково)</span></label>
            <textarea wire:model="statusComment"
                      class="textarea textarea-bordered"
                      rows="2"
                      placeholder="Додайте коментар..."></textarea>
        </div>
        <div class="flex gap-2 mt-4">
            <button wire:click="changeStatus" class="btn btn-primary flex-1">Підтвердити</button>
            <button wire:click="$set('showStatusModal', false)" class="btn btn-ghost">Скасувати</button>
        </div>
    </div>
</div>
@endif


{{-- Модал призначення інженера --}}

@if($showEngineerModal)
<div style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.6)">
    <div class="absolute inset-0 bg-black/60" wire:click="$set('showEngineerModal', false)"></div>
    <div class="relative bg-base-100 rounded-2xl shadow-xl p-6 w-full max-w-md mx-4">
        <h3 class="font-bold text-lg mb-4">Призначити інженера</h3>
        <div class="form-control">
            <select wire:model="engineerId" class="select select-bordered w-full">
                <option value="">Оберіть інженера...</option>
                @foreach($engineers as $engineer)
                    <option value="{{ $engineer->id }}">{{ $engineer->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2 mt-4">
            <button wire:click="assignEngineer" class="btn btn-primary flex-1">Призначити</button>
            <button wire:click="$set('showEngineerModal', false)" class="btn btn-ghost">Скасувати</button>
        </div>
    </div>
</div>
@endif

{{-- Форма оплати --}}
@if($showPaymentForm)
<div style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.6)">
    <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:400px;margin:16px">
        <h3 style="font-size:18px;font-weight:bold;margin-bottom:16px">Прийняти оплату</h3>
        <p style="font-size:14px;color:#666;margin-bottom:16px">
            Заявка {{ $order->number }}
            @if($order->estimate)
                · Залишок: <strong style="color:red">{{ number_format($order->remaining_amount, 0, '.', ' ') }} ₴</strong>
            @endif
        </p>

        <div style="display:flex;flex-direction:column;gap:12px">
            <div>
                <label style="font-size:14px;display:block;margin-bottom:4px">Сума *</label>
                <input wire:model="paymentAmount" type="number" min="0"
                       style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px;font-size:16px"
                       placeholder="{{ $order->remaining_amount }}"/>
                @error('paymentAmount')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
            </div>

            <div>
                <label style="font-size:14px;display:block;margin-bottom:4px">Тип оплати</label>
                <select wire:model="paymentType"
                        style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                    <option value="prepayment">Передоплата</option>
                    <option value="final">Фінальна оплата</option>
                    <option value="refund">Повернення</option>
                </select>
            </div>

            <div>
                <label style="font-size:14px;display:block;margin-bottom:8px">Спосіб оплати</label>
                <div style="display:flex;gap:16px">
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer">
                        <input type="radio" wire:model="paymentMethod" value="cash"/>
                        <span style="font-size:14px">Готівка</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer">
                        <input type="radio" wire:model="paymentMethod" value="terminal"/>
                        <span style="font-size:14px">Термінал</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer">
                        <input type="radio" wire:model="paymentMethod" value="transfer"/>
                        <span style="font-size:14px">Переказ</span>
                    </label>
                </div>
            </div>

            <div>
                <label style="font-size:14px;display:block;margin-bottom:4px">Рахунок</label>
                <select wire:model="paymentAccountId"
                        style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                    @foreach(\App\Models\Account::active()->get() as $account)
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
                    style="flex:1;padding:10px;background:#22c55e;color:white;border:none;border-radius:8px;font-size:15px;cursor:pointer;font-weight:500">
                Зберегти оплату
            </button>
            <button wire:click="$set('showPaymentForm', false)"
                    style="padding:10px 16px;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer">
                Скасувати
            </button>
        </div>
    </div>
</div>
@endif

    {{-- Форма витрати по заявці --}}
        @if($showExpenseForm)
        <div style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.6)">
            <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:480px;margin:16px">
                <h3 style="font-size:18px;font-weight:bold;margin-bottom:4px">Витрата по заявці</h3>
                <p style="font-size:13px;color:#666;margin-bottom:16px">{{ $order->number }}</p>

                <div style="display:flex;flex-direction:column;gap:12px">
                    <div>
                        <label style="font-size:13px;display:block;margin-bottom:4px">Опис *</label>
                        <input wire:model="expenseDescription" type="text"
                            style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"
                            placeholder="Закупівля запчастини, доставка, підряд..."/>
                        @error('expenseDescription')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                        <div>
                            <label style="font-size:13px;display:block;margin-bottom:4px">Сума *</label>
                            <input wire:model="expenseAmount" type="number" min="0"
                                style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                            @error('expenseAmount')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label style="font-size:13px;display:block;margin-bottom:4px">Дата *</label>
                            <input wire:model="expenseDate" type="date"
                                style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                        </div>
                    </div>

                    <div>
                        <label style="font-size:13px;display:block;margin-bottom:4px">Категорія *</label>
                        <select wire:model="expenseCategoryId"
                                style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                            <option value="">Оберіть категорію...</option>
                            @foreach($expenseCategories as $cat)
                                <optgroup label="{{ $cat->name }}">
                                    @foreach($cat->children as $child)
                                        <option value="{{ $child->id }}">{{ $child->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('expenseCategoryId')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label style="font-size:13px;display:block;margin-bottom:4px">Контрагент</label>
                        <select wire:model="expenseSupplierId"
                                style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                            <option value="">— Не вказано —</option>
                            @foreach(\App\Models\Supplier::active()->orderBy('name')->get() as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="font-size:13px;display:block;margin-bottom:4px">Рахунок</label>
                        <select wire:model="expenseAccountId"
                                style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                            @foreach(\App\Models\Account::active()->get() as $account)
                                <option value="{{ $account->id }}">
                                    {{ $account->name }} ({{ number_format($account->balance, 0, '.', ' ') }} ₴)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                        <input wire:model="expenseIsPaid" type="checkbox" style="width:16px;height:16px"/>
                        <span style="font-size:14px">Вже оплачено</span>
                    </label>
                </div>

                <div style="display:flex;gap:8px;margin-top:16px">
                    <button wire:click="saveExpense"
                            style="flex:1;padding:10px;background:#ef4444;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:500">
                        Зберегти витрату
                    </button>
                    <button wire:click="$set('showExpenseForm', false)"
                            style="padding:10px 16px;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer">
                        Скасувати
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- Модал помилки --}}
        @if($showErrorModal)
        <div style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.6)">
            <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:380px;margin:16px;text-align:center">
                <div style="font-size:48px;margin-bottom:12px">⚠️</div>
                <h3 style="font-size:18px;font-weight:bold;margin-bottom:8px;color:#dc2626">Неможливо змінити статус</h3>
                <p style="font-size:14px;color:#666;margin-bottom:20px">{{ $errorMessage }}</p>
                <button wire:click="$set('showErrorModal', false)"
                        style="padding:10px 24px;background:#6366f1;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:500">
                    Зрозуміло
                </button>
            </div>
        </div>
        @endif
</div>