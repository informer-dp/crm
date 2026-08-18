<?php

namespace App\Livewire\Settings;

use App\Models\Setting;
use Livewire\Component;

class SettingsIndex extends Component
{
    public string $activeTab = 'general';

    // ── Загальні ──────────────────────────────
    public string $companyName = '';
    public string $companyPhone = '';
    public string $companyEmail = '';
    public string $companyAddress = '';
    public string $companyWebsite = '';
    public string $workingHours = '';
    public string $currency = 'UAH';
    public string $currencySymbol = '₴';

    // ── Документи ─────────────────────────────
    public string $orderPrefix = '';
    public string $receiptPrefix = '';
    public string $warrantyPrefix = '';
    public string $checkCodeLength = '6';
    public string $receiptFooterText = '';
    public string $warrantyTermsText = '';
    public string $defaultWarrantyDays = '';
    public bool $showEngineerOnReceipt = true;

    // ── Сповіщення ────────────────────────────
    public bool $telegramEnabled = false;
    public string $telegramBotToken = '';
    public bool $smsEnabled = false;
    public string $smsProvider = '';
    public string $smsApiKey = '';
    public string $smsSenderName = '';
    public bool $viberEnabled = false;

    // ── Зарплата ──────────────────────────────
    public string $salaryPeriodCloseDay = '31';
    public bool $salaryAutoCalculate = true;

    // ── Склад ─────────────────────────────────
    public bool $lowStockNotify = true;
    public bool $autoDeductFromStock = true;

    // --- Чеклист -------------------------------
    public string $checklistSmartphone = '';
    public string $checklistLaptop = '';
    public string $checklistUniversal = '';

    public function mount(): void
    {
        // Загальні
        $this->companyName    = Setting::get('company_name', '');
        $this->companyPhone   = Setting::get('company_phone', '');
        $this->companyEmail   = Setting::get('company_email', '');
        $this->companyAddress = Setting::get('company_address', '');
        $this->companyWebsite = Setting::get('company_website', '');
        $this->workingHours   = Setting::get('working_hours', '');
        $this->currency       = Setting::get('currency', 'UAH');
        $this->currencySymbol = Setting::get('currency_symbol', '₴');

        // Документи
        $this->orderPrefix          = Setting::get('order_prefix', 'SC');
        $this->receiptPrefix        = Setting::get('receipt_prefix', 'RC');
        $this->warrantyPrefix       = Setting::get('warranty_prefix', 'WR');
        $this->checkCodeLength      = (string)Setting::get('check_code_length', '6');
        $this->receiptFooterText    = Setting::get('receipt_footer_text', '');
        $this->warrantyTermsText    = Setting::get('warranty_terms_text', '');
        $this->defaultWarrantyDays  = (string)Setting::get('default_warranty_days', '30');
        $this->showEngineerOnReceipt = (bool)Setting::get('show_engineer_name_on_receipt', true);

        // Сповіщення
        $this->telegramEnabled  = (bool)Setting::get('telegram_enabled', false);
        $this->telegramBotToken = Setting::get('telegram_bot_token', '');
        $this->smsEnabled       = (bool)Setting::get('sms_enabled', false);
        $this->smsProvider      = Setting::get('sms_provider', '');
        $this->smsApiKey        = Setting::get('sms_api_key', '');
        $this->smsSenderName    = Setting::get('sms_sender_name', '');
        $this->viberEnabled     = (bool)Setting::get('viber_enabled', false);

        // Зарплата
        $this->salaryPeriodCloseDay = (string)Setting::get('salary_period_close_day', '31');
        $this->salaryAutoCalculate  = (bool)Setting::get('salary_auto_calculate', true);

        // Склад
        $this->lowStockNotify      = (bool)Setting::get('low_stock_notify', true);
        $this->autoDeductFromStock = (bool)Setting::get('auto_deduct_from_stock', true);

        // Чеклист тезнічної карти ремонту
        $this->checklistSmartphone = Setting::get('checklist_smartphone', "Екран\nСенсор\nКамера (фронт/основна)\nДинамік\nМікрофон\nКнопки гучності\nКнопка живлення\nРоз'єм зарядки\nSIM-слот\nWi-Fi\nBluetooth\nFace ID / Touch ID\nВібромотор\nАкумулятор (% заряду)");
        $this->checklistLaptop = Setting::get('checklist_laptop', "Екран (засвіти/пікселі)\nКлавіатура\nТачпад\nWi-Fi\nBluetooth\nВебкамера\nМікрофон\nДинаміки\nUSB-порти\nЗарядка\nАкумулятор\nCD/DVD привід\nKensington замок");
        $this->checklistUniversal = Setting::get('checklist_universal', "Живлення\nЕкран\nКнопки\nПорти/роз'єми\nЗвук\nАкумулятор");
    }

    public function saveGeneral(): void
    {
        $this->validate([
            'companyName'  => 'required|min:2',
            'companyPhone' => 'required',
        ], [
            'companyName.required'  => 'Введіть назву компанії',
            'companyPhone.required' => 'Введіть телефон',
        ]);

        Setting::set('company_name',    $this->companyName);
        Setting::set('company_phone',   $this->companyPhone);
        Setting::set('company_email',   $this->companyEmail);
        Setting::set('company_address', $this->companyAddress);
        Setting::set('company_website', $this->companyWebsite);
        Setting::set('working_hours',   $this->workingHours);
        Setting::set('currency',        $this->currency);
        Setting::set('currency_symbol', $this->currencySymbol);

        session()->flash('success', 'Загальні налаштування збережено');
    }

    public function saveDocuments(): void
    {
        Setting::set('order_prefix',                  $this->orderPrefix);
        Setting::set('receipt_prefix',                $this->receiptPrefix);
        Setting::set('warranty_prefix',               $this->warrantyPrefix);
        Setting::set('check_code_length',             $this->checkCodeLength, 'integer');
        Setting::set('receipt_footer_text',           $this->receiptFooterText);
        Setting::set('warranty_terms_text',           $this->warrantyTermsText);
        Setting::set('default_warranty_days',         $this->defaultWarrantyDays, 'integer');
        Setting::set('show_engineer_name_on_receipt', $this->showEngineerOnReceipt ? '1' : '0', 'boolean');

        session()->flash('success', 'Налаштування документів збережено');
    }

    public function saveNotifications(): void
    {
        Setting::set('telegram_enabled',  $this->telegramEnabled ? '1' : '0', 'boolean');
        Setting::set('telegram_bot_token', $this->telegramBotToken);
        Setting::set('sms_enabled',       $this->smsEnabled ? '1' : '0', 'boolean');
        Setting::set('sms_provider',      $this->smsProvider);
        Setting::set('sms_api_key',       $this->smsApiKey);
        Setting::set('sms_sender_name',   $this->smsSenderName);
        Setting::set('viber_enabled',     $this->viberEnabled ? '1' : '0', 'boolean');

        session()->flash('success', 'Налаштування сповіщень збережено');
    }

    public function saveSalary(): void
    {
        Setting::set('salary_period_close_day', $this->salaryPeriodCloseDay, 'integer');
        Setting::set('salary_auto_calculate',   $this->salaryAutoCalculate ? '1' : '0', 'boolean');

        session()->flash('success', 'Налаштування зарплати збережено');
    }

    public function saveInventory(): void
    {
        Setting::set('low_stock_notify',       $this->lowStockNotify ? '1' : '0', 'boolean');
        Setting::set('auto_deduct_from_stock', $this->autoDeductFromStock ? '1' : '0', 'boolean');

        session()->flash('success', 'Налаштування складу збережено');
    }

    public function saveChecklist(): void
{
    Setting::set('checklist_smartphone', $this->checklistSmartphone);
    Setting::set('checklist_laptop',     $this->checklistLaptop);
    Setting::set('checklist_universal',  $this->checklistUniversal);
    session()->flash('success', 'Чеклист збережено');
}

    public function render()
    {
        return view('livewire.settings.settings-index')
            ->extends('layouts.app')
            ->section('content');
    }
}