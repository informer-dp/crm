<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PartCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Акумулятори',
                'children' => [
                    'Акумулятори для смартфонів',
                    'Акумулятори для ноутбуків',
                    'Акумулятори для планшетів',
                ]
            ],
            [
                'name' => 'Дисплеї та сенсори',
                'children' => [
                    'Дисплейні модулі смартфонів',
                    'Дисплеї для ноутбуків',
                    'Тачскрін / сенсорне скло',
                ]
            ],
            [
                'name' => "Роз'єми та кнопки",
                'children' => [
                    "Роз'єми зарядки",
                    'Кнопки гучності та живлення',
                    'Шлейфи та перехідники',
                ]
            ],
            [
                'name' => 'Корпуси та кришки',
                'children' => [
                    'Задні кришки смартфонів',
                    'Корпуси ноутбуків',
                    'Рамки та середні частини',
                ]
            ],
            [
                'name' => 'Кабелі та зарядки',
                'children' => [
                    'Кабелі USB-C / Lightning',
                    'Блоки живлення',
                    'Бездротові зарядки',
                ]
            ],
            [
                'name' => 'Захисне скло та плівки',
                'children' => [
                    'Захисне скло',
                    'Захисні плівки',
                ]
            ],
            [
                'name' => 'Мікросхеми та компоненти',
                'children' => [
                    'Мікросхеми живлення',
                    'Пам\'ять та процесори',
                    'Пасивні компоненти',
                ]
            ],
            [
                'name' => 'Інструменти та витратні',
                'children' => [
                    'Термопаста та термопрокладки',
                    'Флюс та припій',
                    'Спирт та очисники',
                    'Скотч та клей',
                ]
            ],
        ];

        foreach ($categories as $category) {
            $parentId = DB::table('part_categories')->insertGetId([
                'parent_id'  => null,
                'name'       => $category['name'],
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($category['children'] ?? [] as $child) {
                DB::table('part_categories')->insert([
                    'parent_id'  => $parentId,
                    'name'       => $child,
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Категорії запчастин: ' . count($categories) . ' груп');
    }
}