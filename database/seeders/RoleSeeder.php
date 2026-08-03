<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**=
     * Run the database seeds.
     */
    public function run(): void
    {
        // Створити роль Admin
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Призначити роль користувачу з ID 1
        $user = User::find(1); // Змініть на ID потрібного користувача
        if ($user) {
            $user->assignRole($adminRole);
        }
    }
}