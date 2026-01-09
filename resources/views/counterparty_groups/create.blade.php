@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Додати групу контрагентів</h3>

    <form action="{{ route('counterparty_groups.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Назва *</label>
            <input type="text" name="name" class="form-control" required>
            @error('name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label>Опис</label>
            <textarea name="description" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label>Стандартна знижка (%)</label>
            <input type="number" name="default_discount" value="0" class="form-control">
        </div>

        <button class="btn btn-success">Зберегти</button>
        <a href="{{ route('counterparty_groups.index') }}" class="btn btn-secondary">Назад</a>
    </form>
</div>
@endsection
