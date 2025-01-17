@extends('layouts.app')

@section('content')
    <h1>Клієнти</h1>
    <a href="{{ route('clients.create') }}">Додати нового клієнта</a>
    <table>
        <thead>
            <tr>
                <th>Ім'я</th>
                <th>Телефон</th>
                <th>Email</th>
                <th>Адреса</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($clients as $client)
                <tr>
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->phone }}</td>
                    <td>{{ $client->email }}</td>
                    <td>{{ $client->address }}</td>
                    <td>
                        <a href="#">Редагувати</a> | 
                        <form action="#" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Видалити</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
