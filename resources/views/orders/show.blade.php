@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>
            Замовлення #{{ $order->id }}
        </h4>
        <a href="{{ route('orders.edit', $order) }}" class="btn btn-primary">
                Редагувати
        </a>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
            ← До списку
        </a>
    </div>

    {{-- Вкладки --}}
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-main">
                Основна інформація
            </button>
        </li>

        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-estimate">
                Кошторис
            </button>
        </li>

        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-tasks">
                Завдання
            </button>
        </li>

        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-activity">
                Активності
            </button>
        </li>

        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-payments">
                Рух коштів
            </button>
        </li>
    </ul>

    <div class="tab-content">

        {{-- ================= Основна інформація ================= --}}
        <div class="tab-pane fade show active" id="tab-main">

            <div class="card">
                <div class="card-body">

                    <h5>Клієнт</h5>
                    <p>
                        <strong>{{ $order->counterparty?->contact?->name ?? '—' }}</strong><br>
                        {{ $order->counterparty?->contact?->phone ?? '' }}<br>
                        {{ $order->counterparty?->contact?->email ?? '' }}
                    </p>

                    <hr>

                    <h5>Пристрій</h5>

                    <p>
                        Тип: {{ $order->device?->name ?? '—' }}<br>
                        Бренд: {{ $order->brand?->name }}<br>
                        Модель: {{ $order->device_model }}<br>
                        Серійний номер: {{ $order->serial_number ?? '—' }}
                    </p>

                    <hr>

                    <h5>Статус</h5>
                    <!-- <span class="badge bg-primary">
                        {{ $order->status }}
                    </span> -->
                        <form method="POST" action="{{ route('orders.updateStatus', $order) }}">
                            @csrf
                            @method('PATCH')

                            <select name="status_id" class="form-control" required>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}"
                                        {{ $order->status_id == $status->id ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>

                            <button class="btn btn-primary mt-2">Оновити статус</button>
                        </form>

                    <hr>

                    <h5>Проблема</h5>
                    <div class="border rounded p-2">
                        {!! nl2br(e($order->problem_description)) !!}
                    </div>

                </div>
            </div>
        </div>

        {{-- ================= Кошторис ================= --}}
        <div class="tab-pane fade" id="tab-estimate">
            @include('orders.partials.estimation')
        </div>

        {{-- ================= Завдання ================= --}}
        <div class="tab-pane fade" id="tab-tasks">
            <div class="alert alert-info">
                Розділ "Завдання" буде реалізовано після затвердження Task-модуля.
            </div>
        </div>

        {{-- ================= Активності ================= --}}
        <div class="tab-pane fade" id="tab-activity">
            <div class="">
                <ul class="list-group">
                        @forelse($order->activities as $activity)
                            <li class="list-group-item">
                                <strong>
                                    {{ $activity->user->name ?? 'Система' }}
                                </strong>
                                —
                                {{ $activity->description }}

                                <div class="text-muted small">
                                    {{ $activity->created_at->format('d.m.Y H:i') }}
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">
                                Активностей ще немає
                            </li>
                        @endforelse
                    </ul>
            </div>
        </div>

        {{-- ================= Платежі ================= --}}
        <div class="tab-pane fade" id="tab-payments">
            <div class="alert alert-info">
                Тут буде рух коштів по замовленню.
            </div>
        </div>

    </div>
</div>
@endsection
