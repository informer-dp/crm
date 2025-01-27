<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Створення ролей
$adminRole = Role::create(['name' => 'admin']);
$managerRole = Role::create(['name' => 'manager']);
$engineerRole = Role::create(['name' => 'engineer']);

// Створення прав
$permissions = ['view orders', 'edit orders', 'delete orders', 'manage users'];

foreach ($permissions as $permission) {
    Permission::create(['name' => $permission]);
}

// Прив'язка прав до ролей
$adminRole->givePermissionTo(Permission::all());
$managerRole->givePermissionTo(['view orders', 'edit orders']);
$engineerRole->givePermissionTo(['view orders']);

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
