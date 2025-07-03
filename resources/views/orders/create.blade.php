@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Створити нове замовлення</h1>

    <form action="{{ route('orders.store') }}" method="POST">
        @csrf
        <label for="client_id">Клієнт</label>
        <select name="client_id" id="client_id">
            @foreach ($clients as $client)
                <option value="{{ $client->id }}">{{ $client->name }}</option>
            @endforeach
        </select>
        <div class="mb-3">
            <label for="device_id" class="form-label">Тип пристрою</label>
            <select name="device_id" id="device_id" class="form-select @error('device_id') is-invalid @enderror">
                <option value="" selected>Оберіть пристрій</option>
                @foreach ($devices as $device)
                    <option value="{{ $device->id }}" {{ isset($order) && $order->device_id == $device->id ? 'selected' : '' }}>
                        {{ $device->name }}
                    </option>
                @endforeach
            </select>
            @error('device_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <label>Бренд</label>
        <select name="brand_id" class="form-control">
        @foreach($brands as $brand)
            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
        @endforeach
        </select>

        <label for="device_model">Модель пристрою</label>
        <input type="text" name="device_model" id="device_model" required>

        <label for="problem_description">Опис проблеми</label>
        <textarea name="problem_description" id="problem_description"></textarea>
        <input type="hidden" name="status" value="Прийняте">


        <button type="submit">Створити замовлення</button>
    </form>
</div>
@endsection
