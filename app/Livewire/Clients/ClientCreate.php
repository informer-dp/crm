<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;

class ClientCreate extends Component
{
    public string $clientType = 'individual';
    public string $name = '';
    public string $phone = '';
    public string $email = '';
    public string $taxCode = '';
    public string $contactPerson = '';
    public string $contactPhone = '';
    public string $city = '';
    public string $address = '';
    public string $notes = '';
    public bool $isVip = false;

    protected function rules(): array
    {
        return [
            'name'       => 'required|min:2',
            'phone'      => 'required|min:10',
            'email'      => 'nullable|email',
            'clientType' => 'required|in:individual,legal',
        ];
    }

    protected $messages = [
        'name.required'  => 'Введіть ім\'я або назву',
        'phone.required' => 'Введіть телефон',
        'email.email'    => 'Невірний формат email',
    ];

    public function updatedPhone(): void
    {
        $this->phone = $this->normalizePhone($this->phone);
    }

    private function normalizePhone(string $phone): string
    {
        $clean = preg_replace('/[^\d+]/', '', $phone);
        if (empty($clean)) return '';
        if (str_starts_with($clean, '0')) return '+38' . $clean;
        if (str_starts_with($clean, '380') && !str_starts_with($clean, '+')) return '+' . $clean;
        if (strlen($clean) === 9 && !str_starts_with($clean, '+')) return '+380' . $clean;
        return $clean;
    }

    public function save(): void
    {
        $this->validate();

        $client = Client::create([
            'type'           => $this->clientType,
            'name'           => $this->name,
            'phone'          => $this->phone,
            'email'          => $this->email ?: null,
            'tax_code'       => $this->taxCode ?: null,
            'contact_person' => $this->contactPerson ?: null,
            'contact_phone'  => $this->contactPhone ?: null,
            'city'           => $this->city ?: null,
            'address'        => $this->address ?: null,
            'notes'          => $this->notes ?: null,
            'is_vip'         => $this->isVip,
        ]);

        $this->redirect(route('clients.show', $client), navigate: false);
    }

    public function render()
    {
        return view('livewire.clients.client-create')
            ->extends('layouts.app')
            ->section('content');
    }
}