<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\OrderEstimation;
use Carbon\Carbon; // Для роботи з датами
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Device;

class OrderController extends Controller
{
    // app/Http/Controllers/OrderController.php

public function index(Request $request)
{
    // Отримуємо параметри для фільтрації
    $status = $request->input('status'); // Фільтр за статусом
    $date = $request->input('date');     // Фільтр за датою

    // Базовий запит для отримання всіх замовлень
    $query = Order::with('client');

    // Додаємо фільтрацію за статусом, якщо обрано
    if ($status) {
        $query->where('status', $status);
    }

    // Додаємо фільтрацію за датою, якщо вказано
    // Фільтрація за періодом
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
        $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();

        $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Виконуємо запит
    $orders = $query->latest()->paginate(10);

    return view('orders.index', compact('orders'));
}


    public function create()
    {
        //$devices = Device::all(); // Отримати всі доступні пристрої
        return view('orders.create', ['clients' => Client::all(),'devices' => Device::all()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'device_id' => 'required|exists:devices,id', // Перевірка, що поле обов’язкове і значення існує в таблиці devices
            'device_brand' => 'required|string|max:255',
            'device_model' => 'required|string|max:255',
            'problem_description' => 'required|string',
        ]);

        Order::create($request->all());

        return redirect()->route('orders.index')->with('success', 'Замовлення створене успішно.');
    }

public function show($id)
{
    $order = Order::with(['client', 'parts'])->findOrFail($id);

    return view('orders.show', compact('order'));
}

public function edit(Order $order)
{
    // Отримання клієнтів для випадаючого списку
    $clients = Client::all();
    $devices = Device::all(); 
    return view('orders.edit', compact('order', 'clients','devices'));
}
public function update(Request $request, Order $order)
{
    $request->validate([
        'client_id' => 'required|exists:clients,id',
        'device_id' => 'required|exists:devices,id', // Перевірка, що поле обов’язкове і значення існує в таблиці devices
        'device_model' => 'required|string|max:255',
        'problem_description' => 'required|string|max:1000',
        'status' => 'required|string|in:Прийняте,Поставлене в роботу,Діагностика,Узгодження з клієнтом,Очікування деталей,Ремонт,Готове,Видане,Скасоване,Архівне',
        'price' => 'nullable|numeric|min:0',
    ]);

    $order->update($request->all());

    return redirect()->route('orders.show', $order->id)->with('success', 'Замовлення оновлено.');
}

public function updateStatus(Request $request, $id)
{
    $order = Order::findOrFail($id);
    $request->validate([
       'status' => 'required|string|in:Прийняте,Поставлене в роботу,Діагностика,Узгодження з клієнтом,Очікування деталей,Ремонт,Готове,Видане,Скасоване,Архівне'
    ]);
    $order->status = $request->input('status');
    $order->save();
    $order->update(['status' => $request->status]);

    return redirect()->back()->with('success', 'Статус замовлення оновлено.');
}
// app/Http/Controllers/OrderController.php

public function storeEstimation(Request $request, $orderId)
{
    $request->validate([
        'item_name' => 'required|string|max:255',
        'quantity' => 'required|integer|min:1',
        'price_per_unit' => 'required|numeric|min:0.01',
    ]);

    $order = Order::findOrFail($orderId);

    $order->estimations()->create([
        'item_name' => $request->input('item_name'),
        'quantity' => $request->input('quantity'),
        'price_per_unit' => $request->input('price_per_unit'),
    ]);

    return redirect()->route('orders.show', $orderId)->with('success', 'Елемент додано до кошторису.');
}
public function updateEstimation(Request $request, $estimationId)
{
    $request->validate([
        'item_name' => 'required|string|max:255',
        'quantity' => 'required|integer|min:1',
        'price_per_unit' => 'required|numeric|min:0.01',
    ]);

    $estimation = OrderEstimation::findOrFail($estimationId);

    $estimation->update([
        'item_name' => $request->input('item_name'),
        'quantity' => $request->input('quantity'),
        'price_per_unit' => $request->input('price_per_unit'),
    ]);

    return redirect()->route('orders.show', $estimation->order_id)->with('success', 'Елемент кошторису оновлено.');
}
public function destroyEstimation($estimationId)
{
    $estimation = OrderEstimation::findOrFail($estimationId);

    $orderId = $estimation->order_id; // Запам'ятовуємо ID замовлення

    $estimation->delete();

    return redirect()->route('orders.show', $orderId)->with('success', 'Елемент кошторису видалено.');
}


}

/*if (auth()->check()) {
    // Користувач має роль Admin
} else {
    abort(403, 'У вас немає доступу до цієї сторінки.');
}
*/