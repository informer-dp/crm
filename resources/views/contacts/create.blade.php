@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Додати контакт</h3>

    <form action="{{ route('contacts.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Імʼя *</label>
            <input type="text" name="name" class="form-control" required>
            @error('name')
               <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label>Телефон</label>
            <input type="text" name="phone" class="form-control">
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control">
        </div>

        <div class="mb-3">
            <label>Адреса</label>
            <textarea name="address" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label>Нотатки</label>
            <textarea name="notes" class="form-control"></textarea>
        </div>

        <button class="btn btn-success">Зберегти</button>
        <a href="{{ route('contacts.index') }}" class="btn btn-secondary">Назад</a>
    </form>
</div>
@endsection
