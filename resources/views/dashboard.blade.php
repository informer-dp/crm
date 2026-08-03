@extends('layouts.app')

@section('content')
<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Дашборд</h1>
            <p class="text-base-content/60 text-sm mt-1">{{ now()->format('d.m.Y') }}</p>
        </div>
        <a href="{{ route('orders.create') }}" class="btn btn-primary gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Нова заявка
        </a>
    </div>

    @php
        $activeOrders   = \App\Models\Order::active()->count();
        $readyOrders    = \App\Models\Order::byStatus('ready')->count();
        $urgentOrders   = \App\Models\Order::active()->urgent()->count();
        $todayOrders    = \App\Models\Order::whereDate('created_at', today())->count();
        $monthRevenue = \App\Models\Order::whereMonth('orders.created_at', now()->month)
                    ->whereYear('orders.created_at', now()->year)
                    ->join('estimates', 'orders.id', '=', 'estimates.order_id')
                    ->sum('estimates.total');

        $monthOrders = \App\Models\Order::whereMonth('orders.created_at', now()->month)
                            ->whereYear('orders.created_at', now()->year)
                            ->count();
    @endphp

    {{-- Картки статистики --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        <div class="card bg-base-100 shadow-sm">
            <div class="card-body p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-base-content/60 text-sm">Активних заявок</p>
                        <p class="text-3xl font-bold mt-1">{{ $activeOrders }}</p>
                    </div>
                    <div class="text-primary opacity-60">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-success/10 shadow-sm">
            <div class="card-body p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-base-content/60 text-sm">Готові до видачі</p>
                        <p class="text-3xl font-bold mt-1 text-success">{{ $readyOrders }}</p>
                    </div>
                    <div class="text-success opacity-60">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                @if($readyOrders > 0)
                <a href="{{ route('orders.index', ['status' => 'ready']) }}" class="text-xs text-success link mt-1">
                    Переглянути →
                </a>
                @endif
            </div>
        </div>

        <div class="card {{ $urgentOrders > 0 ? 'bg-error/10' : 'bg-base-100' }} shadow-sm">
            <div class="card-body p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-base-content/60 text-sm">Термінових</p>
                        <p class="text-3xl font-bold mt-1 {{ $urgentOrders > 0 ? 'text-error' : '' }}">{{ $urgentOrders }}</p>
                    </div>
                    <div class="{{ $urgentOrders > 0 ? 'text-error' : 'text-base-content/30' }} opacity-60">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-sm">
            <div class="card-body p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-base-content/60 text-sm">Сьогодні прийнято</p>
                        <p class="text-3xl font-bold mt-1">{{ $todayOrders }}</p>
                    </div>
                    <div class="text-info opacity-60">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Місячна статистика --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">

        <div class="card bg-base-100 shadow-sm">
            <div class="card-body p-4">
                <p class="text-base-content/60 text-sm">Виручка за {{ now()->translatedFormat('F') }}</p>
                <p class="text-3xl font-bold mt-1">{{ number_format($monthRevenue, 0, '.', ' ') }} ₴</p>
                <p class="text-base-content/40 text-xs mt-1">{{ $monthOrders }} замовлень</p>
            </div>
        </div>

        <div class="card bg-base-100 shadow-sm">
            <div class="card-body p-4">
                <p class="text-base-content/60 text-sm">Середній чек</p>
                <p class="text-3xl font-bold mt-1">
                    {{ $monthOrders > 0 ? number_format($monthRevenue / $monthOrders, 0, '.', ' ') : 0 }} ₴
                </p>
                <p class="text-base-content/40 text-xs mt-1">за {{ now()->translatedFormat('F') }}</p>
            </div>
        </div>

    </div>

    {{-- Останні заявки і статуси --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Останні заявки --}}
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="card-title text-base">Останні заявки</h2>
                    <a href="{{ route('orders.index') }}" class="text-xs link link-primary">Всі →</a>
                </div>
                <div class="flex flex-col gap-2">
                    @foreach(\App\Models\Order::with(['client','device.brand','device.model'])->latest()->limit(6)->get() as $order)
                    <a href="{{ route('orders.show', $order) }}"
                       class="flex items-center justify-between p-2 rounded-lg hover:bg-base-200 transition-colors">
                        <div class="flex items-center gap-3">
                            @if($order->isUrgent())
                                <span class="w-2 h-2 bg-error rounded-full flex-shrink-0"></span>
                            @else
                                <span class="w-2 h-2 bg-base-300 rounded-full flex-shrink-0"></span>
                            @endif
                            <div>
                                <p class="text-sm font-medium font-mono">{{ $order->number }}</p>
                                <p class="text-xs text-base-content/60">{{ $order->client->name }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            @php
                                $colors = [
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
                            <span class="badge {{ $colors[$order->status] ?? 'badge-ghost' }} badge-xs">
                                {{ $order->status_label }}
                            </span>
                            <p class="text-xs text-base-content/40 mt-1">{{ $order->created_at->format('d.m H:i') }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Розподіл по статусах --}}
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-base mb-3">Заявки по статусах</h2>
                @php
                    $statuses = [
                        'new'           => ['label' => 'Нові',              'color' => 'bg-info'],
                        'diagnosed'     => ['label' => 'Діагностика',       'color' => 'bg-warning'],
                        'approved'      => ['label' => 'Узгоджено',         'color' => 'bg-warning'],
                        'in_progress'   => ['label' => 'В роботі',          'color' => 'bg-primary'],
                        'waiting_parts' => ['label' => 'Очікує деталей',    'color' => 'bg-base-300'],
                        'ready'         => ['label' => 'Готові',            'color' => 'bg-success'],
                    ];
                    $total = \App\Models\Order::active()->count() ?: 1;
                @endphp
                <div class="flex flex-col gap-3">
                    @foreach($statuses as $status => $info)
                    @php $count = \App\Models\Order::byStatus($status)->count(); @endphp
                    @if($count > 0)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span>{{ $info['label'] }}</span>
                            <span class="font-medium">{{ $count }}</span>
                        </div>
                        <div class="w-full bg-base-200 rounded-full h-2">
                            <div class="{{ $info['color'] }} h-2 rounded-full transition-all"
                                 style="width: {{ min(100, round($count / $total * 100)) }}%"></div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
@endsection