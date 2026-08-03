<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ── Загальні ──────────────────────────────
            ['group' => 'general', 'key' => 'company_name',     'value' => 'Мій Сервісний Центр',  'type' => 'string'],
            ['group' => 'general', 'key' => 'company_phone',    'value' => '+380000000000',         'type' => 'string'],
            ['group' => 'general', 'key' => 'company_email',    'value' => 'info@myservice.ua',     'type' => 'string'],
            ['group' => 'general', 'key' => 'company_address',  'value' => 'м. Дніпро, вул. ...',   'type' => 'string'],
            ['group' => 'general', 'key' => 'company_website',  'value' => 'https://myservice.ua',  'type' => 'string'],
            ['group' => 'general', 'key' => 'working_hours',    'value' => 'Пн-Пт 9:00-18:00, Сб 10:00-16:00', 'type' => 'string'],
            ['group' => 'general', 'key' => 'timezone',         'value' => 'Europe/Kiev',           'type' => 'string'],
            ['group' => 'general', 'key' => 'currency',         'value' => 'UAH',                   'type' => 'string'],
            ['group' => 'general', 'key' => 'currency_symbol',  'value' => '₴',                     'type' => 'string'],

            // ── Квитанції ─────────────────────────────
            ['group' => 'receipt', 'key' => 'order_prefix',     'value' => 'SC',   'type' => 'string'],
            ['group' => 'receipt', 'key' => 'receipt_prefix',   'value' => 'RC',   'type' => 'string'],
            ['group' => 'receipt', 'key' => 'warranty_prefix',  'value' => 'WR',   'type' => 'string'],
            ['group' => 'receipt', 'key' => 'check_code_length','value' => '6',    'type' => 'integer'],
            ['group' => 'receipt', 'key' => 'show_engineer_name_on_receipt', 'value' => '1', 'type' => 'boolean'],
            ['group' => 'receipt', 'key' => 'receipt_footer_text',
             'value' => 'Дякуємо за звернення! Зберігайте чек протягом гарантійного терміну.',
             'type'  => 'string'],

            // ── Гарантія ──────────────────────────────
            ['group' => 'warranty', 'key' => 'default_warranty_days',    'value' => '30',  'type' => 'integer'],
            ['group' => 'warranty', 'key' => 'warranty_terms_text',
             'value' => "Гарантія надається на виконані роботи та встановлені запчастини.\nГарантія не поширюється на механічні пошкодження, потрапляння рідини та несанкціоноване втручання третіх осіб.",
             'type'  => 'string'],

            // ── Сповіщення ────────────────────────────
            ['group' => 'notifications', 'key' => 'sms_enabled',      'value' => '0', 'type' => 'boolean'],
            ['group' => 'notifications', 'key' => 'telegram_enabled',  'value' => '1', 'type' => 'boolean'],
            ['group' => 'notifications', 'key' => 'viber_enabled',     'value' => '0', 'type' => 'boolean'],
            ['group' => 'notifications', 'key' => 'whatsapp_enabled',  'value' => '0', 'type' => 'boolean'],
            ['group' => 'notifications', 'key' => 'telegram_bot_token','value' => '',  'type' => 'string'],
            ['group' => 'notifications', 'key' => 'sms_provider',      'value' => '',  'type' => 'string'],
            ['group' => 'notifications', 'key' => 'sms_api_key',       'value' => '',  'type' => 'string'],
            ['group' => 'notifications', 'key' => 'sms_sender_name',   'value' => '',  'type' => 'string'],

            // ── Зарплата ──────────────────────────────
            ['group' => 'salary', 'key' => 'salary_period_close_day', 'value' => '31', 'type' => 'integer'],
            ['group' => 'salary', 'key' => 'salary_auto_calculate',   'value' => '1',  'type' => 'boolean'],

            // ── Склад ─────────────────────────────────
            ['group' => 'inventory', 'key' => 'low_stock_notify',      'value' => '1', 'type' => 'boolean'],
            ['group' => 'inventory', 'key' => 'auto_deduct_from_stock','value' => '1', 'type' => 'boolean'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                [
                    'value'      => $setting['value'],
                    'type'       => $setting['type'],
                    'group'      => $setting['group'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('✅ Налаштування системи: ' . count($settings));
        $this->command->warn('   Не забудь оновити:');
        $this->command->warn('   - company_name, company_phone, company_address');
        $this->command->warn('   - telegram_bot_token (якщо використовуєш Telegram)');
        $this->command->warn('   - sms_api_key (якщо використовуєш SMS)');
    }
}
