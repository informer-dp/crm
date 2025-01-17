@extends('layouts.app')

@section('content')
    <h1>Створити нового клієнта</h1>

    <form action="{{ route('clients.store') }}" method="POST">
        @csrf
        <label for="name">Ім'я</label>
        <input type="text" name="name" id="name" required>

        <label for="phone">Телефон</label>
        <input type="text" name="phone" id="phone" required>

        <label for="email">Email</label>
        <input type="email" name="email" id="email">

        <label for="address">Адреса</label>
        <textarea name="address" id="address"></textarea>

        <button type="submit">Створити клієнта</button>
    </form>
@endsection
