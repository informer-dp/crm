@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="border-bottom border-gray">Замовлення   <a href="{{ route('orders.create') }}" class="btn btn-warning btn-sm">Створити</a></h1>

    <!-- Фільтри -->
    <form action="{{ route('orders.index') }}" method="GET" class="mb-4 p-2 border border-primary">
        <div class="row">
            <div class="col">
            <label for="status">Статус</label>
                <select name="status" class="form-control">
                    <option value="">Всі статуси</option>
                    <option value="Pending">Очікує</option>
                    <option value="In Progress">В роботі</option>
                    <option value="Completed">Виконано</option>
                    <option value="Cancelled">Скасовано</option>
                </select>
            </div>
        <div class="col">
            <label for="start_date">Початкова дата</label>
            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div class="col">
            <label for="end_date">Кінцева дата</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
            <div class="col">
                <button type="submit" class="btn btn-primary">Застосувати фільтри</button>
            </div>
        </div>
    </form>

    <!-- Таблиця із замовленнями -->
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Клієнт</th>
                <th>Тип пристрою</th>
                <th>Модель</th>
                <th>Статус</th>
                <th>Дата створення</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->client->name }}</td>
                    <td>{{ $order->device_type }}</td>
                    <td>{{ $order->device_model }}</td>
                    <td>{{ $order->status }}</td>
                    <td>{{ $order->created_at->format('d.m.Y') }}</td>
                    <td>
                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info btn-sm">Деталі</a>
                        <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-warning btn-sm">Редагувати</a>

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Пагінація -->
    {{ $orders->links() }}
</div>
@endsection
