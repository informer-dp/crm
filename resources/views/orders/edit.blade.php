@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Редагування замовлення #{{ $order->id }}</h2>

    <form action="{{ route('orders.update', $order->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Контрагент</label>
            <select name="counterparty_id" class="form-select" required>
                <option value="">— Оберіть контрагента —</option>

                @foreach($counterparties as $counterparty)
                    <option value="{{ $counterparty->id }}"
                        @selected($order->counterparty_id === $counterparty->id)>
                        {{ $counterparty->contact->name }}
                    </option>
                @endforeach
            </select>
        </div>

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

        <div class="form-group">
            <label for="device_model">Модель пристрою</label>
            <input type="text" name="device_model" id="device_model" class="form-control" value="{{ $order->device_model }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Серійний номер / IMEI</label>
            <input type="text"
                name="serial_number"
                class="form-control"
                value="{{ old('serial_number', $order->serial_number) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Комплектація</label>
            <textarea name="equipment"
                    class="form-control"
                    rows="2"
                    placeholder="Зарядний пристрій, сумка, кабель тощо">{{ old('equipment', $order->equipment) }}</textarea>
        </div>


        <div class="form-group">
            <label for="problem_description">Опис проблеми</label>
            <textarea name="problem_description" id="problem_description" class="form-control">{{ $order->problem_description }}</textarea>
        </div>

        <div class="form-group">
            <!-- <label for="price">Вартість</label>
            <input type="number" name="price" id="price" class="form-control" step="0.01" value="{{ $order->price }}"> -->
        </div>

        <button type="submit" class="btn btn-primary">Зберегти</button>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary">Скасувати</a>
    </form>
</div>
@endsection
