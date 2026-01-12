<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\WorkTypeController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CounterpartyController;
use App\Http\Controllers\ContactController;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('contacts', ContactController::class);
    Route::resource('counterparties', CounterpartyController::class);
});

Auth::routes(['register' => false]);

/*
|--------------------------------------------------------------------------
| Головна
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('orders.index');
});

/*
|--------------------------------------------------------------------------
| Замовлення (всі працівники)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin|manager|engineer'])->group(function () {
    Route::resource('orders', OrderController::class);

    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])
        ->name('orders.updateStatus');

    Route::post('/orders/{order}/estimations', [OrderController::class, 'storeEstimation'])
        ->name('orders.estimations.store');

    Route::patch('/orders/estimations/{estimation}', [OrderController::class, 'updateEstimation'])
        ->name('orders.estimations.update');

    Route::delete('/orders/estimations/{estimation}', [OrderController::class, 'destroyEstimation'])->name('orders.estimations.destroy');

});

Route::middleware('auth')->get('/ajax/counterparty/{id}', function ($id) {
    $counterparty = \App\Models\Counterparty::with('contact')->findOrFail($id);

            return response()->json([
                'phone' => $counterparty->contact->phone,
                'email' => $counterparty->contact->email,
            ]);
    })->name('ajax.counterparty');
    
Route::get('/contacts/check-phone', [ContactController::class, 'checkPhone']) ->middleware('auth');

Route::middleware('auth')->post('/ajax/create-counterparty', function (\Illuminate\Http\Request $request) {

    $data = $request->validate([
        'name'  => 'required|string|max:191',
        'phone' => 'required|string|max:50',
        'email' => 'nullable|email|max:191',
    ]);

    // 1. Створюємо контакт
    $contact = \App\Models\Contact::create([
        'name' => $data['name'],
        'phone' => $data['phone'],
        'email' => $data['email'] ?? null,
    ]);

    // 2. Знаходимо групу "Клієнти"
    $group = \App\Models\CounterpartyGroup::where('name', 'LIKE', '%Клієнт%')->first();

    if (!$group) {
        $group = \App\Models\CounterpartyGroup::create([
            'name' => 'Клієнти',
        ]);
    }

    // 3. Створюємо контрагента
    $counterparty = \App\Models\Counterparty::create([
        'contact_id' => $contact->id,
        'group_id'   => $group->id,
    ]);

    return redirect()
        ->back()
        ->with('success', 'Клієнта додано')
        ->with('new_counterparty_id', $counterparty->id);
});

/*
|--------------------------------------------------------------------------
| Клієнти (admin + manager)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin|manager'])->group(function () {
    Route::resource('clients', ClientController::class);
});

/*
|--------------------------------------------------------------------------
| Адміністрування
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\CounterpartyGroupController;

Route::middleware(['auth','role:admin'])->group(function () {
    Route::resource('counterparty_groups', CounterpartyGroupController::class);
});

Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

    // Користувачі
    Route::resource('users', UserController::class)->only([
        'index', 'create', 'store'
    ]);

    // Довідники
    Route::resource('devices', DeviceController::class);
    Route::resource('work_types', WorkTypeController::class);
    Route::resource('brands', BrandController::class);
    Route::resource('parts', PartController::class);
    Route::resource('employees', EmployeeController::class);
});
