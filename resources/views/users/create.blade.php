@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Додати нового користувача</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('users.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">Ім'я</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="form-group mt-3">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="form-group mt-3">
            <label for="password">Пароль</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="form-group mt-3">
            <label for="password_confirmation">Підтвердження паролю</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
        </div>

        <div class="form-group mt-3">
            <label for="role">Роль</label>
            <select name="role" id="role" class="form-control" required>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Додати користувача</button>
    </form>
</div>
@endsection
