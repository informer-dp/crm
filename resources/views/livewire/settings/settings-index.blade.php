<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Налаштування</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    {{-- Вкладки --}}
    <div style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:24px;border-bottom:2px solid #e5e7eb">
        @foreach([
            'general'       => 'Загальні',
            'documents'     => 'Документи',
            'notifications' => 'Сповіщення',
            'salary'        => 'Зарплата',
            'inventory'     => 'Склад',
        ] as $tab => $label)
        <button wire:click="$set('activeTab', '{{ $tab }}')"
                style="padding:8px 16px;font-size:14px;font-weight:500;border:none;cursor:pointer;border-radius:8px 8px 0 0;
                       {{ $activeTab === $tab
                           ? 'background:#6366f1;color:white;margin-bottom:-2px'
                           : 'background:transparent;color:#6b7280' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- Загальні --}}
    @if($activeTab === 'general')
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <h2 class="card-title text-base mb-4">Загальна інформація</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="form-control sm:col-span-2">
                    <label class="label"><span class="label-text">Назва сервісного центру *</span></label>
                    <input wire:model="companyName" type="text" class="input input-bordered"
                           placeholder="Сервісний центр"/>
                    @error('companyName')<span class="text-error text-xs">{{ $message }}</span>@enderror
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Телефон *</span></label>
                    <input wire:model="companyPhone" type="text" class="input input-bordered"
                           placeholder="+380501234567"/>
                    @error('companyPhone')<span class="text-error text-xs">{{ $message }}</span>@enderror
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Email</span></label>
                    <input wire:model="companyEmail" type="email" class="input input-bordered"/>
                </div>
                <div class="form-control sm:col-span-2">
                    <label class="label"><span class="label-text">Адреса</span></label>
                    <input wire:model="companyAddress" type="text" class="input input-bordered"
                           placeholder="м. Дніпро, вул. ..."/>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Сайт</span></label>
                    <input wire:model="companyWebsite" type="text" class="input input-bordered"
                           placeholder="https://..."/>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Години роботи</span></label>
                    <input wire:model="workingHours" type="text" class="input input-bordered"
                           placeholder="Пн-Пт 9:00-18:00"/>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Валюта</span></label>
                    <select wire:model="currency" class="select select-bordered">
                        <option value="UAH">UAH — Українська гривня</option>
                        <option value="USD">USD — Долар США</option>
                        <option value="EUR">EUR — Євро</option>
                    </select>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Символ валюти</span></label>
                    <input wire:model="currencySymbol" type="text" class="input input-bordered w-24"
                           placeholder="₴"/>
                </div>
            </div>
            <div class="mt-4">
                <button wire:click="saveGeneral" class="btn btn-primary">
                    Зберегти загальні налаштування
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Документи --}}
    @if($activeTab === 'documents')
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <h2 class="card-title text-base mb-4">Налаштування документів</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="form-control">
                    <label class="label"><span class="label-text">Префікс заявки</span></label>
                    <input wire:model="orderPrefix" type="text" class="input input-bordered"
                           placeholder="SC"/>
                    <label class="label"><span class="label-text-alt">Напр: SC-2026-00001</span></label>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Префікс квитанції</span></label>
                    <input wire:model="receiptPrefix" type="text" class="input input-bordered"
                           placeholder="RC"/>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Префікс гарантії</span></label>
                    <input wire:model="warrantyPrefix" type="text" class="input input-bordered"
                           placeholder="WR"/>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Довжина коду перевірки</span></label>
                    <select wire:model="checkCodeLength" class="select select-bordered">
                        <option value="4">4 цифри</option>
                        <option value="6">6 цифр</option>
                        <option value="8">8 цифр</option>
                    </select>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Гарантія за замовч. (днів)</span></label>
                    <input wire:model="defaultWarrantyDays" type="number" min="0"
                           class="input input-bordered"/>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Показувати інженера в квитанції</span></label>
                    <input wire:model="showEngineerOnReceipt" type="checkbox" class="toggle toggle-primary mt-2"/>
                </div>
                <div class="form-control sm:col-span-3">
                    <label class="label"><span class="label-text">Текст футера квитанції</span></label>
                    <textarea wire:model="receiptFooterText" class="textarea textarea-bordered" rows="2"></textarea>
                </div>
                <div class="form-control sm:col-span-3">
                    <label class="label"><span class="label-text">Умови гарантії (текст за замовч.)</span></label>
                    <textarea wire:model="warrantyTermsText" class="textarea textarea-bordered" rows="4"></textarea>
                </div>
            </div>
            <div class="mt-4">
                <button wire:click="saveDocuments" class="btn btn-primary">
                    Зберегти налаштування документів
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Сповіщення --}}
    @if($activeTab === 'notifications')
    <div class="flex flex-col gap-4">

        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-bold">Telegram</h2>
                        <p class="text-sm text-base-content/60">Сповіщення персоналу і клієнтів через Telegram бота</p>
                    </div>
                    <input wire:model.live="telegramEnabled" type="checkbox" class="toggle toggle-primary"/>
                </div>
                @if($telegramEnabled)
                <div class="form-control mt-3">
                    <label class="label"><span class="label-text">Bot Token</span></label>
                    <input wire:model="telegramBotToken" type="text"
                           class="input input-bordered font-mono text-sm"
                           placeholder="1234567890:ABCdef..."/>
                    <label class="label">
                        <span class="label-text-alt">
                            Отримати у <a href="https://t.me/BotFather" target="_blank" class="link link-primary">@BotFather</a>
                        </span>
                    </label>
                </div>
                @endif
            </div>
        </div>

        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-bold">SMS</h2>
                        <p class="text-sm text-base-content/60">SMS сповіщення клієнтам</p>
                    </div>
                    <input wire:model.live="smsEnabled" type="checkbox" class="toggle toggle-primary"/>
                </div>
                @if($smsEnabled)
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-3">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Провайдер</span></label>
                        <select wire:model="smsProvider" class="select select-bordered select-sm">
                            <option value="">Оберіть...</option>
                            <option value="turbosms">TurboSMS</option>
                            <option value="alphasms">AlphaSMS</option>
                            <option value="smsclub">SMS Club</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">API ключ</span></label>
                        <input wire:model="smsApiKey" type="text"
                               class="input input-bordered input-sm font-mono"/>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Ім'я відправника</span></label>
                        <input wire:model="smsSenderName" type="text"
                               class="input input-bordered input-sm"
                               placeholder="ServiceSC"/>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-bold">Viber</h2>
                        <p class="text-sm text-base-content/60">Сповіщення клієнтам через Viber</p>
                    </div>
                    <input wire:model="viberEnabled" type="checkbox" class="toggle toggle-primary"/>
                </div>
                @if($viberEnabled)
                <div class="alert alert-info mt-3">
                    <span class="text-sm">Налаштування Viber Business будуть додані в наступній версії.</span>
                </div>
                @endif
            </div>
        </div>

        <button wire:click="saveNotifications" class="btn btn-primary w-fit">
            Зберегти налаштування сповіщень
        </button>
    </div>
    @endif

    {{-- Зарплата --}}
    @if($activeTab === 'salary')
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <h2 class="card-title text-base mb-4">Налаштування зарплати</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="form-control">
                    <label class="label"><span class="label-text">День закриття розрахункового періоду</span></label>
                    <select wire:model="salaryPeriodCloseDay" class="select select-bordered">
                        <option value="28">28-е число</option>
                        <option value="30">30-е число</option>
                        <option value="31">Останній день місяця</option>
                    </select>
                    <label class="label">
                        <span class="label-text-alt">Коли автоматично закривається місячний період</span>
                    </label>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Автоматичний розрахунок</span></label>
                    <input wire:model="salaryAutoCalculate" type="checkbox" class="toggle toggle-primary mt-2"/>
                    <label class="label">
                        <span class="label-text-alt">Автоматично розраховувати бонуси при закритті періоду</span>
                    </label>
                </div>
            </div>
            <div class="mt-4">
                <button wire:click="saveSalary" class="btn btn-primary">
                    Зберегти налаштування зарплати
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Склад --}}
    @if($activeTab === 'inventory')
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <h2 class="card-title text-base mb-4">Налаштування складу</h2>
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between p-3 bg-base-200 rounded-lg">
                    <div>
                        <p class="font-medium text-sm">Сповіщення про мінімальний залишок</p>
                        <p class="text-xs text-base-content/60">Повідомляти менеджера коли залишок нижче мінімуму</p>
                    </div>
                    <input wire:model="lowStockNotify" type="checkbox" class="toggle toggle-primary"/>
                </div>
                <div class="flex items-center justify-between p-3 bg-base-200 rounded-lg">
                    <div>
                        <p class="font-medium text-sm">Автоматичне списання зі складу</p>
                        <p class="text-xs text-base-content/60">Автоматично зменшувати залишок при додаванні запчастини в кошторис</p>
                    </div>
                    <input wire:model="autoDeductFromStock" type="checkbox" class="toggle toggle-primary"/>
                </div>
            </div>
            <div class="mt-4">
                <button wire:click="saveInventory" class="btn btn-primary">
                    Зберегти налаштування складу
                </button>
            </div>
        </div>
    </div>
    @endif

</div>