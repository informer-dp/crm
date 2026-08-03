<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Гарантійний талон {{ $order->number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #000; }
        .warranty { width: 148mm; padding: 8mm; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .large { font-size: 18px; }
        .medium { font-size: 14px; }
        .small { font-size: 10px; }
        .divider { border-top: 1px solid #000; margin: 6px 0; }
        .divider-dash { border-top: 1px dashed #000; margin: 6px 0; }
        .row { display: flex; justify-content: space-between; margin: 3px 0; }
        .mt { margin-top: 8px; }
        .mb { margin-bottom: 8px; }
        .border-box {
            border: 2px solid #000;
            padding: 6px;
            margin: 6px 0;
            border-radius: 4px;
        }
        table { width: 100%; border-collapse: collapse; margin: 4px 0; }
        td { padding: 3px 4px; vertical-align: top; border-bottom: 1px solid #eee; }
        td:last-child { text-align: right; white-space: nowrap; }
        .no-print { display: block; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
<div class="warranty">

    {{-- Шапка --}}
    <div class="center mb">
        <div class="bold large">ГАРАНТІЙНИЙ ТАЛОН</div>
        <div class="medium bold mt">{{ config('app.name') }}</div>
        <div class="small">{{ \App\Models\Setting::get('company_address') }}</div>
        <div class="small">{{ \App\Models\Setting::get('company_phone') }}</div>
    </div>

    <div class="divider"></div>

    {{-- Дані замовлення --}}
    <div class="border-box mt mb">
        <div class="row">
            <span class="bold">№ замовлення:</span>
            <span class="bold large">{{ $order->number }}</span>
        </div>
        <div class="row">
            <span>Дата видачі:</span>
            <span class="bold">{{ now()->format('d.m.Y') }}</span>
        </div>
        <div class="row">
            <span>Гарантія до:</span>
            <span class="bold">{{ now()->addDays((int)\App\Models\Setting::get('default_warranty_days', 30))->format('d.m.Y') }}</span>
        </div>
    </div>

    {{-- Клієнт і пристрій --}}
    <div class="mt mb">
        <div class="row">
            <span>Клієнт:</span>
            <span class="bold">{{ $order->client->name }}</span>
        </div>
        <div class="row">
            <span>Телефон:</span>
            <span>{{ $order->client->phone }}</span>
        </div>
        <div class="divider-dash"></div>
        <div class="row">
            <span>Пристрій:</span>
            <span class="bold">{{ $order->device->full_name }}</span>
        </div>
        @if($order->device->serial_number)
        <div class="row">
            <span>Серійний номер:</span>
            <span>{{ $order->device->serial_number }}</span>
        </div>
        @endif
        @if($order->device->imei)
        <div class="row">
            <span>IMEI:</span>
            <span>{{ $order->device->imei }}</span>
        </div>
        @endif
    </div>

    <div class="divider"></div>

    {{-- Виконані роботи --}}
    @if($order->estimate && $order->estimate->works->count())
    <div class="mt mb">
        <div class="bold mb">Виконані роботи:</div>
        <table>
            @foreach($order->estimate->works->where('is_warranty', false) as $work)
            <tr>
                <td>{{ $work->name }}</td>
                <td>{{ number_format($work->total, 0, '.', ' ') }} ₴</td>
            </tr>
            @endforeach
        </table>
    </div>
    <div class="divider"></div>
    @endif

    {{-- Умови гарантії --}}
    <div class="mt mb">
        <div class="bold mb">Умови гарантії:</div>
        <div class="small">{{ \App\Models\Setting::get('warranty_terms_text') }}</div>
    </div>

    <div class="divider"></div>

    {{-- Підписи --}}
    <div class="mt" style="display: flex; justify-content: space-between;">
        <div>
            <div class="small">Виконавець:</div>
            <div class="mt" style="margin-top: 16px; border-top: 1px solid #000; width: 60mm; padding-top: 2px;">
                <div class="small">{{ $order->manager?->name ?? auth()->user()->name }}</div>
            </div>
        </div>
        <div>
            <div class="small">Клієнт:</div>
            <div class="mt" style="margin-top: 16px; border-top: 1px solid #000; width: 60mm; padding-top: 2px;">
                <div class="small">{{ $order->client->name }}</div>
            </div>
        </div>
    </div>

    <div class="divider-dash mt"></div>

    {{-- Відривний купон --}}
    <div class="center small mt">
        <div class="bold">Зберігайте цей талон протягом гарантійного терміну</div>
        <div class="mt">{{ config('app.name') }} · {{ \App\Models\Setting::get('company_phone') }}</div>
        <div>Замовлення: <span class="bold">{{ $order->number }}</span> · Гарантія до: <span class="bold">{{ now()->addDays((int)\App\Models\Setting::get('default_warranty_days', 30))->format('d.m.Y') }}</span></div>
    </div>

</div>

{{-- Кнопки --}}
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