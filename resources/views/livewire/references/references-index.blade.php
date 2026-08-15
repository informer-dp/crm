<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Довідники</h1>
    </div>

    {{-- Вкладки --}}
    <div style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:24px;border-bottom:2px solid #e5e7eb;padding-bottom:0">
        @foreach([
            'device_types' => 'Типи пристроїв',
            'brands' => 'Бренди',
            'part_categories' => 'Категорії запчастин',
            'expense_categories' => 'Категорії витрат',
            'suppliers' => 'Постачальники',
        ] as $tab => $label)
        <button wire:click="$set('activeTab', '{{ $tab }}')"
                style="padding:8px 16px;font-size:14px;font-weight:500;border:none;cursor:pointer;border-radius:8px 8px 0 0;transition:all 0.2s;
                    {{ $activeTab === $tab
                        ? 'background:#6366f1;color:white;margin-bottom:-2px;border-bottom:2px solid #6366f1'
                        : 'background:transparent;color:#6b7280' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- Типи пристроїв --}}
    @if($activeTab === 'device_types')
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-bold">Типи пристроїв</h2>
                <button wire:click="openDeviceTypeForm()" class="btn btn-primary btn-sm">+ Додати</button>
            </div>
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead><tr><th>Назва</th><th>Іконка</th><th>Статус</th><th></th></tr></thead>
                    <tbody>
                        @foreach($deviceTypes as $dt)
                        <tr class="hover">
                            <td class="font-medium">{{ $dt->name }}</td>
                            <td class="text-base-content/60">{{ $dt->icon ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $dt->is_active ? 'badge-success' : 'badge-ghost' }} badge-sm">
                                    {{ $dt->is_active ? 'Активний' : 'Неактивний' }}
                                </span>
                            </td>
                            <td class="text-right">
                                <button wire:click="openDeviceTypeForm({{ $dt->id }})" class="btn btn-ghost btn-xs">Редагувати</button>
                                <button wire:click="toggleDeviceType({{ $dt->id }})"
                                        class="btn btn-ghost btn-xs {{ $dt->is_active ? 'text-error' : 'text-success' }}">
                                    {{ $dt->is_active ? 'Вимкнути' : 'Увімкнути' }}
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Бренди --}}
    @if($activeTab === 'brands')
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-bold">Бренди ({{ $brands->count() }})</h2>
                <button wire:click="openBrandForm()" class="btn btn-primary btn-sm">+ Додати</button>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                @foreach($brands as $brand)
                <div class="flex items-center justify-between p-2 rounded-lg border border-base-200 hover:bg-base-200">
                    <span class="text-sm font-medium {{ !$brand->is_active ? 'opacity-40 line-through' : '' }}">
                        {{ $brand->name }}
                    </span>
                    <div class="flex gap-1">
                        <button wire:click="openBrandForm({{ $brand->id }})"
                                class="btn btn-ghost btn-xs">✎</button>
                        <button wire:click="toggleBrand({{ $brand->id }})"
                                class="btn btn-ghost btn-xs {{ $brand->is_active ? 'text-error' : 'text-success' }}">
                            {{ $brand->is_active ? '✕' : '✓' }}
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Категорії запчастин --}}
    @if($activeTab === 'part_categories')
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-bold">Категорії запчастин</h2>
                <button wire:click="openPartCatForm()" class="btn btn-primary btn-sm">+ Додати</button>
            </div>
            <div class="flex flex-col gap-2">
                @foreach($partCategories as $parent)
                <div>
                    <div class="flex items-center justify-between p-2 bg-base-200 rounded-lg mb-1">
                        <span class="font-medium text-sm">{{ $parent->name }}</span>
                        <button wire:click="openPartCatForm({{ $parent->id }})"
                                class="btn btn-ghost btn-xs">Редагувати</button>
                    </div>
                    @foreach($parent->children as $child)
                    <div class="flex items-center justify-between p-2 ml-4 rounded-lg hover:bg-base-200">
                        <span class="text-sm">↳ {{ $child->name }}</span>
                        <button wire:click="openPartCatForm({{ $child->id }})"
                                class="btn btn-ghost btn-xs">Редагувати</button>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Категорії витрат --}}
    @if($activeTab === 'expense_categories')
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-bold">Категорії витрат</h2>
                <button wire:click="openExpenseCatForm()" class="btn btn-primary btn-sm">+ Додати</button>
            </div>
            <div class="flex flex-col gap-2">
                @foreach($expenseCategories as $parent)
                <div>
                    <div class="flex items-center justify-between p-2 bg-base-200 rounded-lg mb-1">
                        <div>
                            <span class="font-medium text-sm">{{ $parent->name }}</span>
                            <span class="badge badge-ghost badge-xs ml-2">
                                {{ match($parent->type) {
                                    'fixed' => 'Постійна',
                                    'variable' => 'Змінна',
                                    'one_time' => 'Разова',
                                    default => $parent->type
                                } }}
                            </span>
                        </div>
                        <button wire:click="openExpenseCatForm({{ $parent->id }})"
                                class="btn btn-ghost btn-xs">Редагувати</button>
                    </div>
                    @foreach($parent->children as $child)
                    <div class="flex items-center justify-between p-2 ml-4 rounded-lg hover:bg-base-200">
                        <div>
                            <span class="text-sm">↳ {{ $child->name }}</span>
                            <span class="badge badge-ghost badge-xs ml-2">
                                {{ match($child->type) {
                                    'fixed' => 'Постійна',
                                    'variable' => 'Змінна',
                                    'one_time' => 'Разова',
                                    default => $child->type
                                } }}
                            </span>
                        </div>
                        <button wire:click="openExpenseCatForm({{ $child->id }})"
                                class="btn btn-ghost btn-xs">Редагувати</button>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Постачальники --}}
    @if($activeTab === 'suppliers')
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-bold">Постачальники та підрядники</h2>
                <button wire:click="openSupplierForm()" class="btn btn-primary btn-sm">+ Додати</button>
            </div>
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Назва</th>
                            <th>Тип</th>
                            <th>Телефон</th>
                            <th>Сайт</th>
                            <th>Статус</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suppliers as $supplier)
                        <tr class="hover">
                            <td>
                                <div class="font-medium text-sm">{{ $supplier->name }}</div>
                                @if($supplier->contact_person)
                                    <div class="text-xs text-base-content/60">{{ $supplier->contact_person }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-ghost badge-sm">
                                    {{ match($supplier->type) {
                                        'local' => 'Місцевий',
                                        'online_shop' => 'Інтернет-магазин',
                                        'marketplace' => 'Маркетплейс',
                                        'subcontractor' => 'Підрядник',
                                        default => 'Інший'
                                    } }}
                                </span>
                            </td>
                            <td class="text-sm">{{ $supplier->phone ?? '—' }}</td>
                            <td class="text-sm">
                                @if($supplier->website)
                                    <a href="{{ $supplier->website }}" target="_blank"
                                       class="link link-primary text-xs">{{ $supplier->website }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $supplier->is_active ? 'badge-success' : 'badge-ghost' }} badge-sm">
                                    {{ $supplier->is_active ? 'Активний' : 'Неактивний' }}
                                </span>
                            </td>
                            <td class="text-right">
                                <button wire:click="openSupplierForm({{ $supplier->id }})"
                                        class="btn btn-ghost btn-xs">Редагувати</button>
                                <button wire:click="toggleSupplier({{ $supplier->id }})"
                                        class="btn btn-ghost btn-xs {{ $supplier->is_active ? 'text-error' : 'text-success' }}">
                                    {{ $supplier->is_active ? 'Вимкнути' : 'Увімкнути' }}
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Модал типу пристрою --}}
    @if($showDeviceTypeForm)
    <div style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.6)">
        <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:400px;margin:16px">
            <h3 style="font-size:18px;font-weight:bold;margin-bottom:16px">
                {{ $editingDeviceTypeId ? 'Редагувати тип пристрою' : 'Новий тип пристрою' }}
            </h3>
            <div style="display:flex;flex-direction:column;gap:12px">
                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Назва *</label>
                    <input wire:model="deviceTypeName" type="text"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"
                           placeholder="Смартфон, Ноутбук..."/>
                    @error('deviceTypeName')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Іконка</label>
                    <input wire:model="deviceTypeIcon" type="text"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"
                           placeholder="smartphone, laptop, tablet..."/>
                </div>
            </div>
            <div style="display:flex;gap:8px;margin-top:16px">
                <button wire:click="saveDeviceType"
                        style="flex:1;padding:10px;background:#6366f1;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:500">
                    Зберегти
                </button>
                <button wire:click="$set('showDeviceTypeForm', false)"
                        style="padding:10px 16px;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer">
                    Скасувати
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Модал бренду --}}
    @if($showBrandForm)
    <div style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.6)">
        <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:400px;margin:16px">
            <h3 style="font-size:18px;font-weight:bold;margin-bottom:16px">
                {{ $editingBrandId ? 'Редагувати бренд' : 'Новий бренд' }}
            </h3>
            <div>
                <label style="font-size:13px;display:block;margin-bottom:4px">Назва *</label>
                <input wire:model="brandName" type="text"
                       style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"
                       placeholder="Apple, Samsung, Lenovo..."/>
                @error('brandName')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
            </div>
            <div style="display:flex;gap:8px;margin-top:16px">
                <button wire:click="saveBrand"
                        style="flex:1;padding:10px;background:#6366f1;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:500">
                    Зберегти
                </button>
                <button wire:click="$set('showBrandForm', false)"
                        style="padding:10px 16px;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer">
                    Скасувати
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Модал категорії запчастин --}}
    @if($showPartCatForm)
    <div style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.6)">
        <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:400px;margin:16px">
            <h3 style="font-size:18px;font-weight:bold;margin-bottom:16px">
                {{ $editingPartCatId ? 'Редагувати категорію' : 'Нова категорія запчастин' }}
            </h3>
            <div style="display:flex;flex-direction:column;gap:12px">
                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Назва *</label>
                    <input wire:model="partCatName" type="text"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                    @error('partCatName')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Батьківська категорія</label>
                    <select wire:model="partCatParentId"
                            style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                        <option value="">— Верхній рівень —</option>
                        @foreach($partCatParents as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="display:flex;gap:8px;margin-top:16px">
                <button wire:click="savePartCat"
                        style="flex:1;padding:10px;background:#6366f1;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:500">
                    Зберегти
                </button>
                <button wire:click="$set('showPartCatForm', false)"
                        style="padding:10px 16px;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer">
                    Скасувати
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Модал категорії витрат --}}
    @if($showExpenseCatForm)
    <div style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.6)">
        <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:400px;margin:16px">
            <h3 style="font-size:18px;font-weight:bold;margin-bottom:16px">
                {{ $editingExpenseCatId ? 'Редагувати категорію' : 'Нова категорія витрат' }}
            </h3>
            <div style="display:flex;flex-direction:column;gap:12px">
                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Назва *</label>
                    <input wire:model="expenseCatName" type="text"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                    @error('expenseCatName')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Тип</label>
                    <select wire:model="expenseCatType"
                            style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                        <option value="fixed">Постійна</option>
                        <option value="variable">Змінна</option>
                        <option value="one_time">Разова</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Батьківська категорія</label>
                    <select wire:model="expenseCatParentId"
                            style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                        <option value="">— Верхній рівень —</option>
                        @foreach($expenseCatParents as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="display:flex;gap:8px;margin-top:16px">
                <button wire:click="saveExpenseCat"
                        style="flex:1;padding:10px;background:#6366f1;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:500">
                    Зберегти
                </button>
                <button wire:click="$set('showExpenseCatForm', false)"
                        style="padding:10px 16px;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer">
                    Скасувати
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Модал постачальника --}}
    @if($showSupplierForm)
    <div style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;display:flex;align-items:flex-start;justify-content:center;background:rgba(0,0,0,0.6);overflow-y:auto;padding:20px">
        <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:500px;margin:auto">
            <h3 style="font-size:18px;font-weight:bold;margin-bottom:16px">
                {{ $editingSupplierId ? 'Редагувати постачальника' : 'Новий постачальник' }}
            </h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div style="grid-column:span 2">
                    <label style="font-size:13px;display:block;margin-bottom:4px">Назва *</label>
                    <input wire:model="supplierName" type="text"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                    @error('supplierName')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                </div>
                <div style="grid-column:span 2">
                    <label style="font-size:13px;display:block;margin-bottom:4px">Тип</label>
                    <select wire:model="supplierType"
                            style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                        <option value="local">Місцевий постачальник</option>
                        <option value="online_shop">Інтернет-магазин</option>
                        <option value="marketplace">Маркетплейс (Rozetka, Prom, Ali)</option>
                        <option value="subcontractor">Підрядник (інший СЦ)</option>
                        <option value="other">Інший</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Контактна особа</label>
                    <input wire:model="supplierContactPerson" type="text"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                </div>
                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Телефон</label>
                    <input wire:model="supplierPhone" type="text"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                </div>
                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Email</label>
                    <input wire:model="supplierEmail" type="email"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                </div>
                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Сайт</label>
                    <input wire:model="supplierWebsite" type="text"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"
                           placeholder="https://..."/>
                </div>
                <div style="grid-column:span 2">
                    <label style="font-size:13px;display:block;margin-bottom:4px">Нотатки</label>
                    <textarea wire:model="supplierNotes" rows="2"
                              style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px;resize:vertical"></textarea>
                </div>
            </div>
            <div style="display:flex;gap:8px;margin-top:16px">
                <button wire:click="saveSupplier"
                        style="flex:1;padding:10px;background:#6366f1;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:500">
                    Зберегти
                </button>
                <button wire:click="$set('showSupplierForm', false)"
                        style="padding:10px 16px;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer">
                    Скасувати
                </button>
            </div>
        </div>
    </div>
    @endif

</div>