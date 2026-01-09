@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Редагування замовлення #{{ $order->id }}</h2>

    <form action="{{ route('orders.update', $order->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
           
        <select name="counterparty_id" class="form-control">
        @foreach($counterparties as $c)
        <!-- <option value="{{ $c->id }}">{{ $c->name }}</option> -->
                    <option value="{{ $c->id }}"
                        {{ request('counterparty_id') == $c->id ? 'selected' : '' }}>
                        {{ $c->contact->name ?? 'Без імені' }}
                    </option>
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

        <div class="form-group">
            <label for="device_model">Модель пристрою</label>
            <input type="text" name="device_model" id="device_model" class="form-control" value="{{ $order->device_model }}">
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
