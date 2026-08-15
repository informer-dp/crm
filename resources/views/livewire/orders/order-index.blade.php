<div>
    {{-- Заголовок і кнопка створення --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Заявки</h1>
            <p class="text-base-content/60 text-sm mt-1">
                Всього: {{ $orders->total() }}
            </p>
        </div>
        <a href="{{ route('orders.create') }}" class="btn btn-primary gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Нова заявка
        </a>
    </div>

    {{-- Фільтри --}}
    <div class="card bg-base-100 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="flex flex-wrap gap-3">

                {{-- Пошук --}}
                <label class="input input-bordered flex items-center gap-2 flex-1 min-w-48">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input wire:model.live.debounce.300ms="search"
                           type="text"
                           placeholder="Номер, клієнт, телефон, пристрій..."
                           class="grow"/>
                </label>
                {{-- Тип пристрою --}}
                <select wire:model.live="deviceType" class="select select-bordered w-44">
                    <option value="">Всі пристрої</option>
                    @foreach(\App\Models\DeviceType::active()->orderBy('name')->get() as $dt)
                        <option value="{{ $dt->id }}">{{ $dt->name }}</option>
                    @endforeach
                </select>

                {{-- Статус --}}
                <select wire:model.live="status" class="select select-bordered w-40">
                    <option value="">Всі статуси</option>
                    <option value="active">Активні</option>
                    <option value="new">Нова</option>
                    <option value="diagnosed">Діагностика</option>
                    <option value="approved">Узгоджено</option>
                    <option value="in_progress">В роботі</option>
                    <option value="waiting_parts">Очікує деталей</option>
                    <option value="ready">Готове</option>
                    <option value="issued">Видане</option>
                    <option value="cancelled">Скасоване</option>
                </select>

                {{-- Тип --}}
                <select wire:model.live="type" class="select select-bordered w-40">
                    <option value="">Всі типи</option>
                    <option value="repair">Ремонт</option>
                    <option value="express">Експрес</option>
                    <option value="diagnostic">Діагностика</option>
                    <option value="maintenance">Обслуговування</option>
                </select>

                {{-- Пріоритет --}}
                <select wire:model.live="priority" class="select select-bordered w-36">
                    <option value="">Всі</option>
                    <option value="normal">Звичайні</option>
                    <option value="urgent">Термінові</option>
                </select>

                {{-- Очистити --}}
                @if($search || $status || $type || $priority || $deviceType)
                    <button wire:click="clearFilters" class="btn btn-ghost btn-square" title="Очистити фільтри">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Таблиця --}}
    <div class="card bg-base-100 shadow-sm">
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>
                            <button wire:click="sort('number')" class="flex items-center gap-1 hover:text-primary">
                                №
                                @if($sortBy === 'number')
                                    <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </button>
                        </th>
                        <th>Клієнт</th>
                        <th>Пристрій</th>
                        <th>Статус</th>
                        <th>Тип</th>
                        <th>
                            <button wire:click="sort('created_at')" class="flex items-center gap-1 hover:text-primary">
                                Дата
                                @if($sortBy === 'created_at')
                                    <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </button>
                        </th>
                        <th>Сума</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr class="hover cursor-pointer {{ $order->isUrgent() ? 'bg-error/5' : '' }}"
                            onclick="window.location='{{ route('orders.show', $order) }}'">

                            <td>
                                <div class="flex items-center gap-2">
                                    @if($order->isUrgent())
                                        <span class="badge badge-error badge-xs"></span>
                                    @endif
                                    <span class="font-mono font-medium text-sm">{{ $order->number }}</span>
                                </div>
                            </td>

                            <td>
                                <div class="font-medium">{{ $order->client->name }}</div>
                                <div class="text-xs text-base-content/60">{{ $order->client->phone }}</div>
                            </td>

                            <td>
                                <div>{{ $order->device->full_name }}</div>
                                @if($order->device->serial_number)
                                    <div class="text-xs text-base-content/60">SN: {{ $order->device->serial_number }}</div>
                                @endif
                            </td>

                            <td>
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
                                <span class="badge {{ $statusColors[$order->status] ?? 'badge-ghost' }} badge-sm">
                                    {{ $order->status_label }}
                                </span>
                            </td>

                            <td>
                                <span class="text-sm">{{ $order->type_label }}</span>
                            </td>

                            <td>
                                <div class="text-sm">{{ $order->created_at->format('d.m.Y') }}</div>
                                <div class="text-xs text-base-content/60">{{ $order->created_at->format('H:i') }}</div>
                            </td>

                            <td>
                                @if($order->estimate)
                                    <span class="font-medium">{{ number_format($order->estimate->total, 0, '.', ' ') }} ₴</span>
                                @else
                                    <span class="text-base-content/40">—</span>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-base-content/40">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                @if($search || $status || $type || $priority)
                                    Заявок не знайдено. <button wire:click="clearFilters" class="link">Очистити фільтри</button>
                                @else
                                    Заявок поки немає
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Пагінація --}}
        @if($orders->hasPages())
            <div class="p-4 border-t border-base-200">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>