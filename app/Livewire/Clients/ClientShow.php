<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;

class ClientShow extends Component
{
    use WithPagination;

    public Client $client;
    public bool $editing = false;

    // Поля редагування
    public string $name = '';
    public string $phone = '';
    public string $email = '';
    public string $type = '';
    public string $address = '';
    public string $notes = '';
    public bool $isVip = false;
    public bool $isBlacklisted = false;
    public string $blacklistReason = '';

    public function mount(Client $client): void
    {
        $this->client = $client;
    }

    public function startEditing(): void
    {
        $this->name            = $this->client->name;
        $this->phone           = $this->client->phone;
        $this->email           = $this->client->email ?? '';
        $this->type            = $this->client->type;
        $this->address         = $this->client->address ?? '';
        $this->notes           = $this->client->notes ?? '';
        $this->isVip           = $this->client->is_vip;
        $this->isBlacklisted   = $this->client->is_blacklisted;
        $this->blacklistReason = $this->client->blacklist_reason ?? '';
        $this->editing         = true;
    }

    public function save(): void
    {
        $this->validate([
            'name'  => 'required|min:2',
            'phone' => 'required|min:10',
            'email' => 'nullable|email',
        ], [
            'name.required'  => 'Введіть ім\'я',
            'phone.required' => 'Введіть телефон',
            'email.email'    => 'Невірний формат email',
        ]);

        $this->client->update([
            'name'             => $this->name,
            'phone'            => $this->phone,
            'email'            => $this->email ?: null,
            'type'             => $this->type,
            'address'          => $this->address ?: null,
            'notes'            => $this->notes ?: null,
            'is_vip'           => $this->isVip,
            'is_blacklisted'   => $this->isBlacklisted,
            'blacklist_reason' => $this->isBlacklisted ? $this->blacklistReason : null,
        ]);

        $this->client->refresh();
        $this->editing = false;
    }

    public function cancelEditing(): void
    {
        $this->editing = false;
    }

    public function render()
    {
        $orders = $this->client->orders()
            ->with(['device.brand', 'device.model', 'estimate'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.clients.client-show', compact('orders'))
            ->extends('layouts.app')
            ->section('content');
    }
}