<div>
    {{-- Шапка --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Склад запчастин</h1>
            @if($lowStockCount > 0)
            <p class="text-error text-sm mt-1">
                ⚠ {{ $lowStockCount }} позицій з мінімальним залишком
            </p>
            @endif
        </div>
        <button wire:click="openCreate" class="btn btn-primary gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Нова запчастина
        </button>
    </div>

    {{-- Фільтри --}}
    <div class="card bg-base-100 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="flex flex-wrap gap-3 items-center">
                <label class="input input-bordered flex items-center gap-2 flex-1 min-w-48">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input wire:model.live.debounce.300ms="search"
                           type="text" placeholder="Назва або артикул..." class="grow"/>
                </label>

                <select wire:model.live="categoryId" class="select select-bordered w-48">
                    <option value="">Всі категорії</option>
                    @foreach($categories as $cat)
                        <optgroup label="{{ $cat->name }}">
                            @foreach($cat->children as $child)
                                <option value="{{ $child->id }}">{{ $child->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input wire:model.live="showLowStock" type="checkbox" class="checkbox checkbox-warning checkbox-sm"/>
                    <span class="text-sm">Мінімальний залишок</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input wire:model.live="showOutOfStock" type="checkbox" class="checkbox checkbox-error checkbox-sm"/>
                    <span class="text-sm">Немає на складі</span>
                </label>
            </div>
        </div>
    </div>

    {{-- Таблиця --}}
    <div class="card bg-base-100 shadow-sm">
        <div class="overflow-x-auto">
            <table class="table table-zebra table-sm">
                <thead>
                    <tr>
                        <th>Назва</th>
                        <th>Артикул</th>
                        <th>Категорія</th>
                        <th class="text-right">Залишок</th>
                        <th class="text-right">Мін.</th>
                        <th class="text-right">Ціна</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parts as $part)
                    <tr class="hover {{ $part->isLowStock() ? 'bg-warning/5' : '' }} {{ $part->isOutOfStock() ? 'bg-error/5' : '' }}">
                        <td>
                            <div class="font-medium text-sm">{{ $part->name }}</div>
                            @if($part->notes)
                                <div class="text-xs text-base-content/50">{{ Str::limit($part->notes, 40) }}</div>
                            @endif
                        </td>
                        <td class="text-sm font-mono text-base-content/60">{{ $part->sku ?? '—' }}</td>
                        <td class="text-sm text-base-content/60">{{ $part->category?->name ?? '—' }}</td>
                        <td class="text-right">
                            @if($part->track_stock)
                                <span class="font-bold {{ $part->isOutOfStock() ? 'text-error' : ($part->isLowStock() ? 'text-warning' : '') }}">
                                    {{ $part->stock_qty }}
                                </span>
                                <span class="text-xs text-base-content/50"> {{ $part->unit }}</span>
                            @else
                                <span class="text-base-content/40 text-sm">—</span>
                            @endif
                        </td>
                        <td class="text-right text-sm text-base-content/50">
                            {{ $part->track_stock ? $part->min_stock_qty : '—' }}
                        </td>
                        <td class="text-right text-sm font-medium">
                            {{ number_format($part->retail_price, 0, '.', ' ') }} ₴
                        </td>
                        <td>
                            <div class="flex gap-1 justify-end">
                                <button wire:click="openMovement({{ $part->id }}, 'purchase')"
                                        class="btn btn-ghost btn-xs text-success"
                                        title="Надходження">
                                    +
                                </button>
                                <button wire:click="openMovement({{ $part->id }}, 'write_off')"
                                        class="btn btn-ghost btn-xs text-error"
                                        title="Списання">
                                    −
                                </button>
                                <button wire:click="openAdjust({{ $part->id }})"
                                        class="btn btn-ghost btn-xs"
                                        title="Коригування">
                                    ✎
                                </button>
                                <button wire:click="openEdit({{ $part->id }})"
                                        class="btn btn-ghost btn-xs">
                                    ···
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-12 text-base-content/40">
                            @if($search || $categoryId)
                                Запчастин не знайдено
                            @else
                                Склад порожній. <button wire:click="openCreate" class="link">Додати першу запчастину</button>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($parts->hasPages())
        <div class="p-4 border-t border-base-200">
            {{ $parts->links() }}
        </div>
        @endif
    </div>

    {{-- Модал запчастини --}}
    @if($showPartForm)
    <div style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.6);overflow-y:auto;padding:20px">
        <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:500px;margin:auto">
            <h3 style="font-size:18px;font-weight:bold;margin-bottom:16px">
                {{ $editingPartId ? 'Редагування запчастини' : 'Нова запчастина' }}
            </h3>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div style="grid-column:span 2">
                    <label style="font-size:13px;display:block;margin-bottom:4px">Назва *</label>
                    <input wire:model="partName" type="text"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"
                           placeholder="Акумулятор iPhone 13..."/>
                    @error('partName')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Артикул (SKU)</label>
                    <input wire:model="partSku" type="text"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"
                           placeholder="BAT-IP13-001"/>
                </div>

                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Одиниця виміру</label>
                    <select wire:model="partUnit"
                            style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                        <option value="шт">шт</option>
                        <option value="м">м</option>
                        <option value="компл">компл</option>
                        <option value="пара">пара</option>
                    </select>
                </div>

                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Роздрібна ціна *</label>
                    <input wire:model="partRetailPrice" type="number" min="0"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                    @error('partRetailPrice')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Категорія</label>
                    <select wire:model="partCategoryId"
                            style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                        <option value="">Без категорії</option>
                        @foreach($categories as $cat)
                            <optgroup label="{{ $cat->name }}">
                                @foreach($cat->children as $child)
                                    <option value="{{ $child->id }}">{{ $child->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Мінімальний залишок</label>
                    <input wire:model="partMinStock" type="number" min="0"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                </div>

                <div style="grid-column:span 2">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                        <input wire:model="partTrackStock" type="checkbox" style="width:16px;height:16px"/>
                        <span style="font-size:14px">Вести облік залишку</span>
                    </label>
                </div>

                <div style="grid-column:span 2">
                    <label style="font-size:13px;display:block;margin-bottom:4px">Нотатки</label>
                    <textarea wire:model="partNotes" rows="2"
                              style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px;resize:vertical"></textarea>
                </div>
            </div>

            <div style="display:flex;gap:8px;margin-top:16px">
                <button wire:click="savePart"
                        style="flex:1;padding:10px;background:#6366f1;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:500">
                    {{ $editingPartId ? 'Зберегти' : 'Додати запчастину' }}
                </button>
                <button wire:click="$set('showPartForm', false)"
                        style="padding:10px 16px;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer">
                    Скасувати
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Модал руху складу --}}
    @if($showMovementForm)
    <div style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.6)">
        <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:420px;margin:16px">
            <h3 style="font-size:18px;font-weight:bold;margin-bottom:16px">
                {{ match($movementType) {
                    'purchase' => '📦 Надходження',
                    'write_off' => '🗑 Списання',
                    'return_to_supplier' => '↩ Повернення постачальнику',
                    default => 'Рух складу'
                } }}
            </h3>

            <div style="display:flex;flex-direction:column;gap:12px">
                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Тип операції</label>
                    <select wire:model="movementType"
                            style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                        <option value="purchase">Надходження (закупівля)</option>
                        <option value="write_off">Списання (брак/втрата)</option>
                        <option value="return_to_supplier">Повернення постачальнику</option>
                        <option value="adjustment">Коригування</option>
                    </select>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                    <div>
                        <label style="font-size:13px;display:block;margin-bottom:4px">Кількість *</label>
                        <input wire:model="movementQty" type="number" min="1"
                               style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                        @error('movementQty')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label style="font-size:13px;display:block;margin-bottom:4px">Закупівельна ціна</label>
                        <input wire:model="movementUnitCost" type="number" min="0"
                               style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                    </div>

                    <div>
                        <label style="font-size:13px;display:block;margin-bottom:4px">Роздрібна ціна</label>
                        <input wire:model="movementUnitPrice" type="number" min="0"
                               style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"/>
                    </div>

                    @if($movementType === 'purchase')
                    <div>
                        <label style="font-size:13px;display:block;margin-bottom:4px">Постачальник</label>
                        <select wire:model="movementSupplierId"
                                style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px">
                            <option value="">Не вказано</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Примітка</label>
                    <input wire:model="movementNotes" type="text"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"
                           placeholder="Необов'язково"/>
                </div>
            </div>

            <div style="display:flex;gap:8px;margin-top:16px">
                <button wire:click="saveMovement"
                        style="flex:1;padding:10px;background:#6366f1;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:500">
                    Зберегти
                </button>
                <button wire:click="$set('showMovementForm', false)"
                        style="padding:10px 16px;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer">
                    Скасувати
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Модал коригування --}}
    @if($showAdjustForm)
    <div style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.6)">
        <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:380px;margin:16px">
            <h3 style="font-size:18px;font-weight:bold;margin-bottom:4px">Коригування залишку</h3>
            <p style="font-size:13px;color:#666;margin-bottom:16px">Вкажіть фактичну кількість після інвентаризації</p>

            <div style="display:flex;flex-direction:column;gap:12px">
                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Фактична кількість *</label>
                    <input wire:model="adjustQty" type="number" min="0"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px;font-size:18px;text-align:center"/>
                    @error('adjustQty')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label style="font-size:13px;display:block;margin-bottom:4px">Причина коригування *</label>
                    <input wire:model="adjustNotes" type="text"
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px"
                           placeholder="Інвентаризація, пересортиця..."/>
                    @error('adjustNotes')<span style="color:red;font-size:12px">{{ $message }}</span>@enderror
                </div>
            </div>

            <div style="display:flex;gap:8px;margin-top:16px">
                <button wire:click="saveAdjust"
                        style="flex:1;padding:10px;background:#6366f1;color:white;border:none;border-radius:8px;cursor:pointer;font-weight:500">
                    Зберегти
                </button>
                <button wire:click="$set('showAdjustForm', false)"
                        style="padding:10px 16px;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer">
                    Скасувати
                </button>
            </div>
        </div>
    </div>
    @endif

</div>