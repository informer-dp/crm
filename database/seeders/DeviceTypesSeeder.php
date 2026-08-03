<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeviceTypesSeeder extends Seeder
{
    public function run(): void
    {
        // ──────────────────────────────────────────
        // Типи пристроїв
        // ──────────────────────────────────────────
        $types = [
            ['name' => 'Смартфон',   'icon' => 'smartphone'],
            ['name' => 'Ноутбук',    'icon' => 'laptop'],
            ['name' => 'Планшет',    'icon' => 'tablet'],
            ['name' => 'ПК',         'icon' => 'monitor'],
            ['name' => 'Моноблок',   'icon' => 'monitor'],
            ['name' => 'Принтер',    'icon' => 'printer'],
            ['name' => 'Консоль',    'icon' => 'gamepad-2'],
            ['name' => 'Навушники',  'icon' => 'headphones'],
            ['name' => 'Смарт-годинник', 'icon' => 'watch'],
        ];

        foreach ($types as $type) {
            DB::table('device_types')->updateOrInsert(
                ['name' => $type['name']],
                ['icon' => $type['icon'], 'is_active' => true,
                 'created_at' => now(), 'updated_at' => now()]
            );
        }

        $this->command->info('✅ Типи пристроїв: ' . count($types));

        // ──────────────────────────────────────────
        // Бренди — прив'язані до типів
        // ──────────────────────────────────────────
        $smartphoneId = DB::table('device_types')->where('name', 'Смартфон')->value('id');
        $laptopId     = DB::table('device_types')->where('name', 'Ноутбук')->value('id');
        $tabletId     = DB::table('device_types')->where('name', 'Планшет')->value('id');
        $pcId         = DB::table('device_types')->where('name', 'ПК')->value('id');

        $brands = [
            // Смартфони
            ['name' => 'Apple',       'device_type_id' => $smartphoneId],
            ['name' => 'Samsung',     'device_type_id' => $smartphoneId],
            ['name' => 'Xiaomi',      'device_type_id' => $smartphoneId],
            ['name' => 'Redmi',       'device_type_id' => $smartphoneId],
            ['name' => 'POCO',        'device_type_id' => $smartphoneId],
            ['name' => 'Realme',      'device_type_id' => $smartphoneId],
            ['name' => 'OPPO',        'device_type_id' => $smartphoneId],
            ['name' => 'OnePlus',     'device_type_id' => $smartphoneId],
            ['name' => 'Motorola',    'device_type_id' => $smartphoneId],
            ['name' => 'Nokia',       'device_type_id' => $smartphoneId],
            ['name' => 'Huawei',      'device_type_id' => $smartphoneId],
            ['name' => 'Honor',       'device_type_id' => $smartphoneId],
            ['name' => 'Google',      'device_type_id' => $smartphoneId],
            ['name' => 'Sony',        'device_type_id' => $smartphoneId],
            ['name' => 'Vivo',        'device_type_id' => $smartphoneId],
            ['name' => 'ZTE',         'device_type_id' => $smartphoneId],
            ['name' => 'Tecno',       'device_type_id' => $smartphoneId],
            ['name' => 'Infinix',     'device_type_id' => $smartphoneId],
            ['name' => 'Itel',        'device_type_id' => $smartphoneId],

            // Ноутбуки
            ['name' => 'Apple',       'device_type_id' => $laptopId],
            ['name' => 'Lenovo',      'device_type_id' => $laptopId],
            ['name' => 'HP',          'device_type_id' => $laptopId],
            ['name' => 'Dell',        'device_type_id' => $laptopId],
            ['name' => 'ASUS',        'device_type_id' => $laptopId],
            ['name' => 'Acer',        'device_type_id' => $laptopId],
            ['name' => 'MSI',         'device_type_id' => $laptopId],
            ['name' => 'Samsung',     'device_type_id' => $laptopId],
            ['name' => 'Huawei',      'device_type_id' => $laptopId],
            ['name' => 'Microsoft',   'device_type_id' => $laptopId],
            ['name' => 'Toshiba',     'device_type_id' => $laptopId],
            ['name' => 'Sony',        'device_type_id' => $laptopId],
            ['name' => 'LG',          'device_type_id' => $laptopId],

            // Планшети
            ['name' => 'Apple',       'device_type_id' => $tabletId],
            ['name' => 'Samsung',     'device_type_id' => $tabletId],
            ['name' => 'Lenovo',      'device_type_id' => $tabletId],
            ['name' => 'Huawei',      'device_type_id' => $tabletId],
            ['name' => 'Xiaomi',      'device_type_id' => $tabletId],

            // ПК — без прив'язки до конкретного типу (збираємо з будь-яких комплектуючих)
            ['name' => 'Intel',       'device_type_id' => $pcId],
            ['name' => 'AMD',         'device_type_id' => $pcId],
        ];

        foreach ($brands as $brand) {
            DB::table('brands')->updateOrInsert(
                ['name' => $brand['name'], 'device_type_id' => $brand['device_type_id']],
                ['logo' => null, 'is_active' => true,
                 'created_at' => now(), 'updated_at' => now()]
            );
        }

        $this->command->info('✅ Бренди: ' . count($brands));
    }
}
