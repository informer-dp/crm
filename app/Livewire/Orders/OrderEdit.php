<?php

namespace App\Livewire\Orders;

use App\Models\Brand;
use App\Models\Client;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\DeviceType;
use App\Models\Order;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Computed;

class OrderEdit extends Component
{
    public Order $order;

    // ── Клієнт ────────────────────────────────
    public string $clientSearch = '';
    public ?int $clientId = null;
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
    public string $diagnosis = '';
    public string $notes = '';
    public string $estimatedDate = '';
    public string $prepayment = '';

    public function mount(Order $order): void
    {
        $this->order = $order->load([
            'client',
            'device.deviceType',
            'device.brand',
            'device.model',
        ]);

        // Заповнюємо поля клієнта
        $this->clientId     = $order->client_id;
        $this->clientSearch = $order->client->name . ' · ' . $order->client->phone;

        // Заповнюємо поля пристрою
        $this->deviceTypeId  = $order->device->device_type_id;
        $this->brandId       = $order->device->brand_id;
        $this->modelId       = $order->device->model_id;
        $this->modelSearch   = $order->device->model?->name ?? '';
        $this->serialNumber  = $order->device->serial_number ?? '';
        $this->imei          = $order->device->imei ?? '';
        $this->color         = $order->device->color ?? '';
        $this->appearance    = $order->device->appearance ?? '';

        // Заповнюємо поля заявки
        $this->type          = $order->type;
        $this->priority      = $order->priority;
        $this->malfunction   = $order->malfunction;
        $this->diagnosis     = $order->diagnosis ?? '';
        $this->notes         = $order->notes ?? '';
        $this->estimatedDate = $order->estimated_date?->format('Y-m-d') ?? '';
        $this->prepayment    = $order->prepayment > 0 ? (string)$order->prepayment : '';
    }

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
            'estimatedDate' => 'nullable|date',
            'prepayment'    => 'nullable|numeric|min:0',
        ];
    }

    protected $messages = [
        'clientId.required'     => 'Оберіть клієнта',
        'deviceTypeId.required' => 'Оберіть тип пристрою',
        'brandId.required'      => 'Оберіть бренд',
        'modelId.required'      => 'Оберіть модель',
        'malfunction.required'  => 'Опишіть несправність',
        'malfunction.min'       => 'Опис має бути не менше 3 символів',
    ];

    // ── Computed ───────────────────────────────

    #[Computed]
    public function deviceTypes()
    {
        return DeviceType::active()->orderBy('name')->get();
    }

    #[Computed]
    public function brands()
    {
        if (!$this->deviceTypeId) return collect();
        return Brand::active()->forType($this->deviceTypeId)->orderBy('name')->get();
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

    #[Computed]
    public function clientResults()
    {
        if (strlen($this->clientSearch) < 2) return collect();
        return Client::search($this->clientSearch)->limit(5)->get();
    }

    // ── Watchers ───────────────────────────────

    public function updatedDeviceTypeId(): void
    {
        $this->brandId = null;
        $this->modelId = null;
        $this->modelSearch = '';
        unset($this->brands);
        unset($this->filteredModels);
    }

    public function updatedBrandId(): void
    {
        $this->modelId = null;
        $this->modelSearch = '';
        unset($this->filteredModels);
    }

    public function updatedClientSearch(): void
    {
        $this->showClientDropdown = strlen($this->clientSearch) >= 2;
        unset($this->clientResults);
    }

    public function updatedModelSearch(): void
    {
        $this->showModelDropdown = strlen($this->modelSearch) > 0;
        $this->modelId = null;
        unset($this->filteredModels);
    }

    // ── Дії з клієнтом ────────────────────────

    public function selectClient(int $id): void
    {
        $client = Client::find($id);
        if (!$client) return;
        $this->clientId = $client->id;
        $this->clientSearch = $client->name . ' · ' . $client->phone;
        $this->showClientDropdown = false;
    }

    public function clearClient(): void
    {
        $this->clientId = null;
        $this->clientSearch = '';
        $this->showClientDropdown = false;
    }

    // ── Дії з моделлю ─────────────────────────

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

    public function saveNewModel(): void
    {
        $this->validate([
            'newModelName' => 'required|min:2',
        ]);

        $model = DeviceModel::create([
            'brand_id'       => $this->brandId,
            'device_type_id' => $this->deviceTypeId,
            'name'           => $this->newModelName,
        ]);

        $this->modelId = $model->id;
        $this->modelSearch = $model->name;
        $this->showNewModel = false;
        $this->newModelName = '';
        unset($this->filteredModels);
    }

    // ── Збереження ────────────────────────────

    public function save(): void
    {
        $this->validate();

        // Оновлюємо пристрій
        $this->order->device->update([
            'device_type_id' => $this->deviceTypeId,
            'brand_id'       => $this->brandId,
            'model_id'       => $this->modelId,
            'serial_number'  => $this->serialNumber ?: null,
            'imei'           => $this->imei ?: null,
            'color'          => $this->color ?: null,
            'appearance'     => $this->appearance ?: null,
        ]);

        // Оновлюємо заявку
        $this->order->update([
            'client_id'      => $this->clientId,
            'type'           => $this->type,
            'priority'       => $this->priority,
            'malfunction'    => $this->malfunction,
            'diagnosis'      => $this->diagnosis ?: null,
            'notes'          => $this->notes ?: null,
            'estimated_date' => $this->estimatedDate ?: null,
            'prepayment'     => $this->prepayment ?: 0,
        ]);

        $this->redirect(route('orders.show', $this->order), navigate: false);
    }

    public function render()
    {
        return view('livewire.orders.order-edit')
            ->extends('layouts.app')
            ->section('content');
    }
}