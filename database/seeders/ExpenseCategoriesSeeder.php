<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Постійні витрати
            [
                'name' => 'Оренда та приміщення',
                'type' => 'fixed',
                'children' => [
                    ['name' => 'Оренда офісу/сервісного приміщення', 'type' => 'fixed'],
                    ['name' => 'Комунальні послуги',                  'type' => 'variable'],
                    ['name' => 'Електроенергія',                      'type' => 'variable'],
                    ['name' => 'Вода',                                'type' => 'variable'],
                    ['name' => 'Опалення',                            'type' => 'variable'],
                    ['name' => 'Охорона/сигналізація',                'type' => 'fixed'],
                ],
            ],
            [
                'name' => 'Зв\'язок та інтернет',
                'type' => 'fixed',
                'children' => [
                    ['name' => 'Мобільний зв\'язок',  'type' => 'fixed'],
                    ['name' => 'Інтернет',             'type' => 'fixed'],
                    ['name' => 'IP-телефонія',         'type' => 'fixed'],
                ],
            ],
            [
                'name' => 'Програмне забезпечення та хостинг',
                'type' => 'fixed',
                'children' => [
                    ['name' => 'Хостинг та сервери',    'type' => 'fixed'],
                    ['name' => 'Домен',                 'type' => 'one_time'],
                    ['name' => 'CRM / ПЗ (підписка)',   'type' => 'fixed'],
                    ['name' => 'Антивірус/безпека',     'type' => 'fixed'],
                    ['name' => 'Ліцензії на ПЗ',        'type' => 'one_time'],
                ],
            ],

            // Змінні витрати
            [
                'name' => 'Матеріали та витратні',
                'type' => 'variable',
                'children' => [
                    ['name' => 'Термопаста та термопрокладки', 'type' => 'variable'],
                    ['name' => 'Флюс та припій',               'type' => 'variable'],
                    ['name' => 'Спирт та ізопропанол',         'type' => 'variable'],
                    ['name' => 'Скотч, стрічки, клей',         'type' => 'variable'],
                    ['name' => 'Пакувальні матеріали',         'type' => 'variable'],
                    ['name' => 'Канцтовари',                   'type' => 'variable'],
                ],
            ],
            [
                'name' => 'Інструменти та обладнання',
                'type' => 'one_time',
                'children' => [
                    ['name' => 'Паяльне обладнання',       'type' => 'one_time'],
                    ['name' => 'Вимірювальні прилади',     'type' => 'one_time'],
                    ['name' => 'Інструменти для розбирання', 'type' => 'one_time'],
                    ['name' => 'Оргтехніка',               'type' => 'one_time'],
                    ['name' => 'Меблі та обладнання',      'type' => 'one_time'],
                    ['name' => 'Ремонт обладнання',        'type' => 'variable'],
                ],
            ],

            // Маркетинг та реклама
            [
                'name' => 'Маркетинг та реклама',
                'type' => 'variable',
                'children' => [
                    ['name' => 'Google Ads',             'type' => 'variable'],
                    ['name' => 'Facebook/Instagram Ads', 'type' => 'variable'],
                    ['name' => 'SEO та просування сайту','type' => 'variable'],
                    ['name' => 'Поліграфія та банери',   'type' => 'one_time'],
                    ['name' => 'Інша реклама',           'type' => 'variable'],
                ],
            ],

            // Персонал
            [
                'name' => 'Персонал',
                'type' => 'fixed',
                'children' => [
                    ['name' => 'Заробітна плата',         'type' => 'fixed'],
                    ['name' => 'Податки з ФОП / ЄСВ',    'type' => 'fixed'],
                    ['name' => 'Навчання та тренінги',    'type' => 'one_time'],
                    ['name' => 'Спецодяг та засоби захисту', 'type' => 'variable'],
                ],
            ],

            // Доставка
            [
                'name' => 'Доставка та логістика',
                'type' => 'variable',
                'children' => [
                    ['name' => 'Нова Пошта',      'type' => 'variable'],
                    ['name' => 'Укрпошта',        'type' => 'variable'],
                    ['name' => 'Кур\'єрська доставка', 'type' => 'variable'],
                    ['name' => 'Паливо та транспорт',  'type' => 'variable'],
                ],
            ],

            // Банківські та фінансові витрати
            [
                'name' => 'Банківські витрати',
                'type' => 'variable',
                'children' => [
                    ['name' => 'Комісія банку',          'type' => 'variable'],
                    ['name' => 'Комісія терміналу',      'type' => 'variable'],
                    ['name' => 'Обслуговування рахунку', 'type' => 'fixed'],
                ],
            ],

            // Інше
            [
                'name' => 'Інші витрати',
                'type' => 'variable',
                'children' => [
                    ['name' => 'Представницькі витрати', 'type' => 'variable'],
                    ['name' => 'Господарські витрати',   'type' => 'variable'],
                    ['name' => 'Непередбачені витрати',  'type' => 'variable'],
                ],
            ],
        ];

        $totalCount = 0;

        foreach ($categories as $category) {
            $parentId = DB::table('expense_categories')->insertGetId([
                'parent_id'  => null,
                'name'       => $category['name'],
                'type'       => $category['type'],
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $totalCount++;

            foreach ($category['children'] ?? [] as $child) {
                DB::table('expense_categories')->insert([
                    'parent_id'  => $parentId,
                    'name'       => $child['name'],
                    'type'       => $child['type'],
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $totalCount++;
            }
        }

        $this->command->info("✅ Категорії витрат: {$totalCount} (з вкладеністю)");
    }
}
