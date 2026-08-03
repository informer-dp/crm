<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Перевіряємо чи вже є адмін
        $admin = User::where('email', env('ADMIN_EMAIL', 'admin@myservice.ua'))->first();

        if ($admin) {
            $this->command->warn('⚠️  Адмін вже існує: ' . $admin->email);
        } else {
            $admin = User::create([
                'name'      => env('ADMIN_NAME', 'Адміністратор'),
                'email'     => env('ADMIN_EMAIL', 'admin@myservice.ua'),
                'password'  => Hash::make(env('ADMIN_PASSWORD', 'changeme123!')),
                'is_active' => true,
            ]);

            UserProfile::create([
                'user_id'  => $admin->id,
                'position' => 'Власник / Адміністратор',
                'color'    => '#6366f1',
            ]);

            $this->command->info('✅ Створено адміна: ' . $admin->email);
        }

        $admin->assignRole('admin');
        $this->command->info('✅ Роль admin призначено');
        $this->command->warn('   Зміни пароль адміна в .env (ADMIN_PASSWORD) або через інтерфейс!');
    }
}
