@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h3>Контакти</h3>
        <a href="{{ route('contacts.create') }}" class="btn btn-primary">Додати контакт</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>#</th>
            <th>Імʼя</th>
            <th>Телефон</th>
            <th>Email</th>
            <th>Адреса</th>
        </tr>
        </thead>

        <tbody>
        @forelse($contacts as $contact)
            <tr>
                <td>{{ $contact->id }}</td>
                <td>{{ $contact->name }}</td>
                <td>{{ $contact->phone }}</td>
                <td>{{ $contact->email }}</td>
                <td>{{ $contact->address }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted">Немає записів</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{ $contacts->links() }}
</div>
@endsection
