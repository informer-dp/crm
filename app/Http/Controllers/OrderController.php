<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\OrderEstimation;

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
    if ($date) {
        $query->whereDate('created_at', $date);
    }

    // Виконуємо запит
    $orders = $query->latest()->paginate(10);

    return view('orders.index', compact('orders'));
}


    public function create()
    {
        return view('orders.create', ['clients' => Client::all()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'device_type' => 'required|string|in:Смартфон,Ноутбук,Планшет,ПК,Електронна книга,Навушники,Портативна колонка,Інше',
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

public function updateStatus(Request $request, $id)
{
    $order = Order::findOrFail($id);
    $request->validate([
        'status' => 'required|string',
    ]);
    $order->status = $request->input('status');
    $order->save();

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
