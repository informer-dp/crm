@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Додати контрагента</h3>

    <form action="{{ route('counterparties.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Контакт *</label>
            <select name="contact_id" class="form-control" required>
                <option value="">— Оберіть контакт —</option>
                @foreach($contacts as $contact)
                    <option value="{{ $contact->id }}">{{ $contact->name }} ({{ $contact->phone }})</option>
                @endforeach
            </select>
            @error('contact_id')
               <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label>Тип *</label>
            <select name="type" class="form-control" required>
                <option value="client">Клієнт</option>
                <option value="supplier">Постачальник</option>
                <option value="contractor">Підрядник</option>
                <option value="partner">Партнер</option>
                <option value="other">Інший</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Персональна знижка (%)</label>
            <input type="number" name="personal_discount" value="0" class="form-control">
        </div>

        <div class="mb-3">
            <label>
                <input type="checkbox" name="is_active" value="1" checked>
                Активний
            </label>
        </div>

        <button class="btn btn-success">Зберегти</button>
        <a href="{{ route('counterparties.index') }}" class="btn btn-secondary">Назад</a>
    </form>
</div>
@endsection
