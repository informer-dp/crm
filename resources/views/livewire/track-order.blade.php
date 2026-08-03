<div>
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold">Перевірка статусу замовлення</h1>
        <p class="text-base-content/60 mt-2">
            Введіть номер замовлення та код з квитанції
        </p>
    </div>

    {{-- Форма пошуку --}}
    <div class="card bg-base-100 shadow-sm mb-6">
        <div class="card-body">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="form-control">
                    <label class="label"><span class="label-text">Номер замовлення</span></label>
                    <input wire:model="orderNumber"
                           type="text"
                           class="input input-bordered"
                           placeholder="SC-2026-00001"
                           wire:keydown.enter="track"/>
                    @error('orderNumber')<span class="text-error text-xs">{{ $message }}</span>@enderror
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Код перевірки</span></label>
                    <input wire:model="checkCode"
                           type="text"
                           maxlength="6"
                           class="input input-bordered tracking-widest text-center text-lg font-mono"
                           placeholder="000000"
                           wire:keydown.enter="track"/>
                    @error('checkCode')<span class="text-error text-xs">{{ $message }}</span>@enderror
                </div>
            </div>
            <button wire:click="track"
                    wire:loading.attr="disabled"
                    class="btn btn-primary w-full mt-2 gap-2">
                <span wire:loading wire:target="track" class="loading loading-spinner loading-sm"></span>
                Перевірити статус
            </button>
        </div>
    </div>

    {{-- Помилка --}}
    @if($searched && $error)
    <div class="alert alert-error">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>{{ $error }}</span>
    </div>
    @endif

    {{-- Результат --}}
    @if($order)
    <div class="flex flex-col gap-4">

        {{-- Статус --}}
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
            $statusIcons = [
                'new'           => '📋',
                'diagnosed'     => '🔍',
                'approved'      => '✅',
                'in_progress'   => '🔧',
                'waiting_parts' => '⏳',
                'ready'         => '🎉',
                'issued'        => '✓',
                'cancelled'     => '❌',
            ];
        @endphp

        <div class="card bg-base-100 shadow-sm">
            <div class="card-body text-center">
                <div class="text-5xl mb-3">{{ $statusIcons[$order->status] ?? '📋' }}</div>
                <div class="text-2xl font-bold mb-2">{{ $order->status_label }}</div>
                <span class="badge {{ $statusColors[$order->status] ?? 'badge-ghost' }} badge-lg">
                    {{ $order->number }}
                </span>

                @if($order->status === 'ready')
                <div class="alert alert-success mt-4">
                    <span class="font-medium">Ваш пристрій готовий до видачі!</span>
                </div>
                @endif

                @if($order->estimated_date && !in_array($order->status, ['ready', 'issued', 'cancelled']))
                <p class="text-base-content/60 text-sm mt-3">
                    Очікувана дата готовності:
                    <span class="font-medium">{{ $order->estimated_date->format('d.m.Y') }}</span>
                </p>
                @endif
            </div>
        </div>

        {{-- Деталі --}}
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-base">Деталі замовлення</h2>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-base-content/60">Пристрій</p>
                        <p class="font-medium">{{ $order->device->full_name }}</p>
                    </div>
                    <div>
                        <p class="text-base-content/60">Тип</p>
                        <p class="font-medium">{{ $order->type_label }}</p>
                    </div>
                    <div>
                        <p class="text-base-content/60">Дата прийому</p>
                        <p class="font-medium">{{ $order->created_at->format('d.m.Y') }}</p>
                    </div>
                    @if($order->estimate)
                    <div>
                        <p class="text-base-content/60">Сума</p>
                        <p class="font-medium">{{ number_format($order->estimate->total, 0, '.', ' ') }} ₴</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Публічні коментарі --}}
        @if($order->publicComments->count())
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-base">Повідомлення від майстра</h2>
                <div class="flex flex-col gap-3">
                    @foreach($order->publicComments as $comment)
                    <div class="bg-base-200 rounded-lg p-3 text-sm">
                        <p>{{ $comment->body }}</p>
                        <p class="text-base-content/40 text-xs mt-1">
                            {{ $comment->created_at->format('d.m.Y H:i') }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

    </div>
    @endif
</div>