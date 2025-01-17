@extends('layouts.app')

@section('content')
    <h1>Створити нове замовлення</h1>

    <form action="{{ route('orders.store') }}" method="POST">
        @csrf
        <label for="client_id">Клієнт</label>
        <select name="client_id" id="client_id">
            @foreach ($clients as $client)
                <option value="{{ $client->id }}">{{ $client->name }}</option>
            @endforeach
        </select>
        <div class="form-group">
    <label for="device_type">Тип пристрою</label>
    <select name="device_type" id="device_type" class="form-control" required>
        <option value="" disabled selected>Оберіть тип пристрою</option>
        <option value="Смартфон">Смартфон</option>
        <option value="Ноутбук">Ноутбук</option>
        <option value="Планшет">Планшет</option>
        <option value="ПК">ПК</option>
        <option value="Електронна книга">Електронна книга</option>
        <option value="Навушники">Навушники</option>
        <option value="Портативна колонка">Портативна колонка</option>
        <option value="Інше">Інше</option>
    </select>
</div>
        <label for="device_brand">Бренд пристрою</label>
        <input type="text" name="device_brand" id="device_brand" required>

        <label for="device_model">Модель пристрою</label>
        <input type="text" name="device_model" id="device_model" required>

        <label for="problem_description">Опис проблеми</label>
        <textarea name="problem_description" id="problem_description"></textarea>

        <button type="submit">Створити замовлення</button>
    </form>
@endsection
