@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h3>Контрагенти</h3>
        <a href="{{ route('counterparties.create') }}" class="btn btn-primary">Додати контрагента</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>#</th>
            <th>Контакт</th>
            <th>Тип</th>
            <th>Знижка</th>
            <th>Статус</th>
        </tr>
        </thead>

        <tbody>
        @forelse($counterparties as $c)
            <tr>
                <td>{{ $c->id }}</td>
                <td>{{ $c->contact->name ?? '—' }}</td>
                <td>
                    @switch($c->type)
                        @case('client') Клієнт @break
                        @case('supplier') Постачальник @break
                        @case('contractor') Підрядник @break
                        @case('partner') Партнер @break
                        @default Інший
                    @endswitch
                </td>
                <td>{{ $c->personal_discount }} %</td>
                <td>
                    @if($c->is_active)
                        <span class="badge bg-success">Активний</span>
                    @else
                        <span class="badge bg-secondary">Архів</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted">Немає записів</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{ $counterparties->links() }}
</div>
@endsection
