<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;

class ClientIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $type = '';
    public bool $showVip = false;
    public bool $showBlacklisted = false;
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';

    protected $queryString = [
        'search'          => ['except' => ''],
        'type'            => ['except' => ''],
        'showVip'         => ['except' => false],
        'showBlacklisted' => ['except' => false],
    ];

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingType(): void { $this->resetPage(); }

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
        $this->reset(['search', 'type', 'showVip', 'showBlacklisted']);
        $this->resetPage();
    }

    public function render()
    {
        $clients = Client::query()
            ->withCount('orders')
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when($this->type, fn($q) => $q->where('type', $this->type))
            ->when($this->showVip, fn($q) => $q->where('is_vip', true))
            ->when($this->showBlacklisted, fn($q) => $q->where('is_blacklisted', true))
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(20);

        return view('livewire.clients.client-index', compact('clients'))
            ->extends('layouts.app')
            ->section('content');
    }
}