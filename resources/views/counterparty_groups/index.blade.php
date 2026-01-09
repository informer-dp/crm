@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h3>Групи контрагентів</h3>
        <a href="{{ route('counterparty_groups.create') }}" class="btn btn-primary">
            Додати групу
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>#</th>
            <th>Назва</th>
            <th>Опис</th>
            <th>Стандартна знижка</th>
        </tr>
        </thead>

        <tbody>
        @forelse($groups as $g)
            <tr>
                <td>{{ $g->id }}</td>
                <td>{{ $g->name }}</td>
                <td>{{ $g->description }}</td>
                <td>{{ $g->default_discount }}%</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center">Немає записів</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{ $groups->links() }}
</div>
@endsection
