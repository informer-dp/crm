<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

//#[Layout('layouts.app')]
//#[Title('Заявки')]
//#[Layout('layouts.livewire')]
class OrderIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = 'active';
    public string $type = '';
    public string $priority = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';
    public string $deviceType = '';

    protected $queryString = [
        'search'   => ['except' => ''],
        'status'   => ['except' => ''],
        'type'     => ['except' => ''],
        'priority' => ['except' => ''],
        'deviceType' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }
    public function updatingDeviceType(): void
{
    $this->resetPage();
}

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'status', 'type', 'priority', 'deviceType']);
        $this->resetPage();
    }

    public function render()
{
    $orders = Order::query()
        ->with(['client', 'device.brand', 'device.model', 'engineers'])
        ->when($this->search, fn($q) => $q->search($this->search))
        ->when($this->status === 'active', fn($q) => $q->active())
        ->when($this->status && $this->status !== 'active', fn($q) => $q->where('status', $this->status))
        ->when($this->deviceType, fn($q) => 
                $q->whereHas('device', fn($d) => 
                    $d->whereHas('deviceType', fn($t) => 
                        $t->where('name', 'like', '%' . $this->deviceType . '%')
                        ->orWhere('id', $this->deviceType)
                    )
                )
            )
        ->when($this->type, fn($q) => $q->where('type', $this->type))
        ->when($this->priority, fn($q) => $q->where('priority', $this->priority))
        ->orderByRaw("CASE WHEN priority = 'urgent' THEN 0 ELSE 1 END")
        ->orderBy($this->sortBy, $this->sortDir)
        ->paginate(20);

    return view('livewire.orders.order-index', compact('orders'))
        ->extends('layouts.app')
        ->section('content');
}
}