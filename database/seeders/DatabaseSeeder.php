<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('╔═══════════════════════════════════════╗');
        $this->command->info('║     CRM Сервісного Центру — Setup     ║');
        $this->command->info('╚═══════════════════════════════════════╝');
        $this->command->info('');

        $this->call([
            RolesAndPermissionsSeeder::class,
            AdminUserSeeder::class,
            DeviceTypesSeeder::class,
            ExpenseCategoriesSeeder::class,
            WarrantyRulesSeeder::class,
            PartCategoriesSeeder::class,
            AccountsSeeder::class,
            NotificationEventsSeeder::class,
            SettingsSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('╔═══════════════════════════════════════╗');
        $this->command->info('║          ✅ Setup завершено!           ║');
        $this->command->info('╚═══════════════════════════════════════╝');
        $this->command->info('');
        $this->command->warn('Наступні кроки:');
        $this->command->warn('1. Оновіть налаштування в таблиці settings');
        $this->command->warn('2. Для міграції даних зі старої бази:');
        $this->command->warn('   php artisan db:seed --class=MigrateOldDataSeeder');
    }
}