<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\WorkTypeController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\BrandController;


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

Auth::routes(['register' => true]);
Route::get('/', [OrderController::class, 'index'])->middleware(['auth', 'role:admin'])->name('orders.index');
Route::get('/orders', [OrderController::class, 'index'])->middleware(['auth', 'role:admin'])->name('orders.index');
Route::resource('clients', ClientController::class)->middleware('auth');
//Route::resource('orders', OrderController::class);
Route::resource('parts', PartController::class);
Route::resource('employees', EmployeeController::class);
Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
Route::post('/orders/{order}/estimations', [OrderController::class, 'storeEstimation'])->name('orders.estimations.store');
// Редагування елемента кошторису
Route::patch('/orders/estimations/{estimation}', [OrderController::class, 'updateEstimation'])->name('orders.estimations.update');

// Видалення елемента кошторису
Route::delete('/orders/estimations/{estimation}', [OrderController::class, 'destroyEstimation'])->name('orders.estimations.destroy');
// Відображення форми редагування замовлення
Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');

// Оновлення даних замовлення
Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');



Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::group(['middleware' => ['auth', 'role:admin']], function () {
    Route::get('/admin', [AdminController::class, 'index']);
});

Route::group(['middleware' => ['auth', 'role:admin|manager|engineer']], function () {
    Route::resource('/orders', OrderController::class);
});
Route::group(['middleware' => ['auth', 'role:admin']], function () {
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
});


// Роут для довідника Пристрої
Route::resource('devices', DeviceController::class)->middleware('auth');

// Роут для довідника Типи робіт
Route::resource('work_types', WorkTypeController::class)->middleware('auth');

// Роут для довідника Клієнти
Route::resource('clients', ClientController::class)->middleware('auth');

//РОут для довідника "Бренди" 
Route::resource('brands', BrandController::class)->middleware('auth');