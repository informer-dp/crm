@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Бренди</h1>

    <a href="{{ route('brands.create') }}" class="btn btn-primary mb-3">Додати бренд</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($brands->count())
        <table class="table">
            <thead>
                <tr>
                    <th>Назва</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($brands as $brand)
                    <tr>
                        <td>{{ $brand->name }}</td>
                        <td>
                            <a href="{{ route('brands.edit', $brand) }}" class="btn btn-sm btn-warning">Редагувати</a>
                            <form action="{{ route('brands.destroy', $brand) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Ви впевнені, що хочете видалити цей бренд?')">
                                    Видалити
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Брендів ще немає.</p>
    @endif
</div>
@endsection
