@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Типи робіт</h1>

    {{-- Повідомлення про успішні операції --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Кнопка для створення нового типу роботи --}}
    <div class="mb-3">
        <a href="{{ route('work-types.create') }}" class="btn btn-primary">Додати тип роботи</a>
    </div>

    {{-- Таблиця зі списком типів робіт --}}
    @if($workTypes->count() > 0)
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Назва</th>
                    <th>Пристрій</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                @foreach($workTypes as $workType)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $workType->name }}</td>
                    <td>{{ $workType->device->name ?? 'Не вказано' }}</td>
                    <td>
                        {{-- Кнопки редагування та видалення --}}
                        <a href="{{ route('work-types.edit', $workType->id) }}" class="btn btn-warning btn-sm">Редагувати</a>

                        <form action="{{ route('work-types.destroy', $workType->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Ви впевнені, що хочете видалити цей тип роботи?')">Видалити</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Поки що типів робіт не додано.</p>
    @endif
</div>
@endsection
