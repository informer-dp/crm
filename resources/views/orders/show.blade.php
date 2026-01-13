@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>
            Замовлення #{{ $order->id }}
        </h4>
        
        <a href="{{ route('orders.edit', $order) }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                                <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                                </svg>
               &nbsp; Редагувати
        </a>
         <a href="{{ route('orders.edit', $order) }}" class="btn btn-primary">
           <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer-fill" viewBox="0 0 16 16">
            <path d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2zm6 8H5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1"/>
            <path d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
            </svg>
               &nbsp; Друк квитанції
        </a>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
            ← До списку
        </a>
    </div>
<hr />
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
                    <div class="row">
<div class="col">
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
</div>
<div class="col">
<h5>Комплектація</h5>
 <div class="border rounded p-2">
 {!! nl2br(e($order->equipment)) !!}
</div>
</div>
</div>
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
                Тут буде відображення коштів по замовленню.
            </div>
        </div>

    </div>
</div>
@endsection
