<?php

namespace App\Livewire\Inventory;

use App\Models\Part;
use App\Models\PartCategory;
use App\Models\PartSupplierPrice;
use App\Models\Supplier;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;

class PartIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $categoryId = null;
    public bool $showLowStock = false;
    public bool $showOutOfStock = false;

    // ── Форма запчастини ──────────────────────
    public bool $showPartForm = false;
    public ?int $editingPartId = null;
    public string $partName = '';
    public string $partSku = '';
    public string $partUnit = 'шт';
    public string $partRetailPrice = '';
    public ?int $partCategoryId = null;
    public string $partMinStock = '0';
    public bool $partTrackStock = true;
    public string $partNotes = '';

    // ── Форма руху складу ─────────────────────
    public bool $showMovementForm = false;
    public ?int $movementPartId = null;
    public string $movementType = 'purchase';
    public string $movementQty = '';
    public string $movementUnitCost = '';
    public string $movementUnitPrice = '';
    public string $movementNotes = '';
    public ?int $movementSupplierId = null;

    // ── Форма коригування ─────────────────────
    public bool $showAdjustForm = false;
    public ?int $adjustPartId = null;
    public string $adjustQty = '';
    public string $adjustNotes = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingCategoryId(): void { $this->resetPage(); }
    public function updatingShowLowStock(): void { $this->resetPage(); }

    // ── Форма запчастини ──────────────────────

    public function openCreate(): void
    {
        $this->reset([
            'editingPartId', 'partName', 'partSku', 'partUnit',
            'partRetailPrice', 'partCategoryId', 'partMinStock',
            'partTrackStock', 'partNotes',
        ]);
        $this->partUnit = 'шт';
        $this->partTrackStock = true;
        $this->partMinStock = '0';
        $this->showPartForm = true;
    }

    public function openEdit(int $id): void
    {
        $part = Part::findOrFail($id);
        $this->editingPartId   = $part->id;
        $this->partName        = $part->name;
        $this->partSku         = $part->sku ?? '';
        $this->partUnit        = $part->unit;
        $this->partRetailPrice = $part->retail_price;
        $this->partCategoryId  = $part->category_id;
        $this->partMinStock    = $part->min_stock_qty;
        $this->partTrackStock  = $part->track_stock;
        $this->partNotes       = $part->notes ?? '';
        $this->showPartForm    = true;
    }

    public function savePart(): void
    {
        $this->validate([
            'partName'        => 'required|min:2',
            'partRetailPrice' => 'required|numeric|min:0',
            'partUnit'        => 'required',
        ], [
            'partName.required'        => 'Введіть назву',
            'partRetailPrice.required' => 'Введіть ціну',
        ]);

        $data = [
            'name'          => $this->partName,
            'sku'           => $this->partSku ?: null,
            'unit'          => $this->partUnit,
            'retail_price'  => (float)$this->partRetailPrice,
            'category_id'   => $this->partCategoryId ?: null,
            'min_stock_qty' => (int)$this->partMinStock,
            'track_stock'   => $this->partTrackStock,
            'notes'         => $this->partNotes ?: null,
            'is_active'     => true,
        ];

        if ($this->editingPartId) {
            Part::findOrFail($this->editingPartId)->update($data);
        } else {
            Part::create($data);
        }

        $this->showPartForm = false;
        $this->resetPage();
    }

    // ── Рух складу ────────────────────────────

    public function openMovement(int $partId, string $type = 'purchase'): void
    {
        $part = Part::findOrFail($partId);
        $this->movementPartId    = $partId;
        $this->movementType      = $type;
        $this->movementQty       = '';
        $this->movementUnitCost  = '';
        $this->movementUnitPrice = (string)$part->retail_price;
        $this->movementNotes     = '';
        $this->movementSupplierId = null;
        $this->showMovementForm   = true;
    }

    public function saveMovement(): void
    {
        $this->validate([
            'movementPartId' => 'required|exists:parts,id',
            'movementQty'    => 'required|integer|min:1',
            'movementType'   => 'required',
        ], [
            'movementQty.required' => 'Введіть кількість',
            'movementQty.min'      => 'Кількість має бути більше 0',
        ]);

        $qty = (int)$this->movementQty;

        // Витратні операції мають від'ємну кількість
        $isExpense = in_array($this->movementType, [
            'return_to_supplier', 'order_return', 'write_off'
        ]);

        StockMovement::create([
            'part_id'    => $this->movementPartId,
            'type'       => $this->movementType,
            'qty'        => $isExpense ? -$qty : $qty,
            'unit_cost'  => (float)($this->movementUnitCost ?: 0),
            'unit_price' => (float)($this->movementUnitPrice ?: 0),
            'user_id'    => auth()->id(),
            'notes'      => $this->movementNotes ?: null,
        ]);

        $this->showMovementForm = false;
    }

    // ── Коригування залишку ───────────────────

    public function openAdjust(int $partId): void
    {
        $part = Part::findOrFail($partId);
        $this->adjustPartId = $partId;
        $this->adjustQty    = (string)$part->stock_qty;
        $this->adjustNotes  = '';
        $this->showAdjustForm = true;
    }

    public function saveAdjust(): void
    {
        $this->validate([
            'adjustQty'   => 'required|integer|min:0',
            'adjustNotes' => 'required|min:2',
        ], [
            'adjustQty.required'   => 'Введіть кількість',
            'adjustNotes.required' => 'Вкажіть причину коригування',
        ]);

        $part = Part::findOrFail($this->adjustPartId);
        $diff = (int)$this->adjustQty - $part->stock_qty;

        if ($diff !== 0) {
            StockMovement::create([
                'part_id'  => $part->id,
                'type'     => 'adjustment',
                'qty'      => $diff,
                'user_id'  => auth()->id(),
                'notes'    => $this->adjustNotes,
            ]);
        }

        $this->showAdjustForm = false;
    }

    public function render()
    {
        $parts = Part::with('category')
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when($this->categoryId, fn($q) => $q->where('category_id', $this->categoryId))
            ->when($this->showLowStock, fn($q) => $q->lowStock())
            ->when($this->showOutOfStock, fn($q) =>
                $q->where('track_stock', true)->where('stock_qty', '<=', 0)
            )
            ->orderBy('name')
            ->paginate(25);

        $categories = PartCategory::whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();

        $suppliers = Supplier::active()->orderBy('name')->get();

        $lowStockCount = Part::lowStock()->count();

        return view('livewire.inventory.part-index', compact(
            'parts', 'categories', 'suppliers', 'lowStockCount'
        ))->extends('layouts.app')->section('content');
    }
}