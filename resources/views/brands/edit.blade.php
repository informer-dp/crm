@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Редагувати бренд</h1>

    <form action="{{ route('brands.update', $brand) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Назва бренду</label>
            <input type="text" name="name" id="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $brand->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Зберегти</button>
        <a href="{{ route('brands.index') }}" class="btn btn-secondary">Скасувати</a>
    </form>
</div>
@endsection
