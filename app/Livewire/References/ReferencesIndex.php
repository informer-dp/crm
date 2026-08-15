<?php

namespace App\Livewire\References;

use App\Models\Brand;
use App\Models\DeviceType;
use App\Models\ExpenseCategory;
use App\Models\PartCategory;
use App\Models\Supplier;
use Livewire\Component;

class ReferencesIndex extends Component
{
    public string $activeTab = 'device_types';

    // ── Типи пристроїв ────────────────────────
    public bool $showDeviceTypeForm = false;
    public ?int $editingDeviceTypeId = null;
    public string $deviceTypeName = '';
    public string $deviceTypeIcon = '';

    // ── Бренди ────────────────────────────────
    public bool $showBrandForm = false;
    public ?int $editingBrandId = null;
    public string $brandName = '';

    // ── Категорії запчастин ───────────────────
    public bool $showPartCatForm = false;
    public ?int $editingPartCatId = null;
    public string $partCatName = '';
    public ?int $partCatParentId = null;

    // ── Категорії витрат ──────────────────────
    public bool $showExpenseCatForm = false;
    public ?int $editingExpenseCatId = null;
    public string $expenseCatName = '';
    public string $expenseCatType = 'variable';
    public ?int $expenseCatParentId = null;

    // ── Постачальники ─────────────────────────
    public bool $showSupplierForm = false;
    public ?int $editingSupplierId = null;
    public string $supplierName = '';
    public string $supplierType = 'local';
    public string $supplierPhone = '';
    public string $supplierEmail = '';
    public string $supplierWebsite = '';
    public string $supplierNotes = '';
    public string $supplierContactPerson = '';

    // ── Типи пристроїв — методи ───────────────

    public function openDeviceTypeForm(?int $id = null): void
    {
        $this->editingDeviceTypeId = $id;
        if ($id) {
            $dt = DeviceType::findOrFail($id);
            $this->deviceTypeName = $dt->name;
            $this->deviceTypeIcon = $dt->icon ?? '';
        } else {
            $this->deviceTypeName = '';
            $this->deviceTypeIcon = '';
        }
        $this->showDeviceTypeForm = true;
    }

    public function saveDeviceType(): void
    {
        $this->validate([
            'deviceTypeName' => 'required|min:2',
        ], ['deviceTypeName.required' => 'Введіть назву']);

        if ($this->editingDeviceTypeId) {
            DeviceType::findOrFail($this->editingDeviceTypeId)->update([
                'name' => $this->deviceTypeName,
                'icon' => $this->deviceTypeIcon ?: null,
            ]);
        } else {
            DeviceType::create([
                'name'      => $this->deviceTypeName,
                'icon'      => $this->deviceTypeIcon ?: null,
                'is_active' => true,
            ]);
        }
        $this->showDeviceTypeForm = false;
    }

    public function toggleDeviceType(int $id): void
    {
        $dt = DeviceType::findOrFail($id);
        $dt->update(['is_active' => !$dt->is_active]);
    }

    // ── Бренди — методи ───────────────────────

    public function openBrandForm(?int $id = null): void
    {
        $this->editingBrandId = $id;
        if ($id) {
            $brand = Brand::findOrFail($id);
            $this->brandName = $brand->name;
        } else {
            $this->brandName = '';
        }
        $this->showBrandForm = true;
    }

    public function saveBrand(): void
    {
        $this->validate([
            'brandName' => 'required|min:2',
        ], ['brandName.required' => 'Введіть назву бренду']);

        if ($this->editingBrandId) {
            Brand::findOrFail($this->editingBrandId)->update([
                'name'           => $this->brandName,
                'device_type_id' => null,
            ]);
        } else {
            Brand::create([
                'name'           => $this->brandName,
                'device_type_id' => null,
                'is_active'      => true,
            ]);
        }
        $this->showBrandForm = false;
    }

    public function toggleBrand(int $id): void
    {
        $brand = Brand::findOrFail($id);
        $brand->update(['is_active' => !$brand->is_active]);
    }

    // ── Категорії запчастин — методи ──────────

    public function openPartCatForm(?int $id = null): void
    {
        $this->editingPartCatId = $id;
        if ($id) {
            $cat = PartCategory::findOrFail($id);
            $this->partCatName     = $cat->name;
            $this->partCatParentId = $cat->parent_id;
        } else {
            $this->partCatName     = '';
            $this->partCatParentId = null;
        }
        $this->showPartCatForm = true;
    }

    public function savePartCat(): void
    {
        $this->validate([
            'partCatName' => 'required|min:2',
        ], ['partCatName.required' => 'Введіть назву категорії']);

        if ($this->editingPartCatId) {
            PartCategory::findOrFail($this->editingPartCatId)->update([
                'name'      => $this->partCatName,
                'parent_id' => $this->partCatParentId ?: null,
            ]);
        } else {
            PartCategory::create([
                'name'      => $this->partCatName,
                'parent_id' => $this->partCatParentId ?: null,
                'is_active' => true,
            ]);
        }
        $this->showPartCatForm = false;
    }

    // ── Категорії витрат — методи ─────────────

    public function openExpenseCatForm(?int $id = null): void
    {
        $this->editingExpenseCatId = $id;
        if ($id) {
            $cat = ExpenseCategory::findOrFail($id);
            $this->expenseCatName     = $cat->name;
            $this->expenseCatType     = $cat->type;
            $this->expenseCatParentId = $cat->parent_id;
        } else {
            $this->expenseCatName     = '';
            $this->expenseCatType     = 'variable';
            $this->expenseCatParentId = null;
        }
        $this->showExpenseCatForm = true;
    }

    public function saveExpenseCat(): void
    {
        $this->validate([
            'expenseCatName' => 'required|min:2',
        ], ['expenseCatName.required' => 'Введіть назву категорії']);

        if ($this->editingExpenseCatId) {
            ExpenseCategory::findOrFail($this->editingExpenseCatId)->update([
                'name'      => $this->expenseCatName,
                'type'      => $this->expenseCatType,
                'parent_id' => $this->expenseCatParentId ?: null,
            ]);
        } else {
            ExpenseCategory::create([
                'name'      => $this->expenseCatName,
                'type'      => $this->expenseCatType,
                'parent_id' => $this->expenseCatParentId ?: null,
                'is_active' => true,
            ]);
        }
        $this->showExpenseCatForm = false;
    }

    // ── Постачальники — методи ────────────────

    public function openSupplierForm(?int $id = null): void
    {
        $this->editingSupplierId = $id;
        if ($id) {
            $s = Supplier::findOrFail($id);
            $this->supplierName          = $s->name;
            $this->supplierType          = $s->type;
            $this->supplierPhone         = $s->phone ?? '';
            $this->supplierEmail         = $s->email ?? '';
            $this->supplierWebsite       = $s->website ?? '';
            $this->supplierNotes         = $s->notes ?? '';
            $this->supplierContactPerson = $s->contact_person ?? '';
        } else {
            $this->supplierName          = '';
            $this->supplierType          = 'local';
            $this->supplierPhone         = '';
            $this->supplierEmail         = '';
            $this->supplierWebsite       = '';
            $this->supplierNotes         = '';
            $this->supplierContactPerson = '';
        }
        $this->showSupplierForm = true;
    }

    public function saveSupplier(): void
    {
        $this->validate([
            'supplierName' => 'required|min:2',
            'supplierType' => 'required|in:local,online_shop,marketplace,subcontractor,other',
        ], ['supplierName.required' => 'Введіть назву']);

        $data = [
            'name'           => $this->supplierName,
            'type'           => $this->supplierType,
            'contact_person' => $this->supplierContactPerson ?: null,
            'phone'          => $this->supplierPhone ?: null,
            'email'          => $this->supplierEmail ?: null,
            'website'        => $this->supplierWebsite ?: null,
            'notes'          => $this->supplierNotes ?: null,
            'is_active'      => true,
        ];

        if ($this->editingSupplierId) {
            Supplier::findOrFail($this->editingSupplierId)->update($data);
        } else {
            Supplier::create($data);
        }
        $this->showSupplierForm = false;
    }

    public function toggleSupplier(int $id): void
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->update(['is_active' => !$supplier->is_active]);
    }

    public function render()
    {
        $deviceTypes = DeviceType::orderBy('name')->get();
        $brands      = Brand::orderBy('name')->get();

        $partCategories = PartCategory::whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();

        $expenseCategories = ExpenseCategory::whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();

        $partCatParents     = PartCategory::whereNull('parent_id')->orderBy('name')->get();
        $expenseCatParents  = ExpenseCategory::whereNull('parent_id')->orderBy('name')->get();

        $suppliers = Supplier::orderBy('type')->orderBy('name')->get();

        return view('livewire.references.references-index', compact(
            'deviceTypes', 'brands',
            'partCategories', 'expenseCategories',
            'partCatParents', 'expenseCatParents',
            'suppliers'
        ))->extends('layouts.app')->section('content');
    }
}