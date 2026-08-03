<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Скидаємо кеш дозволів
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ──────────────────────────────────────────
        // Всі дозволи системи
        // ──────────────────────────────────────────
        $permissions = [

            // Клієнти
            'clients.view',
            'clients.create',
            'clients.edit',
            'clients.delete',
            'clients.blacklist',

            // Пристрої
            'devices.view',
            'devices.create',
            'devices.edit',

            // Заявки
            'orders.view',           // свої або всі — залежить від ролі
            'orders.view_all',       // бачити всі заявки (не лише свої)
            'orders.create',
            'orders.edit',
            'orders.delete',
            'orders.change_status',
            'orders.assign_engineer',
            'orders.add_comment',
            'orders.view_internal_comments',
            'orders.manage_tasks',
            'orders.print_receipt',
            'orders.issue',          // видати замовлення клієнту
            'orders.cancel',

            // Кошторис
            'estimates.view',
            'estimates.create',
            'estimates.edit',
            'estimates.approve',     // погодити кошторис

            // Склад
            'inventory.view',
            'inventory.create',
            'inventory.edit',
            'inventory.delete',
            'inventory.write_off',   // списання
            'inventory.adjustment',  // ручне коригування залишку

            // Закупівлі
            'purchases.view',
            'purchases.create',
            'purchases.edit',
            'purchases.receive',     // оприбуткувати товар
            'purchases.return',      // повернути постачальнику

            // Постачальники
            'suppliers.view',
            'suppliers.create',
            'suppliers.edit',
            'suppliers.delete',

            // Фінанси
            'finance.view',
            'finance.view_accounts',
            'finance.create_transaction',
            'finance.transfer',      // переказ між рахунками
            'finance.edit_transaction',

            // Витрати
            'expenses.view',
            'expenses.create',
            'expenses.edit',
            'expenses.delete',
            'expenses.pay',          // оплатити витрату

            // Борги перед постачальниками
            'debts.view',
            'debts.pay',

            // Зарплата
            'salary.view',           // бачити власну ЗП
            'salary.view_all',       // бачити ЗП всіх
            'salary.manage',         // керувати нарахуваннями
            'salary.pay',            // виплачувати ЗП
            'salary.manage_bonuses', // премії та утримання

            // Гарантія
            'warranty.view',
            'warranty.create',
            'warranty.claims',       // обробляти гарантійні звернення

            // Звіти
            'reports.view',          // базові звіти
            'reports.financial',     // фінансові звіти
            'reports.export',        // експорт у Excel/PDF

            // Довідники
            'references.view',
            'references.manage',     // редагувати довідники

            // Сповіщення
            'notifications.manage',

            // Налаштування системи
            'settings.view',
            'settings.manage',

            // Користувачі та ролі
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.manage_roles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $this->command->info('✅ Створено дозволів: ' . count($permissions));

        // ──────────────────────────────────────────
        // Ролі та їх дозволи
        // ──────────────────────────────────────────

        // ADMIN — повний доступ до всього
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());
        $this->command->info('✅ Роль admin — всі дозволи');

        // MANAGER — робота з клієнтами, заявками, складом
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $manager->syncPermissions([
            'clients.view', 'clients.create', 'clients.edit', 'clients.blacklist',
            'devices.view', 'devices.create', 'devices.edit',
            'orders.view', 'orders.view_all', 'orders.create', 'orders.edit',
            'orders.change_status', 'orders.assign_engineer', 'orders.add_comment',
            'orders.view_internal_comments', 'orders.manage_tasks',
            'orders.print_receipt', 'orders.issue', 'orders.cancel',
            'estimates.view', 'estimates.create', 'estimates.edit', 'estimates.approve',
            'inventory.view', 'inventory.create', 'inventory.edit',
            'purchases.view', 'purchases.create', 'purchases.edit', 'purchases.receive',
            'suppliers.view', 'suppliers.create', 'suppliers.edit',
            'expenses.view', 'expenses.create',
            'debts.view',
            'salary.view',
            'warranty.view', 'warranty.create', 'warranty.claims',
            'reports.view',
            'references.view', 'references.manage',
            'notifications.manage',
        ]);
        $this->command->info('✅ Роль manager — ' . $manager->permissions()->count() . ' дозволів');

        // ENGINEER — тільки свої заявки та завдання
        $engineer = Role::firstOrCreate(['name' => 'engineer', 'guard_name' => 'web']);
        $engineer->syncPermissions([
            'clients.view',
            'devices.view', 'devices.create', 'devices.edit',
            'orders.view',               // тільки свої (контролюється в Policy)
            'orders.change_status',
            'orders.add_comment',
            'orders.manage_tasks',
            'estimates.view', 'estimates.create', 'estimates.edit',
            'inventory.view',
            'purchases.view',
            'warranty.view',
            'salary.view',               // тільки своя
            'references.view',
        ]);
        $this->command->info('✅ Роль engineer — ' . $engineer->permissions()->count() . ' дозволів');

        // ACCOUNTANT — фінанси, зарплата, звіти
        $accountant = Role::firstOrCreate(['name' => 'accountant', 'guard_name' => 'web']);
        $accountant->syncPermissions([
            'clients.view',
            'orders.view', 'orders.view_all',
            'estimates.view',
            'inventory.view',
            'purchases.view',
            'suppliers.view',
            'finance.view', 'finance.view_accounts',
            'finance.create_transaction', 'finance.transfer',
            'expenses.view', 'expenses.create', 'expenses.edit', 'expenses.pay',
            'debts.view', 'debts.pay',
            'salary.view', 'salary.view_all', 'salary.manage',
            'salary.pay', 'salary.manage_bonuses',
            'warranty.view',
            'reports.view', 'reports.financial', 'reports.export',
            'references.view',
        ]);
        $this->command->info('✅ Роль accountant — ' . $accountant->permissions()->count() . ' дозволів');

        // CLIENT — тільки портал клієнта (guard api)
        $client = Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']);
        $client->syncPermissions([]);
        $this->command->info('✅ Роль client — без дозволів (доступ через client_token)');

        $this->command->info('');
        $this->command->info('Матриця дозволів:');
        $this->command->table(
            ['Роль', 'Кількість дозволів'],
            [
                ['admin',      Permission::count()],
                ['manager',    $manager->permissions()->count()],
                ['engineer',   $engineer->permissions()->count()],
                ['accountant', $accountant->permissions()->count()],
                ['client',     0],
            ]
        );
    }
}
