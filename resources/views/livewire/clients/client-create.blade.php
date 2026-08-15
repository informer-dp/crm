<div>
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('clients.index') }}" class="btn btn-ghost btn-sm gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Назад
        </a>
        <h1 class="text-2xl font-bold">Новий клієнт</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <div class="lg:col-span-2 flex flex-col gap-4">

            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-base">Основна інформація</h2>

                    <div class="flex gap-4 mb-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" wire:model.live="clientType" value="individual" class="radio radio-sm"/>
                            <span class="text-sm">Фізична особа</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" wire:model.live="clientType" value="legal" class="radio radio-sm"/>
                            <span class="text-sm">Юридична особа</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="form-control sm:col-span-2">
                            <label class="label">
                                <span class="label-text">
                                    {{ $clientType === 'legal' ? 'Назва компанії *' : 'ПІБ *' }}
                                </span>
                            </label>
                            <input wire:model="name" type="text"
                                   class="input input-bordered"
                                   placeholder="{{ $clientType === 'legal' ? 'ТОВ Назва' : 'Іван Петренко' }}"/>
                            @error('name')<span class="text-error text-xs">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Телефон *</span></label>
                            <input wire:model.lazy="phone" type="text"
                                   class="input input-bordered"
                                   placeholder="+380501234567"/>
                            @error('phone')<span class="text-error text-xs">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Email</span></label>
                            <input wire:model="email" type="email"
                                   class="input input-bordered"
                                   placeholder="email@example.com"/>
                            @error('email')<span class="text-error text-xs">{{ $message }}</span>@enderror
                        </div>

                        @if($clientType === 'legal')
                        <div class="form-control">
                            <label class="label"><span class="label-text">ЄДРПОУ</span></label>
                            <input wire:model="taxCode" type="text"
                                   class="input input-bordered" placeholder="12345678"/>
                        </div>
                        @else
                        <div class="form-control">
                            <label class="label"><span class="label-text">ІПН</span></label>
                            <input wire:model="taxCode" type="text"
                                   class="input input-bordered" placeholder="1234567890"/>
                        </div>
                        @endif

                        <div class="form-control">
                            <label class="label"><span class="label-text">Місто</span></label>
                            <input wire:model="city" type="text"
                                   class="input input-bordered" placeholder="Дніпро"/>
                        </div>

                        <div class="form-control sm:col-span-2">
                            <label class="label"><span class="label-text">Адреса</span></label>
                            <input wire:model="address" type="text"
                                   class="input input-bordered" placeholder="вул. Назва, 1"/>
                        </div>
                    </div>
                </div>
            </div>

            @if($clientType === 'legal')
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-base">Контактна особа</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="form-control">
                            <label class="label"><span class="label-text">ПІБ</span></label>
                            <input wire:model="contactPerson" type="text"
                                   class="input input-bordered" placeholder="Іван Петренко"/>
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text">Телефон</span></label>
                            <input wire:model="contactPhone" type="text"
                                   class="input input-bordered" placeholder="+380501234567"/>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-base">Нотатки</h2>
                    <textarea wire:model="notes" class="textarea textarea-bordered w-full"
                              rows="3" placeholder="Додаткова інформація..."></textarea>
                </div>
            </div>

        </div>

        <div class="flex flex-col gap-4">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-base">Параметри</h2>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="isVip" type="checkbox" class="checkbox checkbox-warning"/>
                        <span class="text-sm">VIP клієнт</span>
                    </label>
                </div>
            </div>

            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-2">
                    <button wire:click="save"
                            wire:loading.attr="disabled"
                            class="btn btn-primary w-full gap-2">
                        <span wire:loading wire:target="save"
                              class="loading loading-spinner loading-sm"></span>
                        Створити клієнта
                    </button>
                    <a href="{{ route('clients.index') }}" class="btn btn-ghost w-full">
                        Скасувати
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>