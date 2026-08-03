<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountsSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            [
                'name'      => 'Готівкова каса',
                'type'      => 'cash',
                'balance'   => 0,
                'currency'  => 'UAH',
                'is_active' => true,
            ],
            [
                'name'      => 'Термінал (безготівкові)',
                'type'      => 'terminal',
                'balance'   => 0,
                'currency'  => 'UAH',
                'is_active' => true,
            ],
            [
                'name'      => 'Розрахунковий рахунок',
                'type'      => 'bank',
                'balance'   => 0,
                'currency'  => 'UAH',
                'is_active' => true,
            ],
        ];

        foreach ($accounts as $account) {
            DB::table('accounts')->updateOrInsert(
                ['name' => $account['name']],
                array_merge($account, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        $this->command->info('✅ Рахунки (каси): ' . count($accounts));
        $this->command->warn('   Не забудь вказати реальні поточні баланси після введення системи в роботу!');
    }
}
