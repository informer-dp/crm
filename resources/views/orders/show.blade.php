@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Замовлення #{{ $order->id }}</h1>

    <div class="mb-3">
        <h3>Інформація про клієнта</h3>
        <p><strong>Ім'я:</strong> {{ $order->client->name }}</p>
        <p><strong>Телефон:</strong> {{ $order->client->phone }}</p>
        <p><strong>Email:</strong> {{ $order->client->email }}</p>
    </div>

    <div class="mb-3">
        <h3>Інформація про пристрій</h3>
        <p><strong>Тип:</strong> {{ $order->device_type }}</p>
        <p><strong>Бренд:</strong> {{ $order->device_brand }}</p>
        <p><strong>Модель:</strong> {{ $order->device_model }}</p>
        <p><strong>Опис проблеми:</strong> {{ $order->problem_description }}</p>
    </div>

    <div class="mb-3">
        <h3>Статус замовлення</h3>
        <p>{{ $order->status }}</p>
    </div>

    <a href="{{ route('orders.index') }}" class="btn btn-secondary">Назад</a>

<!--Offcanvas "ORDER STATUS CHANGE" start-->
<button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#orderStatusChange" aria-controls="orderStatusChange">
  Оновити статус
</button>

<div class="offcanvas offcanvas-end border-start border-5 border-primary" tabindex="-1" id="orderStatusChange" aria-labelledby="orderStatusChangeLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title border-bottom border-gray" id="orderStatusChangeLabel">Оновити статус замовлення</h5><hr>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div>
      <!--Some text as placeholder. In real life you can have the elements you have chosen. Like, text, images, lists, etc.-->
      <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST">
    @csrf
    @method('PATCH')

    <div class="form-group">
        <label for="status">Новий статус замовлення</label><br/>
        <select name="status" id="status" class="form-control">
            <option value="Прийняте" {{ $order->status == 'Прийняте' ? 'selected' : '' }}>Прийняте</option>
            <option value="Поставлене в роботу" {{ $order->status == 'Поставлене в роботу' ? 'selected' : '' }}>Поставлене в роботу</option>
            <option value="Діагностика" {{ $order->status == 'Діагностика' ? 'selected' : '' }}>Діагностика</option>
            <option value="Узгодження з клієнтом" {{ $order->status == 'Узгодження з клієнтом' ? 'selected' : '' }}>Узгодження з клієнтом</option>
            <option value="Очікування деталей" {{ $order->status == 'Очікування деталей' ? 'selected' : '' }}>Очікування деталей</option>
            <option value="Ремонт" {{ $order->status == 'Ремонт' ? 'selected' : '' }}>Ремонт</option>
            <option value="Готове" {{ $order->status == 'Готове' ? 'selected' : '' }}>Готове</option>
            <option value="Видане" {{ $order->status == 'Видане' ? 'selected' : '' }}>Видане</option>
            <option value="Скасоване" {{ $order->status == 'Скасоване' ? 'selected' : '' }}>Скасоване</option>
            <option value="Архівне" {{ $order->status == 'Архівне' ? 'selected' : '' }}>Архівне</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary mt-3">Оновити статус</button>
</form>
    </div>
  </div>
</div>
<!--Offcanvas "ORDER STATUS CHANGE" end-->
    

<div class="card m-3 border-5">
    <div class="card-body">
<h3>Кошторис</h3>

<table class="table">
    <thead>
        <tr>
            <th>Назва</th>
            <th>Кількість</th>
            <th>Ціна за одиницю</th>
            <th>Всього</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($order->estimations as $estimation)
            <tr>
                <td>{{ $estimation->item_name }}</td>
                <td>{{ $estimation->quantity }}</td>
                <td>{{ $estimation->price_per_unit }} грн</td>
                <td>{{ $estimation->quantity * $estimation->price_per_unit }} грн</td>
            </tr>
        @endforeach
    </tbody>
</table>

<h4>Загальна вартість: 
    {{ $order->estimations->sum(fn($item) => $item->quantity * $item->price_per_unit) }} грн
</h4>
</div>
</div>
<!--Offcanvas "ORDER ESTIMATION STORE" start-->

<button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#orderEstimationStore" aria-controls="orderEstimationStore">
  Додати позицію в кошторис
</button>

<div class="offcanvas offcanvas-end border-start border-5 border-primary" tabindex="-1" id="orderEstimationStore" aria-labelledby="orderEstimationUpdateLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title border-bottom border-2 border-gray" id="orderEstimationUpdateLabel"><strong>Додати позицію в кошторис</strong></h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div>
      <!--Some text as placeholder. In real life you can have the elements you have chosen. Like, text, images, lists, etc.-->
      <form action="{{ route('orders.estimations.store', $order->id) }}" method="POST" class="mt-4">
    @csrf
    <div class="form-group">
        <label for="item_name">Назва:</label>
        <input type="text" name="item_name" id="item_name" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="quantity">Кількість:</label>
        <input type="number" name="quantity" id="quantity" class="form-control" value="1" required>
    </div>
    <div class="form-group">
        <label for="price_per_unit">Ціна за одиницю:</label>
        <input type="number" step="0.01" name="price_per_unit" id="price_per_unit" class="form-control" required>
    </div><br/><br/>
    <button type="submit" class="btn btn-primary ">Додати в кошторис</button>
</form>
    </div>
  </div>
</div>
<!--Offcanvas "ORDER ESTIMATION STORE" end-->
<!--Offcanvas "ORDER ESTIMATION UPDATE" start-->

<button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#orderEstimationUpdate" aria-controls="orderEstimationUpdate">
  Редагувати кошторис
</button>

<div class="offcanvas offcanvas-end border-start border-5 border-primary w-auto" tabindex="-1" id="orderEstimationUpdate" aria-labelledby="orderEstimationUpdateLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="orderEstimationUpdateLabel">Редагування кошторису замовлення</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div>
      <!--Some text as placeholder. In real life you can have the elements you have chosen. Like, text, images, lists, etc.-->
      <div>
    @foreach ($order->estimations as $estimation)
    <div class="card mb-3">
        <div class="card-body bg-secondary-subtle">
        <div class="row mb-3">
            <div class="col">
                <form action="{{ route('orders.estimations.update', $estimation->id) }}" method="POST" class="form-floating">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="item_name" class="form-control" value="{{ $estimation->item_name }}" required>
                    <label for="item_name">Назва позиції</label>
            </div>
</div>
<div class="row mb-3">
            <div class="input-group mb-3">
                 <span class="input-group-text">Кількість</span>
                    <input size="5" type="number" name="quantity" class="form-control" value="{{ $estimation->quantity }}" required>
                    <span class="input-group-text">шт.</span>
            </div>
            <div class="input-group mb-3">
                    <span class="input-group-text">Ціна</span>
                    <input type="number" step="0.01" name="price_per_unit" class="form-control" value="{{ $estimation->price_per_unit }}" required>
                    <span class="input-group-text">грн.</span>
            </div>
            <!--<div class="col-auto" style="text-align: right;">
                {{ $estimation->quantity * $estimation->price_per_unit }} грн
            </div>-->
            <div class="col-auto">
                    <button type="submit" class="btn btn-success btn-sm">Оновити</button>
                </form>
            </div>
            <div class="col-auto">
                <form action="{{ route('orders.estimations.destroy', $estimation->id) }}" method="POST" onsubmit="return confirm('Ви впевнені, що хочете видалити цей елемент?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Видалити</button>
                </form>
        </div>
</div>
</div>
</div>
    @endforeach
</div>

    </div>
  </div>
</div>
<!--Offcanvas "ORDER ESTIMATION UPDATE" end-->

</div>
@endsection