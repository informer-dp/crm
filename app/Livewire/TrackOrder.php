<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;

class TrackOrder extends Component
{
    public string $orderNumber = '';
    public string $checkCode = '';
    public ?Order $order = null;
    public bool $searched = false;
    public string $error = '';

    public function track(): void
    {
        $this->validate([
            'orderNumber' => 'required',
            'checkCode'   => 'required|digits:6',
        ], [
            'orderNumber.required' => 'Введіть номер замовлення',
            'checkCode.required'   => 'Введіть код перевірки',
            'checkCode.digits'     => 'Код перевірки — 6 цифр',
        ]);

        $this->order = Order::where('number', strtoupper(trim($this->orderNumber)))
            ->where('check_code', trim($this->checkCode))
            ->with([
                'device.brand',
                'device.model',
                'estimate',
                'publicComments',
            ])
            ->first();

        $this->searched = true;
        $this->error = $this->order ? '' : 'Замовлення не знайдено. Перевірте номер та код.';
    }

    public function render()
{
    return view('livewire.track-order')
        ->extends('layouts.track')
        ->section('content');
}
}