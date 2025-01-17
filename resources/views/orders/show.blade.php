@extends('layouts.app')

@section('content')
    <h1>Замовлення #{{ $order->id }}</h1>

    <div class="mb-3">
        <h3>Інформація про клієнта</h3>
        <p><strong>Ім'я:</strong> {{ $order->client->name }}</p>
        <p><strong>Телефон:</strong> {{ $order->client->phone }}</p>
        <p><strong>Email:</strong> {{ $order->client->email }}</p>
    </div>

    <div class="mb-3">
        <h3>Інформація про пристрій</h3>
        <p><strong>Тип:</strong> {{ $order->device_type }}</p>
        <p><strong>Бренд:</strong> {{ $order->device_brand }}</p>
        <p><strong>Модель:</strong> {{ $order->device_model }}</p>
        <p><strong>Опис проблеми:</strong> {{ $order->problem_description }}</p>
    </div>

    <div class="mb-3">
        <h3>Статус замовлення</h3>
        <p>{{ $order->status }}</p>
    </div>

    <a href="{{ route('orders.index') }}" class="btn btn-secondary">Назад</a>

    <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" class="mb-3">
    @csrf
    @method('PATCH')
    <div class="form-group">
        <label for="status">Статус замовлення:</label>
        <select name="status" id="status" class="form-control">
            <option value="Pending" {{ $order->status == 'Pending' ? 'selected' : '' }}>Очікує</option>
            <option value="In Progress" {{ $order->status == 'In Progress' ? 'selected' : '' }}>В роботі</option>
            <option value="Completed" {{ $order->status == 'Completed' ? 'selected' : '' }}>Виконано</option>
            <option value="Cancelled" {{ $order->status == 'Cancelled' ? 'selected' : '' }}>Скасовано</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Оновити статус</button>
</form>

<h3>Кошторис</h3>

<table class="table">
    <thead>
        <tr>
            <th>Назва</th>
            <th>Кількість</th>
            <th>Ціна за одиницю</th>
            <th>Всього</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($order->estimations as $estimation)
            <tr>
                <td>{{ $estimation->item_name }}</td>
                <td>{{ $estimation->quantity }}</td>
                <td>{{ $estimation->price_per_unit }} грн</td>
                <td>{{ $estimation->quantity * $estimation->price_per_unit }} грн</td>
            </tr>
        @endforeach
    </tbody>
</table>

<h4>Загальна вартість: 
    {{ $order->estimations->sum(fn($item) => $item->quantity * $item->price_per_unit) }} грн
</h4>

<form action="{{ route('orders.estimations.store', $order->id) }}" method="POST" class="mt-4">
    @csrf
    <div class="form-group">
        <label for="item_name">Назва:</label>
        <input type="text" name="item_name" id="item_name" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="quantity">Кількість:</label>
        <input type="number" name="quantity" id="quantity" class="form-control" value="1" required>
    </div>
    <div class="form-group">
        <label for="price_per_unit">Ціна за одиницю:</label>
        <input type="number" step="0.01" name="price_per_unit" id="price_per_unit" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Додати в кошторис</button>
</form>
<tbody>
    @foreach ($order->estimations as $estimation)
        <tr>
            <td>
                <form action="{{ route('orders.estimations.update', $estimation->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="item_name" class="form-control" value="{{ $estimation->item_name }}" required>
            </td>
            <td>
                    <input type="number" name="quantity" class="form-control" value="{{ $estimation->quantity }}" required>
            </td>
            <td>
                    <input type="number" step="0.01" name="price_per_unit" class="form-control" value="{{ $estimation->price_per_unit }}" required>
            </td>
            <td>
                {{ $estimation->quantity * $estimation->price_per_unit }} грн
            </td>
            <td>
                    <button type="submit" class="btn btn-success btn-sm">Оновити</button>
                </form>
            </td>
            <td>
                <form action="{{ route('orders.estimations.destroy', $estimation->id) }}" method="POST" onsubmit="return confirm('Ви впевнені, що хочете видалити цей елемент?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Видалити</button>
                </form>
            </td>
        </tr>
    @endforeach
</tbody>

@endsection