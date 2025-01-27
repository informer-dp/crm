@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Редагувати пристрій</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('devices.update', $device->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Назва</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $device->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Опис</label>
            <textarea id="description" name="description" class="form-control">{{ old('description', $device->description) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Зберегти</button>
        <a href="{{ route('devices.index') }}" class="btn btn-secondary">Скасувати</a>
    </form>
</div>
@endsection
