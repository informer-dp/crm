<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\EmployeeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::resource('clients', ClientController::class);
Route::resource('orders', OrderController::class);
Route::resource('parts', PartController::class);
Route::resource('employees', EmployeeController::class);
Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
Route::post('/orders/{order}/estimations', [OrderController::class, 'storeEstimation'])->name('orders.estimations.store');
// Редагування елемента кошторису
Route::patch('/orders/estimations/{estimation}', [OrderController::class, 'updateEstimation'])->name('orders.estimations.update');

// Видалення елемента кошторису
Route::delete('/orders/estimations/{estimation}', [OrderController::class, 'destroyEstimation'])->name('orders.estimations.destroy');
