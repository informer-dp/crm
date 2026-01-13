<?php

namespace App\Http\Controllers;

use App\Models\OrderStatus;
use App\Models\Order;
use App\Models\Counterparty;
//use App\Models\Client;
use App\Models\CounterpartyGroup;
use Illuminate\Http\Request;
//use App\Models\OrderEstimation;
use Carbon\Carbon; // Для роботи з датами
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Device;
use App\Models\Brand;
use App\Services\ActivityService;
use App\Models\Estimate;

class OrderController extends Controller
{
    // app/Http/Controllers/OrderController.php
/*
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
$query = Order::query()->with('counterparty');

if(request('group_id')){
    $query->whereHas('counterparty', function($q){
        $q->where('group_id', request('group_id'));
    });
}

if(request('counterparty_id')){
    $query->where('counterparty_id', request('counterparty_id'));
}


    // Виконуємо запит
    $orders = $query->latest()->paginate(20);

    return view('orders.index', compact('orders'));
    
    
}
*/
public function index(Request $request)
{
    $groups = \App\Models\CounterpartyGroup::orderBy('name')->get();

    $counterparties = \App\Models\Counterparty::query()
        ->join('contacts', 'counterparties.contact_id', '=', 'contacts.id')
        ->select('counterparties.*')
        ->orderBy('contacts.name')
        ->get();

    $query = Order::query()
        ->with(['counterparty.contact', 'device','brand','status']);

    // 🔎 Фільтр по групі
    if ($request->filled('group_id')) {
        $query->whereHas('counterparty', function ($q) use ($request) {
            $q->where('group_id', $request->group_id);
        });
    }

    // 🔎 Фільтр по контрагенту
    if ($request->filled('counterparty_id')) {
        $query->where('counterparty_id', $request->counterparty_id);
    }

    $orders = $query->latest()->paginate(115)->withQueryString();

    return view('orders.index', compact(
        'orders',
        'groups',
        'counterparties'
    ));
}



    public function create()
{
    $counterparties = \App\Models\Counterparty::query()
        ->join('contacts', 'counterparties.contact_id', '=', 'contacts.id')
        ->select('counterparties.*')
        ->orderBy('contacts.name')
        ->get();

    $devices = \App\Models\Device::orderBy('name')->get();
    $brands = \App\Models\Brand::orderBy('name')->get();
   
    return view('orders.create', compact(
    'counterparties',
    'devices',
    'brands'
));

}

    

    public function store(Request $request)
{
    $data = $request->validate([
        'counterparty_id'   => ['required', 'exists:counterparties,id'],
        'device_id'         => ['required', 'exists:devices,id'],
        'brand_id'          => ['required', 'exists:brands,id'],
        'device_model'      => ['required', 'string', 'max:191'],
        'serial_number'     => ['nullable', 'string', 'max:191'],
         'equipment'        => ['nullable', 'string', 'max:191'],
        'problem_description' => ['required', 'string'],
    ], [
        'counterparty_id.required' => 'Оберіть клієнта',
        'device_id.required' => 'Оберіть тип пристрою',
        'brand_id.required' => 'Вкажіть бренд',
        'device_model.required' => 'Вкажіть модель',
        'equipment.required' => 'Вкажіть комплектацію',
        'problem_description.required' => 'Опишіть проблему',
    ]);


    $order = Order::create($data);
    
    ActivityService::log(
    $order,
    'created',
    'Створено нове замовлення'
);

    return redirect()
        ->route('orders.show', $order)
        ->with('success', 'Замовлення створено');
}


public function show(Order $order)
{
    $counterparty = $order->counterparty ?? null;

    $statuses = \App\Models\OrderStatus::orderBy('sort_order')->get();

    return view('orders.show', compact('order', 'counterparty', 'statuses'));
}



public function edit(Order $order)
{
    $counterparties = Counterparty::orderBy('id')->get();
    $devices = Device::orderBy('name')->get();
    $brands  = Brand::orderBy('name')->get();
    $statuses = OrderStatus::orderBy('sort_order')->get();
    return view('orders.edit', compact('counterparties','order','devices','brands','statuses'));

}
public function update(Request $request, Order $order)
{
    $request->validate([
        'counterparty_id' => 'required|exists:counterparties,id',
        'device_id' => 'required|exists:devices,id', // Перевірка, що поле обов’язкове і значення існує в таблиці devices
        'device_model' => 'required|string|max:255',
        'serial_number' => 'nullable|string|max:191',
        'equipment' => 'nullable|string',
        'problem_description' => 'required|string|max:1000',
        //'status' => 'required|exists:order_statuses,id',
        'price' => 'nullable|numeric|min:0',
    ]);

    $order->update($request->all());
            ActivityService::log(
            $order,
            'updated',
            'Оновлено дані замовлення'
        );
    return redirect()->route('orders.show', $order->id)->with('success', 'Замовлення оновлено.');
}

public function updateStatus(Request $request, Order $order)
{

    
    $request->validate([
        'status_id' => 'required|exists:order_statuses,id'
    ]);

    
$oldStatus = $order->status->name;
$order->update([
    'status_id' => $request->status_id
]);
 $order->load('status');
$newStatus = $order->status->name;

ActivityService::log(
    $order,
    'status_changed',
    "Змінено статус: {$oldStatus} → {$newStatus}"
);


    return back()->with('success', 'Статус оновлено');
}

// app/Http/Controllers/OrderController.php

public function storeEstimation(Request $request, Order $order)
{
    $data = $request->validate([
        'description' => 'required|string|max:255',
        'part_cost'   => 'nullable|numeric|min:0',
        'labor_cost'  => 'nullable|numeric|min:0',
    ]);

    $data['total_cost'] =
        ($data['part_cost'] ?? 0) + ($data['labor_cost'] ?? 0);

    $order->estimations()->create($data);
    ActivityService::log(
            $order,
            'updated',
            'Додано позицію в кошторис замовлення '
        );

    return back()->with('success', 'Позицію додано');
}


public function updateEstimation(Request $request, Estimate $estimation)
{
    $data = $request->validate([
        'description' => 'required|string|max:255',
        'part_cost'   => 'nullable|numeric|min:0',
        'labor_cost'  => 'nullable|numeric|min:0',
    ]);

    $data['total_cost'] =
        ($data['part_cost'] ?? 0) + ($data['labor_cost'] ?? 0);

    $order=$estimation['order_id'];
    $estimation->update($data);
        // ActivityService::log(
        // $order,
        // 'updated',
        // 'Оновлено позицію кошторису'
        // );

    return back()->with('success', 'Позицію оновлено');
}

public function destroyEstimation(Estimate $estimation)
{
    $estimation->delete();

    return back()->with('success', 'Позицію кошторису видалено');
}



}