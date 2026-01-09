<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@crm.local'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
            ]
        );

        $user->assignRole('admin');
    }
}
