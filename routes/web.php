<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Orders\OrderIndex;
use App\Livewire\Orders\OrderCreate;
use App\Livewire\Orders\OrderShow;
use App\Livewire\Orders\OrderEdit;
use App\Livewire\Clients\ClientIndex;
use App\Livewire\Clients\ClientCreate;
use App\Livewire\Clients\ClientShow;
use App\Livewire\Inventory\PartIndex;
use App\Livewire\Inventory\PurchaseIndex;
use App\Livewire\Finance\FinanceIndex;
use App\Livewire\Salary\SalaryIndex;
use App\Livewire\Users\UserIndex;
use App\Livewire\Settings\SettingsIndex;

// ──────────────────────────────────────────
// Публічні роути
// ──────────────────────────────────────────
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Перевірка статусу замовлення без реєстрації
Route::get('/track', \App\Livewire\TrackOrder::class)->name('track');

// ──────────────────────────────────────────
// Захищені роути
// ──────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    Route::get('/', fn() => redirect()->route('orders.index'));
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // Заявки
    Route::get('/orders', OrderIndex::class)->name('orders.index');
    Route::get('/orders/create', OrderCreate::class)->name('orders.create');
    Route::get('/orders/{order}', OrderShow::class)->name('orders.show');
    Route::get('/orders/{order}/edit', OrderEdit::class)->name('orders.edit');

    // Клієнти
    Route::get('/clients', ClientIndex::class)->name('clients.index');
    Route::get('/clients/create', ClientCreate::class)->name('clients.create');
    Route::get('/clients/{client}', ClientShow::class)->name('clients.show');

    // Склад
    Route::get('/inventory', PartIndex::class)->name('inventory.index');
    Route::get('/purchases', PurchaseIndex::class)->name('purchases.index');

    // Фінанси
    Route::get('/finance', FinanceIndex::class)->name('finance.index');
    Route::get('/salary', SalaryIndex::class)->name('salary.index');

    // Користувачі (тільки адмін)
    Route::get('/users', UserIndex::class)
        ->middleware('can:users.view')
        ->name('users.index');

    // Налаштування (тільки адмін)
    Route::get('/settings', SettingsIndex::class)
        ->middleware('can:settings.manage')
        ->name('settings.index');
    // Друк
    Route::get('/orders/{order}/print/warranty', [App\Http\Controllers\PrintController::class, 'warranty'])
        ->name('orders.print.warranty'); 
    Route::get('/orders/{order}/print/{type}', [App\Http\Controllers\PrintController::class, 'receipt'])
        ->name('orders.print');
       
});