<?php

namespace App\Livewire\Orders;

use App\Models\Brand;
use App\Models\Client;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\DeviceType;
use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\Computed;

class OrderCreate extends Component
{
    // ── Клієнт ────────────────────────────────
    public string $clientSearch = '';
    public ?int $clientId = null;
    public string $clientName = '';
    public string $clientPhone = '';
    public string $clientEmail = '';
    public string $clientType = 'individual';
    public bool $showClientForm = false;
    public bool $showClientDropdown = false;

    // ── Пристрій ──────────────────────────────
    public ?int $deviceTypeId = null;
    public ?int $brandId = null;
    public mixed $modelId = null;
    public string $modelSearch = '';
    public bool $showModelDropdown = false;
    public bool $showNewModel = false;
    public string $newModelName = '';
    public string $serialNumber = '';
    public string $imei = '';
    public string $color = '';
    public string $appearance = '';

    // ── Заявка ────────────────────────────────
    public string $type = 'repair';
    public string $priority = 'normal';
    public string $malfunction = '';
    public string $notes = '';
    public string $estimatedDate = '';
    public string $prepayment = '';

    protected function rules(): array
    {
        return [
            'clientId'      => 'required|exists:clients,id',
            'deviceTypeId'  => 'required|exists:device_types,id',
            'brandId'       => 'required|exists:brands,id',
            'modelId'       => 'required|exists:models,id',
            'type'          => 'required|in:repair,express,diagnostic,maintenance',
            'priority'      => 'required|in:normal,urgent',
            'malfunction'   => 'required|min:3',
            'estimatedDate' => 'nullable|date|after:today',
            'prepayment'    => 'nullable|numeric|min:0',
        ];
    }

    protected $messages = [
        'clientId.required'     => 'Оберіть або створіть клієнта',
        'deviceTypeId.required' => 'Оберіть тип пристрою',
        'brandId.required'      => 'Оберіть бренд',
        'modelId.required'      => 'Оберіть модель',
        'malfunction.required'  => 'Опишіть несправність',
        'malfunction.min'       => 'Опис має бути не менше 3 символів',
    ];

    // ── Computed properties ────────────────────

    #[Computed]
    public function deviceTypes()
    {
        return DeviceType::active()->orderBy('name')->get();
    }

    #[Computed]
    public function brands()
    {
        if (!$this->deviceTypeId) return collect();
        return Brand::active()
            ->forType($this->deviceTypeId)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function models()
    {
        if (!$this->brandId) return collect();
        return DeviceModel::active()
            ->forBrand($this->brandId)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function clientResults()
    {
        if (strlen($this->clientSearch) < 2) return collect();
        return Client::search($this->clientSearch)
            ->limit(5)
            ->get();
    }
    #[Computed]
    public function filteredModels()
    {
        if (!$this->brandId) return collect();
        return DeviceModel::active()
            ->forBrand($this->brandId)
            ->when($this->modelSearch, fn($q) => 
                $q->where('name', 'like', '%' . $this->modelSearch . '%')
            )
            ->orderBy('name')
            ->limit(10)
            ->get();
    }

    public function updatedModelSearch(): void
    {
        $this->showModelDropdown = true;
        $this->modelId = null;
        unset($this->filteredModels);
    }

    public function selectModel(int $id, string $name): void
    {
        $this->modelId = $id;
        $this->modelSearch = $name;
        $this->showModelDropdown = false;
    }

    public function clearModel(): void
    {
        $this->modelId = null;
        $this->modelSearch = '';
        $this->showModelDropdown = false;
    }

    // ── Watchers ───────────────────────────────

    public function updatedDeviceTypeId(): void
    {
        $this->brandId = null;
        $this->modelId = null;
        unset($this->brands);
        unset($this->models);
    }

    public function updatedBrandId(): void
    {
        $this->modelId = null;
        $this->modelSearch = '';
        $this->showModelDropdown = false;
        unset($this->models);
        unset($this->filteredModels);
    }

    public function updatedClientSearch(): void
    {
        $this->showClientDropdown = strlen($this->clientSearch) >= 2;
        unset($this->clientResults);
    }

    // ── Дії з клієнтом ────────────────────────

    public function selectClient(int $id): void
    {
        $client = Client::find($id);
        if (!$client) return;

        $this->clientId = $client->id;
        $this->clientSearch = $client->name . ' · ' . $client->phone;
        $this->showClientDropdown = false;
        $this->showClientForm = false;
    }

    public function createNewClient(): void
    {
        $this->showClientForm = true;
        $this->showClientDropdown = false;
        $this->clientId = null;
    }

    public function saveClient(): void
    {
        $this->validate([
            'clientName'  => 'required|min:2',
            'clientPhone' => 'required|min:10',
        ], [
            'clientName.required'  => 'Введіть ім\'я клієнта',
            'clientPhone.required' => 'Введіть номер телефону',
        ]);

        $client = Client::create([
            'type'  => $this->clientType,
            'name'  => $this->clientName,
            'phone' => $this->clientPhone,
            'email' => $this->clientEmail ?: null,
        ]);

        $this->selectClient($client->id);
        $this->showClientForm = false;
    }

    public function clearClient(): void
    {
        $this->clientId = null;
        $this->clientSearch = '';
        $this->showClientForm = false;
        $this->showClientDropdown = false;
    }

    // ── Дії з моделлю пристрою ────────────────

    public function saveNewModel(): void
    {
        $this->validate([
            'newModelName' => 'required|min:2',
        ], [
            'newModelName.required' => 'Введіть назву моделі',
        ]);

        $model = DeviceModel::create([
            'brand_id'       => $this->brandId,
            'device_type_id' => $this->deviceTypeId,
            'name'           => $this->newModelName,
        ]);

        $this->modelId = $model->id;
        $this->showNewModel = false;
        $this->newModelName = '';
        unset($this->models);
    }

    // ── Збереження заявки ─────────────────────

    public function save(): void
    {
        $this->validate();

        // Знаходимо або створюємо пристрій
        $device = Device::create([
            'device_type_id' => $this->deviceTypeId,
            'brand_id'       => $this->brandId,
            'model_id'       => $this->modelId,
            'serial_number'  => $this->serialNumber ?: null,
            'imei'           => $this->imei ?: null,
            'color'          => $this->color ?: null,
            'appearance'     => $this->appearance ?: null,
        ]);

        $order = Order::create([
            'client_id'      => $this->clientId,
            'device_id'      => $device->id,
            'manager_id'     => auth()->id(),
            'type'           => $this->type,
            'priority'       => $this->priority,
            'malfunction'    => $this->malfunction,
            'notes'          => $this->notes ?: null,
            'estimated_date' => $this->estimatedDate ?: null,
            'prepayment'     => $this->prepayment ?: 0,
        ]);

        $this->redirect(route('orders.show', $order), navigate: false);
    }

    public function render()
    {
        return view('livewire.orders.order-create')
            ->extends('layouts.app')
            ->section('content');
    }
    public function updatedModelId($value): void
{
    if ($value === 'new') {
        $this->modelId = null;
        $this->showNewModel = true;
    }
}
public function updatedClientPhone(): void
{
    $this->clientPhone = $this->normalizePhone($this->clientPhone);
}

private function normalizePhone(string $phone): string
{
    // Видаляємо все крім цифр і +
    $clean = preg_replace('/[^\d+]/', '', $phone);
    
    if (empty($clean)) return '';
    
    // 0501234567 → +380501234567
    if (str_starts_with($clean, '0')) {
        return '+38' . $clean;
    }
    
    // 380501234567 → +380501234567
    if (str_starts_with($clean, '380') && !str_starts_with($clean, '+')) {
        return '+' . $clean;
    }
    
    // 501234567 → +380501234567
    if (strlen($clean) === 9 && !str_starts_with($clean, '+')) {
        return '+380' . $clean;
    }
    
    return $clean;
}
}