<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Квитанція {{ $order->number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #000; }
        .receipt { width: 80mm; padding: 5mm; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .large { font-size: 16px; }
        .small { font-size: 10px; }
        .divider { border-top: 1px dashed #000; margin: 4px 0; }
        .row { display: flex; justify-content: space-between; margin: 2px 0; }
        .mt { margin-top: 6px; }
        .mb { margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin: 4px 0; }
        td { padding: 2px 0; vertical-align: top; }
        td:last-child { text-align: right; white-space: nowrap; }
        .total-row td { font-weight: bold; font-size: 14px; border-top: 1px solid #000; padding-top: 3px; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
<div class="receipt">

    {{-- Шапка --}}
    <div class="center mb">
        <div class="bold large">{{ config('app.name') }}</div>
        <div class="small">{{ \App\Models\Setting::get('company_address') }}</div>
        <div class="small">{{ \App\Models\Setting::get('company_phone') }}</div>
    </div>

    <div class="divider"></div>

    {{-- Тип документу --}}
    <div class="center bold mt mb">
        @if($type === 'acceptance')
            КВИТАНЦІЯ ПРО ПРИЙОМ
        @elseif($type === 'prepayment')
            КВИТАНЦІЯ ПРО ПЕРЕДОПЛАТУ
        @else
            КВИТАНЦІЯ ПРО ОПЛАТУ
        @endif
    </div>

    <div class="divider"></div>

    {{-- Дані заявки --}}
    <div class="mt mb">
        <div class="row">
            <span>№ замовлення:</span>
            <span class="bold">{{ $order->number }}</span>
        </div>
        <div class="row">
            <span>Дата:</span>
            <span>{{ now()->format('d.m.Y H:i') }}</span>
        </div>
        <div class="row">
            <span>Код перевірки:</span>
            <span class="bold">{{ $order->check_code }}</span>
        </div>
        @if($order->estimated_date)
        <div class="row">
            <span>Очік. дата:</span>
            <span>{{ $order->estimated_date->format('d.m.Y') }}</span>
        </div>
        @endif
    </div>

    <div class="divider"></div>

    {{-- Клієнт --}}
    <div class="mt mb">
        <div class="row">
            <span>Клієнт:</span>
            <span class="bold">{{ $order->client->name }}</span>
        </div>
        <div class="row">
            <span>Телефон:</span>
            <span>{{ $order->client->phone }}</span>
        </div>
    </div>

    <div class="divider"></div>

    {{-- Пристрій --}}
    <div class="mt mb">
        <div class="row">
            <span>Пристрій:</span>
            <span class="bold">{{ $order->device->full_name }}</span>
        </div>
        @if($order->device->serial_number)
        <div class="row">
            <span>SN:</span>
            <span>{{ $order->device->serial_number }}</span>
        </div>
        @endif
        @if($order->device->imei)
        <div class="row">
            <span>IMEI:</span>
            <span>{{ $order->device->imei }}</span>
        </div>
        @endif
        @if($order->device->appearance)
        <div class="mt small">
            <div>Вигляд: {{ $order->device->appearance }}</div>
        </div>
        @endif
    </div>

    <div class="divider"></div>

    {{-- Несправність --}}
    <div class="mt mb small">
        <div class="bold">Несправність:</div>
        <div>{{ $order->malfunction }}</div>
    </div>

    @if($type !== 'acceptance' && $order->estimate)
    <div class="divider"></div>

    {{-- Кошторис --}}
    <div class="mt mb">
        @if($order->estimate->works->count())
        <div class="small bold mb">Роботи:</div>
        <table>
            @foreach($order->estimate->works as $work)
            <tr>
                <td>{{ $work->name }}</td>
                <td>{{ number_format($work->total, 0, '.', ' ') }} ₴</td>
            </tr>
            @endforeach
        </table>
        @endif

        @if($order->estimate->parts->count())
        <div class="small bold mb mt">Запчастини:</div>
        <table>
            @foreach($order->estimate->parts as $part)
            <tr>
                <td>{{ $part->name }}{{ $part->is_own_part ? ' (кл.)' : '' }}</td>
                <td>{{ number_format($part->total, 0, '.', ' ') }} ₴</td>
            </tr>
            @endforeach
        </table>
        @endif

        <table class="mt">
            @if($order->estimate->discount > 0)
            <tr>
                <td>Знижка:</td>
                <td>-{{ number_format($order->estimate->discount, 0, '.', ' ') }}
                    {{ $order->estimate->discount_type === 'percent' ? '%' : '₴' }}</td>
            </tr>
            @endif
            @if($order->prepayment > 0)
            <tr>
                <td>Передоплата:</td>
                <td>{{ number_format($order->prepayment, 0, '.', ' ') }} ₴</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>РАЗОМ:</td>
                <td>{{ number_format($order->estimate->total, 0, '.', ' ') }} ₴</td>
            </tr>
            @if($order->prepayment > 0)
            <tr>
                <td>До сплати:</td>
                <td class="bold">{{ number_format($order->estimate->total - $order->prepayment, 0, '.', ' ') }} ₴</td>
            </tr>
            @endif
        </table>
    </div>
    @endif

    @if($type === 'acceptance')
    <div class="divider"></div>
    {{-- Передоплата при прийомі --}}
    @if($order->prepayment > 0)
    <div class="mt mb">
        <div class="row bold">
            <span>Передоплата:</span>
            <span>{{ number_format($order->prepayment, 0, '.', ' ') }} ₴</span>
        </div>
    </div>
    <div class="divider"></div>
    @endif
    @endif

    {{-- Підпис --}}
    <div class="mt mb small">
        <div class="row mt">
            <span>Прийняв:</span>
            <span>{{ $order->manager?->name ?? auth()->user()->name }}</span>
        </div>
        <div class="row mt" style="margin-top: 12px;">
            <span>Підпис клієнта: ___________</span>
        </div>
    </div>

    <div class="divider"></div>

    {{-- Футер --}}
    <div class="center small mt">
        <div>Перевірити статус замовлення:</div>
        <div>{{ \App\Models\Setting::get('company_website') }}</div>
        <div class="mt">Код: <span class="bold">{{ $order->check_code }}</span></div>
        <div class="mt">{{ \App\Models\Setting::get('receipt_footer_text') }}</div>
    </div>

</div>

{{-- Кнопка друку --}}
<div class="no-print" style="padding: 10px; text-align: center;">
    <button onclick="window.print()" style="padding: 8px 20px; font-size: 14px; cursor: pointer;">
        🖨 Друкувати
    </button>
    <button onclick="window.close()" style="padding: 8px 20px; font-size: 14px; cursor: pointer; margin-left: 10px;">
        ✕ Закрити
    </button>
</div>

</body>
</html>