<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use App\Models\User;
use Livewire\Component;

class OrderShow extends Component
{
    public Order $order;

    // Зміна статусу
    public bool $showStatusForm = false;
    public bool $showStatusModal = false;
    public string $newStatus = '';
    public string $statusComment = '';

    // Призначення інженера
    public bool $showEngineerModal = false;
    public ?int $engineerId = null;

    // ── Кошторис ──────────────────────────────
    public bool $showEstimateForm = false;

    // Нова робота
    public string $workName = '';
    public string $workType = 'own';
    public ?int $workEngineerId = null;
    public string $workPrice = '';
    public string $workQuantity = '1';
    public bool $workIsWarranty = false;

    // Нова запчастина
    public string $partName = '';
    public string $partPrice = '';
    public string $partCost = '';
    public string $partQuantity = '1';
    public bool $partIsOwn = false;

    public function mount(Order $order): void
    {
        $this->order = $order->load([
            'client',
            'device.deviceType',
            'device.brand',
            'device.model',
            'manager',
            'engineers',
            'estimate.works',
            'estimate.parts',
            'comments.user',
            'statusHistory.user',
        ]);
    }

    // ── Зміна статусу ─────────────────────────

    public function openStatusModal(string $status): void
    {
        $this->newStatus = $status;
        $this->statusComment = '';
        $this->showStatusModal = true;
    }

    public function changeStatus(): void
    {
        if (!$this->order->canTransitionTo($this->newStatus)) {
            $this->addError('status', 'Неможливо змінити статус');
            return;
        }

        $this->order->update([
            'status'       => $this->newStatus,
            'issued_at'    => $this->newStatus === 'issued' ? now() : $this->order->issued_at,
            'cancelled_at' => $this->newStatus === 'cancelled' ? now() : $this->order->cancelled_at,
        ]);

        // Додаємо коментар якщо є
        if (trim($this->statusComment)) {
            $this->order->comments()->create([
                'user_id'     => auth()->id(),
                'body'        => $this->statusComment,
                'is_internal' => true,
            ]);
        }

        $this->order->refresh()->load([
            'comments.user',
            'statusHistory.user',
            'engineers',
        ]);

        $this->showStatusModal = false;
        $this->statusComment = '';
    }

    // ── Призначення інженера ──────────────────

    public function openEngineerModal(): void
    {
        $this->engineerId = $this->order->engineers->first()?->id;
        $this->showEngineerModal = true;
    }

    public function assignEngineer(): void
    {
        if (!$this->engineerId) return;

        // Знімаємо старих інженерів і призначаємо нового
        $this->order->orderEngineers()->delete();
        $this->order->orderEngineers()->create([
            'user_id'    => $this->engineerId,
            'is_primary' => true,
        ]);

        $this->order->refresh()->load('engineers');
        $this->showEngineerModal = false;
    }
    // ── Методи кошторису ──────────────────────

public function initEstimate(): void
{
    if (!$this->order->estimate) {
        $this->order->estimate()->create([
            'works_total'   => 0,
            'parts_total'   => 0,
            'discount'      => 0,
            'discount_type' => 'fixed',
            'total'         => 0,
        ]);
        $this->order->refresh()->load('estimate.works', 'estimate.parts');
    }
    $this->showEstimateForm = true;
}

public function addWork(): void
{
    $this->validate([
        'workName'  => 'required|min:2',
        'workPrice' => 'required|numeric|min:0',
        'workQuantity' => 'required|integer|min:1',
    ], [
        'workName.required'  => 'Введіть назву роботи',
        'workPrice.required' => 'Введіть ціну',
    ]);

    $this->order->estimate->works()->create([
        'name'        => $this->workName,
        'work_type'   => $this->workType,
        'engineer_id' => $this->workEngineerId ?: null,
        'price'       => $this->workPrice,
        'quantity'    => $this->workQuantity,
        'total'       => $this->workPrice * $this->workQuantity,
        'cost'        => 0,
        'is_warranty' => $this->workIsWarranty,
        'status'      => 'pending',
    ]);

    $this->order->estimate->recalculate();
    $this->order->refresh()->load('estimate.works', 'estimate.parts');

    $this->workName = '';
    $this->workPrice = '';
    $this->workQuantity = '1';
    $this->workIsWarranty = false;
    $this->workEngineerId = null;
}

public function removeWork(int $id): void
{
    if ($this->order->isLocked()) return;
    $this->order->estimate->works()->findOrFail($id)->delete();
    $this->order->refresh()->load('estimate.works', 'estimate.parts');
}

public function addPart(): void
{
    $this->validate([
        'partName'  => 'required|min:2',
        'partPrice' => 'required|numeric|min:0',
        'partQuantity' => 'required|integer|min:1',
    ], [
        'partName.required'  => 'Введіть назву запчастини',
        'partPrice.required' => 'Введіть ціну',
    ]);

    $this->order->estimate->parts()->create([
        'name'       => $this->partName,
        'price'      => $this->partPrice,
        'cost'       => $this->partCost ?: 0,
        'quantity'   => $this->partQuantity,
        'total'      => $this->partPrice * $this->partQuantity,
        'is_own_part'=> $this->partIsOwn,
    ]);

    $this->order->estimate->recalculate();
    $this->order->refresh()->load('estimate.works', 'estimate.parts');

    $this->partName = '';
    $this->partPrice = '';
    $this->partCost = '';
    $this->partQuantity = '1';
    $this->partIsOwn = false;
}

public function removePart(int $id): void
{
    if ($this->order->isLocked()) return;
    $this->order->estimate->parts()->findOrFail($id)->delete();
    $this->order->refresh()->load('estimate.works', 'estimate.parts');
}

public function updateDiscount(string $discount, string $type): void
{
    if ($this->order->isLocked()) return;
    $this->order->estimate->update([
        'discount'      => (float)$discount,
        'discount_type' => $type,
    ]);
    $this->order->estimate->recalculate();
    $this->order->refresh()->load('estimate');
}
    // ── Додавання коментаря ───────────────────

    public string $newComment = '';
    public bool $commentIsInternal = true;

    public function addComment(): void
    {
        $this->validate([
            'newComment' => 'required|min:2',
        ], [
            'newComment.required' => 'Введіть текст коментаря',
        ]);

        $this->order->comments()->create([
            'user_id'     => auth()->id(),
            'body'        => $this->newComment,
            'is_internal' => $this->commentIsInternal,
        ]);

        $this->newComment = '';
        $this->order->refresh()->load('comments.user');
    }

    public function render()
    {
        $engineers = User::role('engineer')->active()->get();

        return view('livewire.orders.order-show', compact('engineers'))
            ->extends('layouts.app')
            ->section('content');
    }
}