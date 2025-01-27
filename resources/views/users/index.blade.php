@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Користувачі</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Ім'я</th>
                <th>Email</th>
                <th>Дата створення</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
