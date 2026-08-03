<div>
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('orders.index') }}" class="btn btn-ghost btn-sm gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Назад
        </a>
        <h1 class="text-2xl font-bold">Нова заявка</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Ліва колонка --}}
        <div class="lg:col-span-2 flex flex-col gap-4">

            {{-- Клієнт --}}
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-base">Клієнт</h2>

                    @if($clientId && !$showClientForm)
                        {{-- Клієнт обраний --}}
                        <div class="flex items-center justify-between bg-success/10 rounded-lg p-3">
                            <div>
                                <p class="font-medium text-sm">{{ $clientSearch }}</p>
                            </div>
                            <button wire:click="clearClient" class="btn btn-ghost btn-xs">Змінити</button>
                        </div>
                    @elseif($showClientForm)
                        {{-- Форма нового клієнта --}}
                        <div class="flex flex-col gap-3">
                            <div class="flex gap-3">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" wire:model="clientType" value="individual" class="radio radio-sm"/>
                                    <span class="text-sm">Фізична особа</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" wire:model="clientType" value="legal" class="radio radio-sm"/>
                                    <span class="text-sm">Юридична особа</span>
                                </label>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Ім'я / Назва *</span></label>
                                    <input wire:model="clientName" type="text" class="input input-bordered input-sm"
                                           placeholder="Іван Петренко"/>
                                    @error('clientName')<span class="text-error text-xs">{{ $message }}</span>@enderror
                                </div>
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Телефон *</span></label>
                                    <input wire:model="clientPhone" type="text" class="input input-bordered input-sm"
                                           placeholder="+380501234567"/>
                                    @error('clientPhone')<span class="text-error text-xs">{{ $message }}</span>@enderror
                                </div>
                                <div class="form-control sm:col-span-2">
                                    <label class="label"><span class="label-text">Email</span></label>
                                    <input wire:model="clientEmail" type="email" class="input input-bordered input-sm"
                                           placeholder="email@example.com"/>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button wire:click="saveClient" class="btn btn-primary btn-sm">Зберегти клієнта</button>
                                <button wire:click="$set('showClientForm', false)" class="btn btn-ghost btn-sm">Скасувати</button>
                            </div>
                        </div>
                    @else
                        {{-- Пошук клієнта --}}
                        <div class="relative">
                            <label class="input input-bordered flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input wire:model.live.debounce.300ms="clientSearch"
                                       type="text"
                                       placeholder="Пошук за ім'ям або телефоном..."
                                       class="grow text-sm"
                                       autocomplete="off"/>
                            </label>

                            @if($showClientDropdown)
                            <div class="absolute z-50 w-full mt-1 bg-base-100 border border-base-300 rounded-lg shadow-lg">
                                @forelse($this->clientResults as $client)
                                    <button wire:click="selectClient({{ $client->id }})"
                                            class="w-full text-left px-4 py-3 hover:bg-base-200 flex justify-between items-center">
                                        <span class="font-medium text-sm">{{ $client->name }}</span>
                                        <span class="text-xs text-base-content/60">{{ $client->phone }}</span>
                                    </button>
                                @empty
                                    <div class="px-4 py-3 text-sm text-base-content/60">
                                        Не знайдено.
                                        <button wire:click="createNewClient" class="link link-primary ml-1">Створити нового</button>
                                    </div>
                                @endforelse
                            </div>
                            @endif
                        </div>

                        @error('clientId')
                            <span class="text-error text-xs">{{ $message }}</span>
                        @enderror

                        <button wire:click="createNewClient" class="btn btn-outline btn-sm w-fit gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Новий клієнт
                        </button>
                    @endif
                </div>
            </div>

            {{-- Пристрій --}}
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-base">Пристрій</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        {{-- Тип --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Тип *</span></label>
                            <select wire:model.live="deviceTypeId" class="select select-bordered select-sm">
                                <option value="">Оберіть тип...</option>
                                @foreach($this->deviceTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('deviceTypeId')<span class="text-error text-xs">{{ $message }}</span>@enderror
                        </div>

                        {{-- Бренд --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Бренд *</span></label>
                            <select wire:model.live="brandId" class="select select-bordered select-sm"
                                    @disabled(!$deviceTypeId)>
                                <option value="">Оберіть бренд...</option>
                                @foreach($this->brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                            @error('brandId')<span class="text-error text-xs">{{ $message }}</span>@enderror
                        </div>

                        {{-- Модель --}}
<div class="form-control">
    <label class="label"><span class="label-text">Модель *</span></label>
    @if(!$showNewModel)
        <div class="relative">
            <input wire:model.live.debounce.300ms="modelSearch"
                   type="text"
                   class="input input-bordered input-sm w-full"
                   placeholder="{{ $brandId ? 'Пошук моделі...' : 'Спочатку оберіть бренд' }}"
                   @disabled(!$brandId)
                   autocomplete="off"
                   wire:focus="$set('showModelDropdown', true)"/>

            @if($modelId)
                <button wire:click="clearModel"
                        class="absolute right-2 top-1/2 -translate-y-1/2 btn btn-ghost btn-xs">✕</button>
            @endif

            @if($showModelDropdown && $brandId && strlen($modelSearch) > 0)
            <div class="absolute z-50 w-full mt-1 bg-base-100 border border-base-300 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                @forelse($this->filteredModels as $model)
                    <button wire:click="selectModel({{ $model->id }}, '{{ addslashes($model->name) }}')"
                            class="w-full text-left px-3 py-2 hover:bg-base-200 text-sm">
                        {{ $model->name }}
                    </button>
                @empty
                    <div class="px-3 py-2 text-sm text-base-content/60">
                        Не знайдено.
                        <button wire:click="$set('showNewModel', true); $set('showModelDropdown', false); $set('newModelName', modelSearch)"
                                class="link link-primary ml-1">Додати "{{ $modelSearch }}"</button>
                    </div>
                @endforelse
                <div class="border-t border-base-200">
                    <button wire:click="$set('showNewModel', true); $set('showModelDropdown', false)"
                            class="w-full text-left px-3 py-2 hover:bg-base-200 text-sm text-primary">
                        + Додати нову модель
                    </button>
                </div>
            </div>
            @endif
        </div>

        @if($modelId)
            <span class="text-success text-xs mt-1">✓ Модель обрана</span>
        @endif
    @else
        <div class="flex gap-1">
            <input wire:model="newModelName" type="text"
                   class="input input-bordered input-sm flex-1"
                   placeholder="Назва моделі"/>
            <button wire:click="saveNewModel" class="btn btn-primary btn-sm">✓</button>
            <button wire:click="$set('showNewModel', false)" class="btn btn-ghost btn-sm">✕</button>
        </div>
    @endif
    @error('modelId')<span class="text-error text-xs">{{ $message }}</span>@enderror
</div>
                    </div>

                    {{-- Додаткові поля --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-2">
                        <div class="form-control">
                            <label class="label"><span class="label-text">Серійний номер</span></label>
                            <input wire:model="serialNumber" type="text" class="input input-bordered input-sm"
                                   placeholder="SN123456789"/>
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text">IMEI</span></label>
                            <input wire:model="imei" type="text" class="input input-bordered input-sm"
                                   placeholder="352000000000000"/>
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text">Колір</span></label>
                            <input wire:model="color" type="text" class="input input-bordered input-sm"
                                   placeholder="Чорний"/>
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text">Зовнішній вигляд</span></label>
                            <input wire:model="appearance" type="text" class="input input-bordered input-sm"
                                   placeholder="Подряпини на корпусі"/>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Несправність --}}
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-base">Несправність</h2>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Скарга клієнта *</span></label>
                        <textarea wire:model="malfunction"
                                  class="textarea textarea-bordered"
                                  rows="3"
                                  placeholder="Опишіть проблему зі слів клієнта..."></textarea>
                        @error('malfunction')<span class="text-error text-xs">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Внутрішні нотатки</span></label>
                        <textarea wire:model="notes"
                                  class="textarea textarea-bordered"
                                  rows="2"
                                  placeholder="Нотатки для персоналу (клієнт не бачить)..."></textarea>
                    </div>
                </div>
            </div>

        </div>

        {{-- Права колонка --}}
        <div class="flex flex-col gap-4">

            {{-- Параметри заявки --}}
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-base">Параметри</h2>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Тип заявки</span></label>
                        <select wire:model="type" class="select select-bordered select-sm">
                            <option value="repair">Ремонт</option>
                            <option value="express">Експрес</option>
                            <option value="diagnostic">Діагностика</option>
                            <option value="maintenance">Обслуговування</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Пріоритет</span></label>
                        <div class="flex gap-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model="priority" value="normal" class="radio radio-sm"/>
                                <span class="text-sm">Звичайний</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model="priority" value="urgent" class="radio radio-sm radio-error"/>
                                <span class="text-sm text-error">Терміново</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Очікувана дата</span></label>
                        <input wire:model="estimatedDate" type="date" class="input input-bordered input-sm"/>
                        @error('estimatedDate')<span class="text-error text-xs">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Передоплата (₴)</span></label>
                        <input wire:model="prepayment" type="number" min="0" class="input input-bordered input-sm"
                               placeholder="0"/>
                        @error('prepayment')<span class="text-error text-xs">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            {{-- Кнопки --}}
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <button wire:click="save" wire:loading.attr="disabled"
                            class="btn btn-primary w-full gap-2">
                        <span wire:loading wire:target="save" class="loading loading-spinner loading-sm"></span>
                        Створити заявку
                    </button>
                    <a href="{{ route('orders.index') }}" class="btn btn-ghost w-full">
                        Скасувати
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>