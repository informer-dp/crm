@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Додати бренд</h1>

    <form action="{{ route('brands.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Назва бренду</label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Додати</button>
        <a href="{{ route('brands.index') }}" class="btn btn-secondary">Скасувати</a>
    </form>
</div>
@endsection
