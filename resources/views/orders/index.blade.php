@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="border-bottom border-gray">Замовлення</h1>

    <!-- Фільтри -->
   
<form method="GET" action="{{ route('orders.index') }}" class="mb-3">

    <div class="row">

        {{-- Група контрагентів --}}
        <div class="col-md-4">
            <label>Група контрагентів</label>
            <select name="group_id" class="form-control">
                <option value="">— Усі групи —</option>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}"
                        {{ request('group_id') == $group->id ? 'selected' : '' }}>
                        {{ $group->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Контрагент --}}
        <div class="col-md-4">
            <label>Контрагент</label>
            <select name="counterparty_id" class="form-control">
                <option value="">— Усі контрагенти —</option>
                @foreach($counterparties as $c)
                    <option value="{{ $c->id }}"
                        {{ request('counterparty_id') == $c->id ? 'selected' : '' }}>
                        {{ $c->contact->name ?? 'Без імені' }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Кнопки --}}
        <div class="col-md-4 d-flex align-items-end">
            <button class="btn btn-primary me-2">Фільтрувати</button>

            <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                Скинути
            </a>
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
                <th>Клієнт / Контрагент</th>
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
                        {{ $order->counterparty?->contact?->name ?? '—' }}<br>
                        <small class="text-muted">
                            {{ $order->counterparty?->group?->name ?? '' }}
                        </small>
                    </td>
                    <td>
                        {{ $order->created_at?->format('d.m.Y H:i') }}
                    </td>

                    <td>
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                            Перегляд
                        </a>

                        <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-outline-secondary">
                            Редагувати
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
        {{ $orders->links() }}
    </div>
</div>


    <!-- Пагінація -->
    {{ $orders->links() }}
</div>
@endsection
