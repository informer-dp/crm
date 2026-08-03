<?php

namespace App\Http\Controllers;

use App\Models\Order;

class PrintController extends Controller
{
    public function receipt(Order $order, string $type = 'acceptance')
    {
        $order->load([
            'client',
            'device.deviceType',
            'device.brand',
            'device.model',
            'manager',
            'estimate.works',
            'estimate.parts',
        ]);

        return view('print.receipt', compact('order', 'type'));
    }
        public function warranty(Order $order)
    {
        $order->load([
            'client',
            'device.brand',
            'device.model',
            'manager',
            'estimate.works',
        ]);

        return view('print.warranty', compact('order'));
    }
}