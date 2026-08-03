<div>
    {{-- Заголовок --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Клієнти</h1>
            <p class="text-base-content/60 text-sm mt-1">Всього: {{ $clients->total() }}</p>
        </div>
        <a href="{{ route('clients.create') }}" class="btn btn-primary gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Новий клієнт
        </a>
    </div>

    {{-- Фільтри --}}
    <div class="card bg-base-100 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="flex flex-wrap gap-3 items-center">
                <label class="input input-bordered flex items-center gap-2 flex-1 min-w-48">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input wire:model.live.debounce.300ms="search"
                           type="text"
                           placeholder="Ім'я, телефон, email..."
                           class="grow"/>
                </label>

                <select wire:model.live="type" class="select select-bordered w-44">
                    <option value="">Всі типи</option>
                    <option value="individual">Фізичні особи</option>
                    <option value="legal">Юридичні особи</option>
                </select>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input wire:model.live="showVip" type="checkbox" class="checkbox checkbox-warning checkbox-sm"/>
                    <span class="text-sm">VIP</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input wire:model.live="showBlacklisted" type="checkbox" class="checkbox checkbox-error checkbox-sm"/>
                    <span class="text-sm">Чорний список</span>
                </label>

                @if($search || $type || $showVip || $showBlacklisted)
                    <button wire:click="clearFilters" class="btn btn-ghost btn-square" title="Очистити">
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
                            <button wire:click="sort('name')" class="flex items-center gap-1 hover:text-primary">
                                Клієнт
                                @if($sortBy === 'name')<span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>@endif
                            </button>
                        </th>
                        <th>Телефон</th>
                        <th>Тип</th>
                        <th>
                            <button wire:click="sort('orders_count')" class="flex items-center gap-1 hover:text-primary">
                                Заявок
                                @if($sortBy === 'orders_count')<span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>@endif
                            </button>
                        </th>
                        <th>
                            <button wire:click="sort('created_at')" class="flex items-center gap-1 hover:text-primary">
                                З нами з
                                @if($sortBy === 'created_at')<span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>@endif
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        <tr class="hover cursor-pointer"
                            onclick="window.location='{{ route('clients.show', $client) }}'">
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="avatar placeholder">
                                        <div class="bg-neutral text-neutral-content rounded-full w-8">
                                            <span class="text-xs">{{ substr($client->name, 0, 1) }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-medium flex items-center gap-1">
                                            {{ $client->name }}
                                            @if($client->is_vip)
                                                <span class="badge badge-warning badge-xs">VIP</span>
                                            @endif
                                            @if($client->is_blacklisted)
                                                <span class="badge badge-error badge-xs">⛔</span>
                                            @endif
                                        </div>
                                        @if($client->email)
                                            <div class="text-xs text-base-content/60">{{ $client->email }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $client->phone }}</td>
                            <td>
                                <span class="badge badge-ghost badge-sm">
                                    {{ $client->type === 'legal' ? 'Юр. особа' : 'Фіз. особа' }}
                                </span>
                            </td>
                            <td>
                                <span class="font-medium">{{ $client->orders_count }}</span>
                            </td>
                            <td>{{ $client->created_at->format('d.m.Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-base-content/40">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                @if($search || $type)
                                    Клієнтів не знайдено. <button wire:click="clearFilters" class="link">Очистити фільтри</button>
                                @else
                                    Клієнтів поки немає
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($clients->hasPages())
            <div class="p-4 border-t border-base-200">
                {{ $clients->links() }}
            </div>
        @endif
    </div>
</div>