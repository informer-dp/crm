<div>
    {{-- Шапка --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('clients.index') }}" class="btn btn-ghost btn-sm gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Назад
        </a>
        <div class="flex-1">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold">{{ $client->name }}</h1>
                @if($client->is_vip)
                    <span class="badge badge-warning">VIP</span>
                @endif
                @if($client->is_blacklisted)
                    <span class="badge badge-error">Чорний список</span>
                @endif
            </div>
            <p class="text-base-content/60 text-sm mt-1">
                {{ $client->type === 'legal' ? 'Юридична особа' : 'Фізична особа' }}
                · Клієнт з {{ $client->created_at->format('d.m.Y') }}
            </p>
        </div>
        @if(!$editing)
            <button wire:click="startEditing" class="btn btn-outline btn-sm">
                Редагувати
            </button>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Ліва колонка — інформація --}}
        <div class="flex flex-col gap-4">

            @if($editing)
            {{-- Форма редагування --}}
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-base">Редагування</h2>

                    <div class="flex gap-3 mb-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" wire:model="type" value="individual" class="radio radio-sm"/>
                            <span class="text-sm">Фізична особа</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" wire:model="type" value="legal" class="radio radio-sm"/>
                            <span class="text-sm">Юридична особа</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Ім'я / Назва *</span></label>
                        <input wire:model="name" type="text" class="input input-bordered input-sm"/>
                        @error('name')<span class="text-error text-xs">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Телефон *</span></label>
                        <input wire:model="phone" type="text" class="input input-bordered input-sm"/>
                        @error('phone')<span class="text-error text-xs">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Email</span></label>
                        <input wire:model="email" type="email" class="input input-bordered input-sm"/>
                        @error('email')<span class="text-error text-xs">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Адреса</span></label>
                        <input wire:model="address" type="text" class="input input-bordered input-sm"/>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Нотатки</span></label>
                        <textarea wire:model="notes" class="textarea textarea-bordered textarea-sm" rows="2"></textarea>
                    </div>

                    <div class="flex flex-col gap-2 mt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input wire:model="isVip" type="checkbox" class="checkbox checkbox-warning checkbox-sm"/>
                            <span class="text-sm">VIP клієнт</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input wire:model.live="isBlacklisted" type="checkbox" class="checkbox checkbox-error checkbox-sm"/>
                            <span class="text-sm">Чорний список</span>
                        </label>
                        @if($isBlacklisted)
                        <div class="form-control">
                            <input wire:model="blacklistReason" type="text"
                                   class="input input-bordered input-sm"
                                   placeholder="Причина..."/>
                        </div>
                        @endif
                    </div>

                    <div class="flex gap-2 mt-2">
                        <button wire:click="save" class="btn btn-primary btn-sm flex-1">Зберегти</button>
                        <button wire:click="cancelEditing" class="btn btn-ghost btn-sm">Скасувати</button>
                    </div>
                </div>
            </div>
            @else
            {{-- Перегляд інформації --}}
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-base">Контакти</h2>
                    <div class="flex flex-col gap-3 text-sm">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-base-content/40 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 7V5z"/>
                            </svg>
                            <a href="tel:{{ $client->phone }}" class="link link-hover">{{ $client->phone }}</a>
                        </div>
                        @if($client->email)
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-base-content/40 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ $client->email }}</span>
                        </div>
                        @endif
                        @if($client->address)
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-base-content/40 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>{{ $client->address }}</span>
                        </div>
                        @endif
                        @if($client->notes)
                        <div class="mt-1">
                            <p class="text-base-content/60 text-xs mb-1">Нотатки</p>
                            <p class="bg-base-200 rounded p-2 text-xs">{{ $client->notes }}</p>
                        </div>
                        @endif
                        @if($client->is_blacklisted && $client->blacklist_reason)
                        <div class="alert alert-error py-2 text-xs">
                            <span>⛔ {{ $client->blacklist_reason }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Статистика --}}
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-base">Статистика</h2>
                    <div class="stats stats-vertical shadow-none">
                        <div class="stat py-2 px-0">
                            <div class="stat-title text-xs">Всього заявок</div>
                            <div class="stat-value text-2xl">{{ $client->orders()->count() }}</div>
                        </div>
                        <div class="stat py-2 px-0">
                            <div class="stat-title text-xs">Активних</div>
                            <div class="stat-value text-2xl text-primary">{{ $client->active_orders_count }}</div>
                        </div>
                        <div class="stat py-2 px-0">
                            <div class="stat-title text-xs">Загальна сума</div>
                            <div class="stat-value text-lg">
                                {{ number_format($client->orders()->join('estimates', 'orders.id', '=', 'estimates.order_id')->sum('estimates.total'), 0, '.', ' ') }} ₴
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- Права колонка — історія заявок --}}
        <div class="lg:col-span-2">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="card-title text-base">Історія заявок</h2>
                        <a href="{{ route('orders.create') }}?client={{ $client->id }}"
                           class="btn btn-primary btn-sm gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Нова заявка
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>№</th>
                                    <th>Пристрій</th>
                                    <th>Статус</th>
                                    <th>Дата</th>
                                    <th>Сума</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr class="hover cursor-pointer"
                                        onclick="window.location='{{ route('orders.show', $order) }}'">
                                        <td class="font-mono text-xs">{{ $order->number }}</td>
                                        <td>{{ $order->device->full_name }}</td>
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
                                            <span class="badge {{ $statusColors[$order->status] ?? 'badge-ghost' }} badge-xs">
                                                {{ $order->status_label }}
                                            </span>
                                        </td>
                                        <td class="text-xs">{{ $order->created_at->format('d.m.Y') }}</td>
                                        <td class="text-xs font-medium">
                                            @if($order->estimate)
                                                {{ number_format($order->estimate->total, 0, '.', ' ') }} ₴
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-8 text-base-content/40">
                                            Заявок поки немає
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($orders->hasPages())
                        <div class="mt-4">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>