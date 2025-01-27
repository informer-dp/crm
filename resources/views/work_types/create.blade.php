@extends('layouts.app')
@section('content')
<div class="container">
<form action="{{ route('work-types.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="name" class="form-label">Назва типу роботи</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>
    <div class="mb-3">
        <label for="device_id" class="form-label">Тип пристрою</label>
        <select class="form-select" id="device_id" name="device_id" required>
            @foreach ($devices as $device)
                <option value="{{ $device->id }}">{{ $device->name }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Зберегти</button>
</form>

</div>
@endsection
