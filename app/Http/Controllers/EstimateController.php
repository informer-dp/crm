<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EstimateController extends Controller
{
    public function storeEstimation(Request $request, Order $order)
{
    $data = $request->validate([
        'description' => 'required|string',
        'part_cost'   => 'nullable|numeric|min:0',
        'labor_cost'  => 'nullable|numeric|min:0',
    ]);

    $data['total_cost'] =
        ($data['part_cost'] ?? 0) + ($data['labor_cost'] ?? 0);

    $order->estimates()->create($data);

    return back()->with('success', 'Позицію кошторису додано');
}

}
