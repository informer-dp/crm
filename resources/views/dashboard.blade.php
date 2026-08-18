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
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px">

            <div style="background:white;border-radius:12px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,0.1)">
                <p style="font-size:13px;color:#666;margin-bottom:4px">Активних заявок</p>
                <p style="font-size:32px;font-weight:bold">{{ $activeOrders }}</p>
            </div>

            <div style="background:{{ $readyOrders > 0 ? '#f0fdf4' : 'white' }};border-radius:12px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,0.1)">
                <p style="font-size:13px;color:#666;margin-bottom:4px">Готові до видачі</p>
                <p style="font-size:32px;font-weight:bold;color:{{ $readyOrders > 0 ? '#16a34a' : '#000' }}">{{ $readyOrders }}</p>
                @if($readyOrders > 0)
                <a href="{{ route('orders.index', ['status' => 'ready']) }}"
                style="font-size:12px;color:#16a34a;text-decoration:underline">Переглянути →</a>
                @endif
            </div>

            <div style="background:{{ $urgentOrders > 0 ? '#fef2f2' : 'white' }};border-radius:12px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,0.1)">
                <p style="font-size:13px;color:#666;margin-bottom:4px">Термінових</p>
                <p style="font-size:32px;font-weight:bold;color:{{ $urgentOrders > 0 ? '#dc2626' : '#000' }}">{{ $urgentOrders }}</p>
            </div>

            <div style="background:white;border-radius:12px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,0.1)">
                <p style="font-size:13px;color:#666;margin-bottom:4px">Сьогодні прийнято</p>
                <p style="font-size:32px;font-weight:bold">{{ $todayOrders }}</p>
            </div>

        </div>

        {{-- Місячна статистика --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px">

            <div style="background:#6366f1;border-radius:12px;padding:16px;color:white">
                <p style="font-size:13px;opacity:0.8;margin-bottom:4px">Виручка за {{ now()->format('F') }}</p>
                <p style="font-size:28px;font-weight:bold">{{ number_format($monthRevenue, 0, '.', ' ') }} ₴</p>
                <p style="font-size:12px;opacity:0.6;margin-top:4px">{{ $monthOrders }} замовлень</p>
            </div>

            <div style="background:white;border-radius:12px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,0.1)">
                <p style="font-size:13px;color:#666;margin-bottom:4px">Середній чек</p>
                <p style="font-size:28px;font-weight:bold">
                    {{ $monthOrders > 0 ? number_format($monthRevenue / $monthOrders, 0, '.', ' ') : 0 }} ₴
                </p>
                <p style="font-size:12px;color:#999;margin-top:4px">за {{ now()->format('F') }}</p>
            </div>

        </div>

    {{-- Останні заявки і статуси --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

            {{-- Останні заявки --}}
            <div style="background:white;border-radius:12px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,0.1)">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
                    <span style="font-weight:bold">Останні заявки</span>
                    <a href="{{ route('orders.index') }}" style="font-size:12px;color:#6366f1;text-decoration:none">Всі →</a>
                </div>
                @foreach(\App\Models\Order::with(['client','device.brand','device.model'])->latest()->limit(6)->get() as $order)
                <a href="{{ route('orders.show', $order) }}"
                style="display:flex;justify-content:space-between;align-items:center;padding:8px;border-radius:8px;text-decoration:none;color:inherit;margin-bottom:4px"
                onmouseover="this.style.background='#f5f5f5'"
                onmouseout="this.style.background='transparent'">
                    <div style="display:flex;align-items:center;gap:8px">
                        <span style="width:8px;height:8px;border-radius:50%;background:{{ $order->isUrgent() ? '#dc2626' : '#d1d5db' }};flex-shrink:0"></span>
                        <div>
                            <div style="font-size:13px;font-weight:500;font-family:monospace">{{ $order->number }}</div>
                            <div style="font-size:11px;color:#666">{{ $order->client->name }}</div>
                        </div>
                    </div>
                    <div style="text-align:right">
                        @php
                            $colors = [
                                'new' => '#3b82f6', 'diagnosed' => '#f59e0b',
                                'approved' => '#f59e0b', 'in_progress' => '#6366f1',
                                'waiting_parts' => '#9ca3af', 'ready' => '#16a34a',
                                'issued' => '#6b7280', 'cancelled' => '#dc2626',
                            ];
                        @endphp
                        <span style="font-size:11px;padding:2px 6px;border-radius:9999px;background:{{ $colors[$order->status] ?? '#9ca3af' }}20;color:{{ $colors[$order->status] ?? '#9ca3af' }}">
                            {{ $order->status_label }}
                        </span>
                        <div style="font-size:11px;color:#999;margin-top:2px">{{ $order->created_at->format('d.m H:i') }}</div>
                    </div>
                </a>
                @endforeach
            </div>

            {{-- Розподіл по статусах --}}
            <div style="background:white;border-radius:12px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,0.1)">
                <div style="font-weight:bold;margin-bottom:12px">Заявки по статусах</div>
                @php
                    $statuses = [
                        'new'           => ['label' => 'Нові',           'color' => '#3b82f6'],
                        'diagnosed'     => ['label' => 'Діагностика',    'color' => '#f59e0b'],
                        'approved'      => ['label' => 'Узгоджено',      'color' => '#f59e0b'],
                        'in_progress'   => ['label' => 'В роботі',       'color' => '#6366f1'],
                        'waiting_parts' => ['label' => 'Очікує деталей', 'color' => '#9ca3af'],
                        'ready'         => ['label' => 'Готові',         'color' => '#16a34a'],
                    ];
                    $total = \App\Models\Order::active()->count() ?: 1;
                @endphp
                @foreach($statuses as $status => $info)
                @php $count = \App\Models\Order::byStatus($status)->count(); @endphp
                @if($count > 0)
                <div style="margin-bottom:10px">
                    <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px">
                        <span>{{ $info['label'] }}</span>
                        <span style="font-weight:600">{{ $count }}</span>
                    </div>
                    <div style="background:#f3f4f6;border-radius:9999px;height:6px">
                        <div style="background:{{ $info['color'] }};height:6px;border-radius:9999px;width:{{ min(100, round($count / $total * 100)) }}%"></div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>

        </div>
</div>
@endsection