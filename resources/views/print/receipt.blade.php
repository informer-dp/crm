<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Квитанція {{ $order->number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; }

        .page { width: 210mm; min-height: 280mm; padding: 8mm; }

        /* ── Квитанція (1/3 аркуша) ── */
        .receipt-section {
            height: 95mm;
            border: 1px solid #000;
            padding: 4mm;
            position: relative;
        }

        /* ── Лінія розрізу ── */
        .cut-line {
            border-top: 1px dashed #000;
            margin: 3mm 0;
            text-align: center;
            position: relative;
        }
        .cut-line::before {
            content: '✂ розріжте тут';
            position: absolute;
            top: -8px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 0 4px;
            font-size: 9px;
            color: #666;
        }

        /* ── Технічна карта (2/3 аркуша) ── */
        .techcard-section {
            border: 1px solid #000;
            padding: 4mm;
        }

        /* ── Спільні стилі ── */
        .header { text-align: left; margin-bottom: 2mm; }
        .company-name { font-size: 13px; font-weight: bold; }
        .company-info { font-size: 9px; color: #444; }
        .doc-title { float: none; font-size: 12px; font-weight: bold; text-align: center;
                     border-top: 1px solid #000; border-bottom: 1px solid #000;
                     padding: 1mm 0; margin: 2mm 0; }

        .row { display: flex; justify-content: space-between; margin: 1mm 0; }
        .label { color: #555; min-width: 35mm; font-size: 11px;}
        .value { font-weight: bold; text-align: right; }
        .value-left { font-weight: bold; }

        .divider { border-top: 1px solid #ccc; margin: 2mm 0; }
        .bold { font-weight: bold; }
        .small { font-size: 9px; }
        .center { text-align: center; }

        /* ── QR-код ── */
        .qr-block {
            position: relative;
            float: right;
            right: -30mm;
            /*top: 4mm;*/
            background-color: white;
            padding: 5mm;
            margin-left: -30mm;
            text-align: center;
        }
        .qr-block img { width: 22mm; height: 22mm; }
        .qr-block .qr-label { font-size: 10px; color: #666; margin-top: 1mm; }

        /* ── Технічна карта ── */
        .techcard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4mm;
        }

        .techcard-block { margin-bottom: 3mm; }
        .techcard-block-title {
            font-weight: bold;
            font-size: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 1mm;
            margin-bottom: 2mm;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .field-line {
            border-bottom: 1px solid #ccc;
            min-height: 6mm;
            margin-bottom: 2mm;
            padding-bottom: 1mm;
        }
        .field-label { font-size: 11px; color: #555; margin-bottom: 0.5mm; }

        /* ── Чеклист ── */
        .checklist { column-count: 2; column-gap: 4mm; }
        .checklist-item {
            display: flex;
            align-items: center;
            gap: 2mm;
            margin-bottom: 1.5mm;
            break-inside: avoid;
        }
        .checkbox {
            width: 4mm; height: 4mm;
            border: 1px solid #000;
            flex-shrink: 0;
            display: inline-block;
        }

        /* ── Підписи ── */
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4mm;
            margin-top: 3mm;
        }
        .sign-line {
            border-bottom: 1px solid #000;
            height: 6mm;
            margin-top: 5mm;
        }
        .sign-label { font-size: 11px; color: #555; margin-top: 1mm; }

        /* ── Друк ── */
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
            @page { margin: 0; size: A4; }
        }
    </style>
</head>
<body>
<div class="page">

    {{-- ════════════════════════════════════
         КВИТАНЦІЯ (1/3 аркуша)
    ════════════════════════════════════ --}}
    <div class="receipt-section">

        

        {{-- Шапка --}}
        <div class="header" style="margin-right: 26mm">
            {{-- QR-код --}}
        <div class="qr-block">
            {!! QrCode::size(84)->generate(
                config('app.url') . '/track?number=' . $order->number . '&code=' . $order->check_code
            ) !!}
            <div class="qr-label">Перевірити статус</div>
        </div>
            <div class="company-name">{{ \App\Models\Setting::get('company_name') }}</div>
            <div class="company-info">
                {{ \App\Models\Setting::get('company_address') }} |
                {{ \App\Models\Setting::get('company_phone') }}
            </div>
        </div>

        {{-- Заголовок --}}
        <div class="doc-title"><h2>КВИТАНЦІЯ ПРО ПРИЙОМ ПРИСТРОЮ</h2></div>

        {{-- Основні дані --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:2mm">
            <div>
                <div class="row">
                    <span class="label">№ замовлення:</span>
                    <span class="value">{{ $order->number }}</span>
                </div>
                <div class="row">
                    <span class="label">Дата прийому:</span>
                    <span class="value">{{ now()->format('d.m.Y H:i') }}</span>
                </div>
                <div class="row">
                    <span class="label">Код перевірки:</span>
                    <span class="value">{{ $order->check_code }}</span>
                </div>
                @if($order->estimated_date)
                <div class="row">
                    <span class="label">Очік. дата:</span>
                    <span class="value">{{ $order->estimated_date->format('d.m.Y') }}</span>
                </div>
                @endif
            </div>
            <div>
                <div class="row">
                    <span class="label">Клієнт:</span>
                    <span class="value-left">{{ $order->client->name }}</span>
                </div>
                <div class="row">
                    <span class="label">Телефон:</span>
                    <span class="value-left">{{ $order->client->phone }}</span>
                </div>
                @if($order->prepayment > 0)
                <div class="row">
                    <span class="label">Передоплата:</span>
                    <span class="value">{{ number_format($order->prepayment, 0, '.', ' ') }} ₴</span>
                </div>
                @endif
            </div>
        </div>

        <div class="divider"></div>

        {{-- Пристрій --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:2mm">
            <div>
                <div class="row">
                    <span class="label">Пристрій:</span>
                    <span class="value-left bold">{{ $order->device->full_name }}</span>
                </div>
                @if($order->device->serial_number)
                <div class="row">
                    <span class="label">SN:</span>
                    <span class="value-left">{{ $order->device->serial_number }}</span>
                </div>
                @endif
                @if($order->device->imei)
                <div class="row">
                    <span class="label">IMEI:</span>
                    <span class="value-left">{{ $order->device->imei }}</span>
                </div>
                @endif
            </div>
            {{-- права колонка пристрою --}}
            <div>
                <div class="field-label" style="font-size:9px;color:#555;margin-bottom:1mm">Комплектація:</div>
                {{-- Комплектація в квитанції --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5mm">
                    @foreach([
                        'charger'     => 'Зарядний пристрій',
                        'cable'       => 'Кабель',
                        'case'        => 'Чохол',
                        'glass'       => 'Захисне скло',
                        'sim'         => 'SIM-карта',
                        'memory_card' => 'Карта пам\'яті',
                        'bag'         => 'Сумка',
                        'other'       => 'Інше',
                    ] as $key => $label)
                    <div style="display:flex;align-items:center;gap:1mm;font-size:9px">
                        @if(in_array($key, $order->device->equipment ?? []))
                            <span style="width:3mm;height:3mm;border:1px solid #000;display:inline-block;flex-shrink:0;background:#000;position:relative">
                                <span style="position:absolute;top:-1px;left:0;color:white;font-size:8px;font-weight:bold">✓</span>
                            </span>
                        @else
                            <span style="width:3mm;height:3mm;border:1px solid #000;display:inline-block;flex-shrink:0"></span>
                        @endif
                        {{ $label }}
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        @if($order->malfunction)
        <div class="divider"></div>
        <div class="row">
            <span class="label">Несправність:</span>
            <span style="flex:1;padding-left:2mm">{{ $order->malfunction }}</span>
        </div>
        @endif

        {{-- Підпис --}}
        <div style="display:flex;justify-content:space-between;margin-top:3mm;align-items:flex-end">
            <div class="small" style="color:#555">
                {{ \App\Models\Setting::get('receipt_footer_text') }}
            </div>
            <div style="text-align:right">
                <div class="small" style="color:#555">Прийняв: {{ $order->manager?->name ?? auth()->user()->name }}</div>
                <div style="border-bottom:1px solid #000;width:40mm;margin-top:4mm"></div>
                <div class="small" style="color:#555;margin-top:1mm">Підпис клієнта</div>
            </div>
        </div>
    </div>

    {{-- Лінія розрізу --}}
    <div class="cut-line"></div>

    {{-- ════════════════════════════════════
         ТЕХНІЧНА КАРТА (2/3 аркуша)
    ════════════════════════════════════ --}}
    <div class="techcard-section">

        {{-- Заголовок техкарти --}}
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:3mm">
            <div>
                <div class="bold" style="font-size:13px">ТЕХНІЧНА КАРТА</div>
                <div class="small" style="color:#555">{{ \App\Models\Setting::get('company_name') }}</div>
            </div>
            <div style="text-align:right">
                <div class="bold" style="font-size:14px">{{ $order->number }}</div>
                <div class="small">{{ now()->format('d.m.Y') }}</div>
            </div>
        </div>

        <div class="techcard-grid">

            {{-- ЛІВА КОЛОНКА --}}
            <div>

                {{-- Клієнт і пристрій --}}
                <div class="techcard-block">
                    <div class="techcard-block-title">Клієнт і пристрій</div>
                    <div class="field-label">Клієнт / Телефон</div>
                    <div class="field-line" style="font-size:14px">
                        {{ $order->client->name }} | {{ $order->client->phone }}
                    </div>
                    <div class="field-label">Пристрій</div>
                    <div class="field-line bold" style="font-size:11px">
                        {{ $order->device->full_name }}
                        @if($order->device->serial_number) | SN: {{ $order->device->serial_number }} @endif
                        @if($order->device->imei) | IMEI: {{ $order->device->imei }} @endif
                    </div>
                </div>

                {{-- Комплектація --}}
                <div class="techcard-block">
                    <div class="techcard-block-title">Комплектація</div>
                    {{-- Комплектація в квитанції --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5mm">
                        @foreach([
                            'charger'     => 'Зарядний пристрій',
                            'cable'       => 'Кабель',
                            'case'        => 'Чохол',
                            'glass'       => 'Захисне скло',
                            'sim'         => 'SIM-карта',
                            'memory_card' => 'Карта пам\'яті',
                            'bag'         => 'Сумка',
                            'other'       => 'Інше',
                        ] as $key => $label)
                        <div style="display:flex;align-items:center;gap:1mm;font-size:9px">
                            @if(in_array($key, $order->device->equipment ?? []))
                                <span style="width:3mm;height:3mm;border:1px solid #000;display:inline-block;flex-shrink:0;background:#000;position:relative">
                                    <span style="position:absolute;top:-1px;left:0;color:white;font-size:8px;font-weight:bold">✓</span>
                                </span>
                            @else
                                <span style="width:3mm;height:3mm;border:1px solid #000;display:inline-block;flex-shrink:0"></span>
                            @endif
                            {{ $label }}
                        </div>
                        @endforeach
                    </div>
                    <div class="field-label">Додатково</div>
                    <div class="field-line"></div>
                </div>

                {{-- Несправність --}}
                <div class="techcard-block">
                    <div class="techcard-block-title">Несправність (зі слів клієнта)</div>
                    <div style="min-height:12mm;border:1px solid #ddd;padding:2mm;font-size:10px;border-radius:2mm">
                        {{ $order->malfunction }}
                    </div>
                </div>

                

            </div>

            {{-- ПРАВА КОЛОНКА --}}
            <div>

                {{-- Чеклист перевірки --}}
                <div class="techcard-block">
                    <div class="techcard-block-title">✓ Чеклист перевірки після ремонту</div>
                    @php
                        $deviceTypeName = strtolower($order->device->deviceType->name ?? '');
                        if (str_contains($deviceTypeName, 'смартфон') || str_contains($deviceTypeName, 'планшет')) {
                            $checklistKey = 'checklist_smartphone';
                        } elseif (str_contains($deviceTypeName, 'ноутбук') || str_contains($deviceTypeName, 'пк') || str_contains($deviceTypeName, 'моноблок')) {
                            $checklistKey = 'checklist_laptop';
                        } else {
                            $checklistKey = 'checklist_universal';
                        }
                        $checklistItems = array_filter(
                            explode("\n", \App\Models\Setting::get($checklistKey, '')),
                            fn($item) => trim($item) !== ''
                        );
                    @endphp
                    <div class="checklist">
                        @foreach($checklistItems as $item)
                        <div class="checklist-item">
                            <span class="checkbox"></span>
                            <span style="font-size:10px">{{ trim($item) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Нотатки інженера --}}
                <div class="techcard-block" style="margin-top:3mm">
                    <div class="techcard-block-title">Нотатки інженера</div>
                    <div class="field-line" style="min-height:6mm">{{ $order->notes }}</div>
                    <div class="field-line"></div>
                </div>

                {{-- Фінансовий підсумок --}}
                @if($order->estimate)
                <div class="techcard-block" style="margin-top:3mm">
                    <div class="techcard-block-title">Фінансовий підсумок</div>
                    <div class="row">
                        <span class="label">Роботи:</span>
                        <span class="value">{{ number_format($order->estimate->works_total, 0, '.', ' ') }} ₴</span>
                    </div>
                    <div class="row">
                        <span class="label">Запчастини:</span>
                        <span class="value">{{ number_format($order->estimate->parts_total, 0, '.', ' ') }} ₴</span>
                    </div>
                    @if($order->estimate->discount > 0)
                    <div class="row">
                        <span class="label">Знижка:</span>
                        <span class="value">-{{ number_format($order->estimate->discount, 0, '.', ' ') }}
                            {{ $order->estimate->discount_type === 'percent' ? '%' : '₴' }}</span>
                    </div>
                    @endif
                    @if($order->prepayment > 0)
                    <div class="row">
                        <span class="label">Передоплата:</span>
                        <span class="value">{{ number_format($order->prepayment, 0, '.', ' ') }} ₴</span>
                    </div>
                    @endif
                    <div class="divider"></div>
                    <div class="row bold">
                        <span>РАЗОМ:</span>
                        <span>{{ number_format($order->estimate->total, 0, '.', ' ') }} ₴</span>
                    </div>
                    @if($order->prepayment > 0)
                    <div class="row bold" style="color:#c00">
                        <span>ДО СПЛАТИ:</span>
                        <span>{{ number_format(max(0, $order->estimate->total - $order->prepayment), 0, '.', ' ') }} ₴</span>
                    </div>
                    @endif
                </div>
                @endif

            </div>
        </div>

        {{-- Підписи --}}
        <div class="signatures" style="margin-top:4mm;border-top:1px solid #000;padding-top:3mm">
            <div>
                <div class="small" style="color:#555">Інженер</div>
                <div class="sign-line"></div>
                <div class="sign-label">ПІБ та підпис</div>
            </div>
            <div>
                <div class="small" style="color:#555">Клієнт отримав пристрій. Претензій не маю.</div>
                <div class="sign-line"></div>
                <div class="sign-label">Підпис клієнта при видачі</div>
            </div>
        </div>

    </div>
</div>

{{-- Кнопки --}}
<div class="no-print" style="padding:16px;text-align:center;position:fixed;bottom:0;left:0;right:0;background:white;border-top:1px solid #eee;box-shadow:0 -2px 8px rgba(0,0,0,0.1)">
    <button onclick="window.print()"
            style="padding:10px 24px;background:#6366f1;color:white;border:none;border-radius:8px;font-size:15px;cursor:pointer;margin-right:8px">
        🖨 Друкувати
    </button>
    <button onclick="window.close()"
            style="padding:10px 24px;background:#f3f4f6;border:none;border-radius:8px;font-size:15px;cursor:pointer">
        ✕ Закрити
    </button>
</div>

</body>
</html>