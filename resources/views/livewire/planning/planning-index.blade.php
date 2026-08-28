<div>
    {{-- Шапка --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
        <h1 style="font-size:24px;font-weight:bold">Планування</h1>
        <div style="display:flex;gap:8px;align-items:center">
            {{-- Режим перегляду --}}
            <div style="display:flex;gap:4px;background:#f3f4f6;padding:4px;border-radius:8px">
                @foreach(['day' => 'День', 'week' => 'Тиждень', 'month' => 'Місяць', 'quarter' => '3 місяці'] as $mode => $label)
                <button wire:click="$set('viewMode', '{{ $mode }}')"
                        style="padding:4px 10px;border:none;border-radius:6px;cursor:pointer;font-size:13px;
                               {{ $viewMode === $mode ? 'background:white;box-shadow:0 1px 2px rgba(0,0,0,0.1);font-weight:500' : 'background:transparent;color:#666' }}">
                    {{ $label }}
                </button>
                @endforeach
            </div>

            {{-- Навігація --}}
            <button wire:click="prev"
                    style="padding:6px 12px;background:white;border:1px solid #ddd;border-radius:8px;cursor:pointer">
                ←
            </button>
            <button wire:click="today"
                    style="padding:6px 12px;background:white;border:1px solid #ddd;border-radius:8px;cursor:pointer;font-size:13px">
                Сьогодні
            </button>
            <button wire:click="next"
                    style="padding:6px 12px;background:white;border:1px solid #ddd;border-radius:8px;cursor:pointer">
                →
            </button>

            {{-- Поточний період --}}
            <div style="font-size:14px;font-weight:500;color:#374151;min-width:160px;text-align:center">
                {{ $startDate->format('d.m.Y') }}
                @if($startDate->format('Y-m-d') !== $endDate->format('Y-m-d'))
                    — {{ $endDate->format('d.m.Y') }}
                @endif
            </div>
        </div>
    </div>

    {{-- Легенда --}}
    <div style="display:flex;gap:12px;align-items:center;margin-bottom:16px;font-size:12px;color:#666">
        <span>Навантаження:</span>
        @foreach([
            ['#f3f4f6', 'Немає'],
            ['#86efac', 'Нормальне (≤50%)'],
            ['#fde68a', 'Високе (≤75%)'],
            ['#fca5a5', 'Повне (≤100%)'],
            ['#ef4444', 'Перевантажено'],
        ] as [$color, $label])
        <div style="display:flex;align-items:center;gap:4px">
            <div style="width:16px;height:16px;border-radius:4px;background:{{ $color }};border:1px solid rgba(0,0,0,0.1)"></div>
            <span>{{ $label }}</span>
        </div>
        @endforeach
        <span style="margin-left:8px">Норма: {{ $maxPerDay }} замовлення/інженер/день</span>
    </div>

    {{-- Календар навантаження --}}
    <div style="background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.1);overflow:hidden;margin-bottom:20px">

        {{-- Заголовок таблиці --}}
        <div style="display:grid;grid-template-columns:140px repeat({{ count($days) }}, 1fr);border-bottom:2px solid #e5e7eb">
            <div style="padding:10px 12px;font-size:12px;font-weight:600;color:#666;background:#f9fafb">
                Інженер
            </div>
            @foreach($days as $dateStr => $day)
            <div style="padding:8px 4px;text-align:center;background:#f9fafb;border-left:1px solid #e5e7eb;
                        {{ $day['date']->isToday() ? 'background:#eff6ff;' : '' }}
                        {{ $day['date']->isWeekend() ? 'background:#fafafa;' : '' }}">
                <div style="font-size:11px;color:#888">
                    {{ $day['date']->isoFormat('dd') }}
                </div>
                <div style="font-size:13px;font-weight:{{ $day['date']->isToday() ? 'bold' : '500' }};
                            color:{{ $day['date']->isToday() ? '#6366f1' : '#374151' }}">
                    {{ $day['date']->format('d') }}
                </div>
                @if(!in_array($viewMode, ['day']))
                <div style="font-size:10px;color:#aaa">
                    {{ $day['date']->format('m') }}
                </div>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Рядки інженерів --}}
        @foreach($engineers as $engineer)
        <div style="display:grid;grid-template-columns:140px repeat({{ count($days) }}, 1fr);border-bottom:1px solid #f3f4f6">

            {{-- Ім'я інженера --}}
            <div style="padding:8px 12px;display:flex;align-items:center;gap:8px">
                <div style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:12px;font-weight:bold;flex-shrink:0;background:{{ $engineer->profile?->color ?? '#6366f1' }}">
                    {{ substr($engineer->name, 0, 1) }}
                </div>
                <span style="font-size:12px;font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                    {{ $engineer->name }}
                </span>
            </div>

            {{-- Клітинки днів --}}
            @foreach($days as $dateStr => $day)
            @php
                $cell = $day['engineers'][$engineer->id] ?? null;
                $count = $cell['count'] ?? 0;
                $color = $cell['color'] ?? '#f3f4f6';
                $load  = $cell['load'] ?? 0;
            @endphp
            <div style="border-left:1px solid #f3f4f6;padding:4px;
                        {{ $day['date']->isWeekend() ? 'background:#fafafa;' : '' }}">
                @if($count > 0)
                <div style="background:{{ $color }};border-radius:6px;padding:4px;text-align:center;cursor:pointer;height:100%;min-height:36px;display:flex;flex-direction:column;align-items:center;justify-content:center"
                     title="{{ $cell['orders']->pluck('number')->join(', ') }}">
                    <div style="font-size:14px;font-weight:bold;color:#374151">{{ $count }}</div>
                    @if($viewMode !== 'quarter')
                    <div style="font-size:10px;color:#666">{{ $load }}%</div>
                    @endif
                </div>
                @else
                <div style="height:36px;border-radius:6px;background:{{ $day['date']->isWeekend() ? 'transparent' : '#f9fafb' }}"></div>
                @endif
            </div>
            @endforeach
        </div>
        @endforeach

        {{-- Рядок підсумку --}}
        <div style="display:grid;grid-template-columns:140px repeat({{ count($days) }}, 1fr);background:#f9fafb;border-top:2px solid #e5e7eb">
            <div style="padding:8px 12px;font-size:12px;font-weight:600;color:#666">
                Всього
            </div>
            @foreach($days as $dateStr => $day)
            <div style="border-left:1px solid #e5e7eb;padding:4px;text-align:center">
                @if($day['total'] > 0)
                <div style="font-size:13px;font-weight:bold;color:#374151">{{ $day['total'] }}</div>
                @else
                <div style="color:#d1d5db">—</div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- Таймлайн Ганта --}}
    @if($ganttOrders->count() > 0)
    <div style="background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.1);overflow:hidden;margin-bottom:20px">
        <div style="padding:12px 16px;border-bottom:1px solid #e5e7eb;font-weight:bold;font-size:14px">
            Таймлайн замовлень
        </div>

        <div style="overflow-x:auto">
            <div style="min-width:600px">

                {{-- Заголовок дат --}}
                <div style="display:grid;grid-template-columns:200px 1fr;border-bottom:1px solid #e5e7eb">
                    <div style="padding:8px 12px;font-size:12px;color:#666;background:#f9fafb">Замовлення</div>
                    <div style="background:#f9fafb;position:relative;overflow:hidden">
                        <div style="display:flex">
                            @foreach($days as $dateStr => $day)
                            <div style="flex:1;text-align:center;padding:4px 0;border-left:1px solid #e5e7eb;font-size:10px;color:#888;min-width:24px">
                                {{ $day['date']->format('d') }}
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Рядки замовлень --}}
                @foreach($ganttOrders->take(30) as $order)
                @php
                
                    $orderStart = $order->created_at->startOfDay();
                    $orderEnd   = $order->estimated_date
                        ? \Carbon\Carbon::parse($order->estimated_date)->startOfDay()
                        : $orderStart->copy()->addDays(2);

                    $totalDays  = count($days);
                    $dayKeys    = array_keys($days);

                    $firstDay   = \Carbon\Carbon::parse($dayKeys[0]);
                    $lastDay    = \Carbon\Carbon::parse(end($dayKeys));

                    // Позиція старту (в %)
                    $startOffset = max(0, $firstDay->diffInDays($orderStart, false));
                    $endOffset   = min($totalDays, $firstDay->diffInDays($orderEnd, false) + 1);
                    $width        = max(1, $endOffset - $startOffset);

                    $leftPct  = ($startOffset / $totalDays) * 100;
                    $widthPct = ($width / $totalDays) * 100;

                    $isOverdue = $orderEnd->isPast() && !in_array($order->status, ['ready', 'issued']);
                    $barColor  = $isOverdue ? '#fca5a5' : ($order->priority === 'urgent' ? '#fde68a' : '#c7d2fe');
                @endphp
                <div style="display:grid;grid-template-columns:200px 1fr;border-bottom:1px solid #f3f4f6;min-height:36px"
                     onmouseover="this.style.background='#f9fafb'"
                     onmouseout="this.style.background='white'">

                    <div style="padding:6px 12px">
                        <a href="{{ route('orders.show', $order) }}"
                           style="font-size:12px;font-family:monospace;font-weight:500;color:#6366f1;text-decoration:none">
                            {{ $order->number }}
                        </a>
                        <div style="font-size:11px;color:#666">{{ $order->client->name }}</div>
                    </div>

                    <div style="position:relative;border-left:1px solid #e5e7eb">
                        {{-- Сітка днів --}}
                        <div style="display:flex;height:100%;position:absolute;width:100%;top:0;left:0">
                            @foreach($days as $day)
                            <div style="flex:1;border-left:1px solid #f3f4f6;{{ $day['date']->isWeekend() ? 'background:#fafafa' : '' }}"></div>
                            @endforeach
                        </div>

                        {{-- Бар замовлення --}}
                        <div style="position:absolute;top:6px;height:24px;
                                    left:{{ $leftPct }}%;
                                    width:{{ $widthPct }}%;
                                    background:{{ $barColor }};
                                    border-radius:4px;
                                    border:1px solid rgba(0,0,0,0.1);
                                    display:flex;align-items:center;padding:0 6px;
                                    overflow:hidden;white-space:nowrap;
                                    min-width:4px"
                             title="{{ $order->device->full_name }}">
                            @if($widthPct > 5)
                            <span style="font-size:10px;color:#374151;font-weight:500">
                                {{ $order->device->full_name }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach

                @if($ganttOrders->count() > 30)
                <div style="padding:8px 12px;font-size:12px;color:#666;text-align:center">
                    + ще {{ $ganttOrders->count() - 30 }} замовлень
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Замовлення з найближчими дедлайнами --}}
    <div style="background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.1);overflow:hidden">
        <div style="padding:12px 16px;border-bottom:1px solid #e5e7eb;font-weight:bold;font-size:14px">
            Найближчі дедлайни
        </div>
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb">
                    <th style="padding:8px 16px;text-align:left;font-size:12px;color:#666">Замовлення</th>
                    <th style="padding:8px 16px;text-align:left;font-size:12px;color:#666">Клієнт / Пристрій</th>
                    <th style="padding:8px 16px;text-align:left;font-size:12px;color:#666">Інженер</th>
                    <th style="padding:8px 16px;text-align:left;font-size:12px;color:#666">Статус</th>
                    <th style="padding:8px 16px;text-align:left;font-size:12px;color:#666">Дедлайн</th>
                    <th style="padding:8px 16px;text-align:left;font-size:12px;color:#666">Залишилось</th>
                </tr>
            </thead>
            <tbody>
                @forelse(
                    \App\Models\Order::active()
                        ->with(['client', 'device.brand', 'device.model', 'engineers'])
                        ->whereNotNull('estimated_date')
                        ->orderBy('estimated_date')
                        ->limit(15)
                        ->get()
                    as $order
                )
                @php
                    $deadline = \Carbon\Carbon::parse($order->estimated_date);
                    $isOverdue = $deadline->isPast();
                    $isToday = $deadline->isToday();
                    $isTomorrow = $deadline->isTomorrow();
                    $daysLeft = now()->diffInDays($deadline, false);
                @endphp
                <tr style="border-bottom:1px solid #f3f4f6;background:{{ $isOverdue ? '#fef2f2' : ($isToday ? '#fffbeb' : 'white') }}"
                    onmouseover="this.style.background='#f9fafb'"
                    onmouseout="this.style.background='{{ $isOverdue ? '#fef2f2' : ($isToday ? '#fffbeb' : 'white') }}'">

                    <td style="padding:8px 16px">
                        <a href="{{ route('orders.show', $order) }}"
                           style="font-size:13px;font-family:monospace;font-weight:500;color:#6366f1;text-decoration:none">
                            {{ $order->number }}
                        </a>
                    </td>

                    <td style="padding:8px 16px">
                        <div style="font-size:13px;font-weight:500">{{ $order->client->name }}</div>
                        <div style="font-size:11px;color:#666">{{ $order->device->full_name }}</div>
                    </td>

                    <td style="padding:8px 16px;font-size:13px;color:#666">
                        {{ $order->engineers->first()?->name ?? '—' }}
                    </td>

                    <td style="padding:8px 16px">
                        @php
                            $statusColors = [
                                'new' => '#3b82f6', 'diagnosed' => '#f59e0b',
                                'approved' => '#f59e0b', 'in_progress' => '#6366f1',
                                'waiting_parts' => '#9ca3af', 'ready' => '#16a34a',
                            ];
                        @endphp
                        <span style="font-size:11px;padding:2px 8px;border-radius:9999px;
                                     background:{{ ($statusColors[$order->status] ?? '#9ca3af') }}20;
                                     color:{{ $statusColors[$order->status] ?? '#9ca3af' }}">
                            {{ $order->status_label }}
                        </span>
                    </td>

                    <td style="padding:8px 16px;font-size:13px;font-weight:500;
                               color:{{ $isOverdue ? '#dc2626' : ($isToday ? '#d97706' : '#374151') }}">
                        {{ $deadline->format('d.m.Y') }}
                    </td>

                    <td style="padding:8px 16px">
                        @if($isOverdue)
                            <span style="font-size:12px;color:#dc2626;font-weight:500">
                                ⚠ Прострочено на {{ abs($daysLeft) }} дн.
                            </span>
                        @elseif($isToday)
                            <span style="font-size:12px;color:#d97706;font-weight:500">
                                🔥 Сьогодні
                            </span>
                        @elseif($isTomorrow)
                            <span style="font-size:12px;color:#f59e0b;font-weight:500">
                                Завтра
                            </span>
                        @else
                            <span style="font-size:12px;color:#666">
                                {{ $daysLeft }} дн.
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding:24px;text-align:center;color:#999;font-size:14px">
                        Замовлень з дедлайнами немає
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>