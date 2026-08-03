@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Пристрої</h1>

    {{-- Повідомлення про успішні операції --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Кнопка для створення нового пристрою --}}
    <div class="mb-3">
        <a href="{{ route('devices.create') }}" class="btn btn-primary">Додати пристрій</a>
    </div>

    {{-- Таблиця зі списком пристроїв --}}
    @if($devices->count() > 0)
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Назва</th>
                    <th>Опис</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                @foreach($devices as $device)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $device->name }}</td>
                    <td>{{ $device->description ?? 'Немає опису' }}</td>
                    <td>
                        {{-- Кнопки редагування та видалення --}}
                        <a href="{{ route('devices.edit', $device->id) }}" class="btn btn-warning btn-sm">Редагувати</a>

                        <form action="{{ route('devices.destroy', $device->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Ви впевнені, що хочете видалити цей пристрій?')">Видалити</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Поки що пристроїв не додано.</p>
    @endif
</div>
@endsection
