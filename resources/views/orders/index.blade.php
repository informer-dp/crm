@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Замовлення</h3>
<hr />
<!-- Presets -->
 <div class="btn-group mb-3">
    <!-- <a href="{{ route('orders.index', ['preset' => 'my']) }}" class="btn btn-outline-secondary">Мої</a> -->
    <a href="{{ route('orders.index', ['preset' => 'today']) }}" class="btn btn-outline-secondary">Сьогодні</a>
    <!-- <a href="{{ route('orders.index', ['preset' => 'overdue']) }}" class="btn btn-outline-danger">Прострочені</a> -->
</div>

<!-- Filters-->
    <form method="GET" action="{{ route('orders.index') }}" class="card mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">

            {{-- Тип пристрою --}}
            <div class="col-md-2">
                <label class="form-label">Тип пристрою</label>
                <select name="device" class="form-select">
                    <option value="">Усі</option>
                    @foreach($devices as $device)
                        <option value="{{ $device->id }}"
                            @selected(request('device') == $device->id)>
                            {{ $device->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Бренд --}}
            <div class="col-md-2">
                <label class="form-label">Бренд</label>
                <select name="brand" class="form-select">
                    <option value="">Усі</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}"
                            @selected(request('brand') == $brand->id)>
                            {{ $brand->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Статус --}}
            <div class="col-md-2">
                <label class="form-label">Статус</label>
                <select name="status" class="form-select">
                    <option value="">Усі</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}"
                            @selected(request('status') == $status->id)>
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Пошук --}}
            <div class="col-md-3">
                <input type="text"
                    name="phone"
                    value="{{ request('phone') }}"
                    class="form-control"
                    placeholder="Телефон клієнта">
            </div>


            {{-- Кнопки --}}
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary w-100">
                    Фільтрувати
                </button>

                <a href="{{ route('orders.index') }}"
                   class="btn btn-outline-secondary">
                    Скинути
                </a>
            </div>

        </div>
    </div>
</form>


    <!-- Таблиця із замовленнями -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Замовлення</h5>

            <a href="{{ route('orders.create') }}" class="btn btn-primary">
                + Нове замовлення
            </a>
        </div>

    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
            <tr>
                <th>ID</th>
                <th>Пристрій</th>
                <th>Статус</th>
                <th>Клієнт</th>
                <th>Створено</th>
                <th style="width: 160px;">Дії</th>
            </tr>
            </thead>

            <tbody>

            @forelse($orders as $order)
                <tr>
                    <td>
                        <strong>#{{ $order->id }}</strong>
                    </td>

                    <td>
                        {{ $order->device?->name ?? '—' }}
                        <br><h5><b>
                        <a href="/orders/{{ $order->id }}" class='color-primary, text-decoration-none' >
                            {{ $order->brand?->name ?? '—' }} {{ $order->device_model }}
                        </a></b></h5>
                    </td>

                    <td>
                        @php
                            $colors = [
                                '1' => 'secondary',
                                '2' => 'primary',
                                '3' => 'warning',
                                '4' => 'info',
                                '5' => 'dark',
                                '6' => 'primary',
                                '7' => 'success',
                                '8' => 'success',
                                '9' => 'danger',
                                '10' => 'secondary',
                            ];
                        @endphp

                        <span class="badge bg-{{ $colors[$order->status_id] ?? 'secondary' }}">
                            {{ $order->status->name ?? '—' }}
                        </span>
                    </td>

                    <td>
                        <a href="tel:{{ $order->counterparty?->contact?->phone ?? '—' }}" style="text-decoration:none;">
                        {{ $order->counterparty?->contact?->name ?? '—' }}<br>
                        <span style="color:#333333;font-weight: bold;"> 
                           {{ $order->counterparty?->contact?->phone ?? '—' }}
                        </span>
                        </a>
                    </td>
                    <td>
                        {{ $order->created_at?->format('d.m.Y H:i') }}
                    </td>

                    <td>
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                            Перегляд
                        </a>

                        <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-outline-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                                <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                                </svg>
                            <!-- Редагувати -->
                        </a>
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        Немає замовлень
                    </td>
                </tr>
            @endforelse

            </tbody>
        </table>
    </div>

    <div class="card-footer">
        <!-- Пагінація -->
        {{ $orders->links() }}
    </div>
</div>

</div>
@endsection
<!-- <script>
    $('#counterparty_id').select2({
    placeholder: 'Клієнт',
    allowClear: true,
    ajax: {
        url: '/ajax/counterparties',
        dataType: 'json',
        delay: 300,
        data: params => ({ q: params.term }),
        processResults: data => ({ results: data })
    }
});
</script>
<script>
    $('#brand_id').select2({
    placeholder: 'Бренд',
    allowClear: true
});
</script> -->