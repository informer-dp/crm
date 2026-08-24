<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionBasesSeeder extends Seeder
{
    public function run(): void
    {
        $bases = [

            // ── НАДХОДЖЕННЯ ───────────────────────────────────────────
            // Власні для СЦ
            ['group' => 'Надходження від клієнтів', 'name' => 'Оплата ремонту',                    'flow_type' => 'income', 'sort_order' => 1],
            ['group' => 'Надходження від клієнтів', 'name' => 'Передоплата за ремонт',             'flow_type' => 'income', 'sort_order' => 2],
            ['group' => 'Надходження від клієнтів', 'name' => 'Продаж аксесуарів',                 'flow_type' => 'income', 'sort_order' => 3],
            ['group' => 'Надходження від клієнтів', 'name' => 'Продаж запчастин',                  'flow_type' => 'income', 'sort_order' => 4],

            // З dilovod
            ['group' => 'Роздрібна виручка',        'name' => 'Роздрібна виручка',                 'flow_type' => 'income', 'sort_order' => 10],
            ['group' => 'Роздрібна виручка',        'name' => 'Повернення роздрібної виручки',     'flow_type' => 'expense','sort_order' => 11],
            ['group' => 'Розрахунки з покупцями',   'name' => 'Надходження від реалізації послуг', 'flow_type' => 'income', 'sort_order' => 12],
            ['group' => 'Розрахунки з покупцями',   'name' => 'Надходження від реалізації товарів','flow_type' => 'income', 'sort_order' => 13],
            ['group' => 'Розрахунки з покупцями',   'name' => 'Надходження від реалізації основних засобів', 'flow_type' => 'income', 'sort_order' => 14],
            ['group' => 'Розрахунки з покупцями',   'name' => 'Надходження від оренди',            'flow_type' => 'income', 'sort_order' => 15],
            ['group' => 'Розрахунки з покупцями',   'name' => 'Повернення коштів покупцям',        'flow_type' => 'expense','sort_order' => 16],
            ['group' => 'Інші доходи',              'name' => 'Інші надходження',                  'flow_type' => 'income', 'sort_order' => 17],
            ['group' => 'Інші доходи',              'name' => 'Дивіденди отримані',                'flow_type' => 'income', 'sort_order' => 18],
            ['group' => 'Інші доходи',              'name' => 'Відсотки отримані',                 'flow_type' => 'income', 'sort_order' => 19],
            ['group' => "Нез'ясовані",              'name' => "Нез'ясовані доходи",                'flow_type' => 'income', 'sort_order' => 20],

            // Кредити і позики
            ['group' => 'Отримані кредити і позики','name' => 'Кредити отримані',                  'flow_type' => 'income', 'sort_order' => 21],
            ['group' => 'Отримані кредити і позики','name' => 'Позики отримані',                   'flow_type' => 'income', 'sort_order' => 22],
            ['group' => 'Отримані кредити і позики','name' => 'Повернення кредитів',               'flow_type' => 'expense','sort_order' => 23],
            ['group' => 'Отримані кредити і позики','name' => 'Повернення позик отриманих',        'flow_type' => 'expense','sort_order' => 24],
            ['group' => 'Отримані кредити і позики','name' => 'Позики видані',                     'flow_type' => 'expense','sort_order' => 25],
            ['group' => 'Отримані кредити і позики','name' => 'Повернення позик виданих',          'flow_type' => 'income', 'sort_order' => 26],

            // Власники
            ['group' => 'Розрахунки з власниками', 'name' => 'Надходження від засновників',        'flow_type' => 'income', 'sort_order' => 27],
            ['group' => 'Розрахунки з власниками', 'name' => 'Дивіденди виплачені',                'flow_type' => 'expense','sort_order' => 28],
            ['group' => 'Власні кошти',            'name' => 'Внесення власних коштів',            'flow_type' => 'income', 'sort_order' => 29],
            ['group' => 'Власні кошти',            'name' => 'Вилучення власних коштів',           'flow_type' => 'expense','sort_order' => 30],

            // ── ВИТРАТИ ───────────────────────────────────────────────
            // Власні для СЦ
            ['group' => 'Витрати по заявках',      'name' => 'Закупівля запчастини під заявку',    'flow_type' => 'expense','sort_order' => 40],
            ['group' => 'Витрати по заявках',      'name' => 'Доставка запчастини',                'flow_type' => 'expense','sort_order' => 41],
            ['group' => 'Витрати по заявках',      'name' => 'Підряд (інший СЦ)',                  'flow_type' => 'expense','sort_order' => 42],

            // Постачальники
            ['group' => 'Розрахунки з постачальниками', 'name' => 'Оплата товарів і матеріалів',  'flow_type' => 'expense','sort_order' => 50],
            ['group' => 'Розрахунки з постачальниками', 'name' => 'Оплата товарів та послуг (без документів)', 'flow_type' => 'expense','sort_order' => 51],
            ['group' => 'Розрахунки з постачальниками', 'name' => 'Оплата робіт, послуг',         'flow_type' => 'expense','sort_order' => 52],
            ['group' => 'Розрахунки з постачальниками', 'name' => 'Оплата машин, обладнання',     'flow_type' => 'expense','sort_order' => 53],
            ['group' => 'Розрахунки з постачальниками', 'name' => 'Оплата оренди',                'flow_type' => 'expense','sort_order' => 54],
            ['group' => 'Розрахунки з постачальниками', 'name' => 'Оплата транспортних послуг',   'flow_type' => 'expense','sort_order' => 55],
            ['group' => 'Розрахунки з постачальниками', 'name' => 'Оплата юридичних послуг',      'flow_type' => 'expense','sort_order' => 56],
            ['group' => 'Розрахунки з постачальниками', 'name' => 'Оплата інформаційно-консультаційних послуг', 'flow_type' => 'expense','sort_order' => 57],
            ['group' => 'Розрахунки з постачальниками', 'name' => 'Оплата рекламних, маркетингових послуг', 'flow_type' => 'expense','sort_order' => 58],
            ['group' => 'Розрахунки з постачальниками', "name" => "Оплата за послуги зв'язку та інтернету", 'flow_type' => 'expense','sort_order' => 59],
            ['group' => 'Розрахунки з постачальниками', 'name' => 'Оплата капітального ремонту машин та обладнання', 'flow_type' => 'expense','sort_order' => 60],
            ['group' => 'Розрахунки з постачальниками', 'name' => 'Оплата капітального ремонту приміщень, будівель, споруд', 'flow_type' => 'expense','sort_order' => 61],
            ['group' => 'Розрахунки з постачальниками', 'name' => 'Повернення коштів постачальниками', 'flow_type' => 'income','sort_order' => 62],

            // Зарплата
            ['group' => 'Виплата заробітної плати', 'name' => 'Заробітна плата працівників',      'flow_type' => 'expense','sort_order' => 70],
            ['group' => 'Виплата заробітної плати', 'name' => 'Аванс по заробітній платі працівників', 'flow_type' => 'expense','sort_order' => 71],

            // Податки
            ['group' => 'Перерахування податків',  'name' => 'Єдиний податок',                    'flow_type' => 'expense','sort_order' => 80],
            ['group' => 'Перерахування податків',  'name' => 'Єдиний соціальний внесок',          'flow_type' => 'expense','sort_order' => 81],

            // Загальні витрати
            ['group' => 'Витрати',                 'name' => 'Комунальні витрати',                 'flow_type' => 'expense','sort_order' => 90],
            ['group' => 'Витрати',                 'name' => 'Витрати на рекламу і маркетинг',    'flow_type' => 'expense','sort_order' => 91],
            ['group' => 'Витрати',                 'name' => 'Транспортні витрати',               'flow_type' => 'expense','sort_order' => 92],
            ['group' => 'Витрати',                 'name' => 'Юридичні витрати',                  'flow_type' => 'expense','sort_order' => 93],
            ['group' => 'Витрати',                 'name' => 'Інформаційно-консультаційні витрати','flow_type' => 'expense','sort_order' => 94],
            ['group' => 'Витрати',                 "name" => "Витрати на зв'язок та інтернет",    'flow_type' => 'expense','sort_order' => 95],
            ['group' => 'Витрати',                 'name' => 'Витрати на поточний ремонт машин та обладнання', 'flow_type' => 'expense','sort_order' => 96],
            ['group' => 'Витрати',                 'name' => 'Витрати на поточний ремонт приміщень, будівель, споруд', 'flow_type' => 'expense','sort_order' => 97],
            ['group' => 'Витрати',                 'name' => 'Розрахунково-касове обслуговування', 'flow_type' => 'expense','sort_order' => 98],
            ['group' => 'Витрати',                 'name' => 'Відсотки за кредитами і позиками',  'flow_type' => 'expense','sort_order' => 99],
            ['group' => 'Витрати',                 'name' => 'Штрафи і пені',                     'flow_type' => 'expense','sort_order' => 100],
            ['group' => 'Витрати',                 'name' => 'Представницькі витрати',            'flow_type' => 'expense','sort_order' => 101],
            ['group' => 'Витрати',                 'name' => 'Марні витрати',                     'flow_type' => 'expense','sort_order' => 102],
            ['group' => "Нез'ясовані",             'name' => "Нез'ясовані витрати",               'flow_type' => 'expense','sort_order' => 103],
            ['group' => 'Витрати',                 'name' => 'Інші витрати',                      'flow_type' => 'expense','sort_order' => 104],

            // Підзвітні особи
            ['group' => 'Розрахунки з підзвітними особами', 'name' => 'Видача грошей під звіт',  'flow_type' => 'expense','sort_order' => 110],
            ['group' => 'Розрахунки з підзвітними особами', 'name' => 'Повернення грошей виданих під звіт', 'flow_type' => 'income','sort_order' => 111],

            // Інші
            ['group' => 'Інші взаєморозрахунки',  'name' => 'Виплата аліментів',                 'flow_type' => 'expense','sort_order' => 120],

            // ── ВНУТРІШНІ ─────────────────────────────────────────────
            ['group' => 'Внутрішні платежі',       'name' => 'Переказ між рахунками',             'flow_type' => 'internal','sort_order' => 130],
            ['group' => 'Внутрішні платежі',       'name' => 'Отримання готівки в банку',         'flow_type' => 'internal','sort_order' => 131],
            ['group' => 'Внутрішні платежі',       'name' => 'Здача готівки в банк',              'flow_type' => 'internal','sort_order' => 132],
            ['group' => 'Внутрішні платежі',       'name' => 'Поповнення карткового рахунку',     'flow_type' => 'internal','sort_order' => 133],
            ['group' => 'Внутрішні платежі',       'name' => 'Передача з каси в касу',            'flow_type' => 'internal','sort_order' => 134],
            ['group' => 'Внутрішні платежі',       'name' => 'Обмін, купівля, продаж валюти',     'flow_type' => 'internal','sort_order' => 135],
        ];

        foreach ($bases as $base) {
            DB::table('transaction_bases')->insertOrIgnore([
                'group'      => $base['group'],
                'name'       => $base['name'],
                'flow_type'  => $base['flow_type'],
                'is_active'  => true,
                'sort_order' => $base['sort_order'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Статті руху коштів: ' . count($bases));

        // ── Переносимо дані з expenses в transactions ─────────────────
        $expenses = DB::table('expenses')->get();
        $migrated = 0;

        foreach ($expenses as $expense) {
            // Знаходимо transaction_id
            if ($expense->transaction_id) {
                DB::table('transactions')
                    ->where('id', $expense->transaction_id)
                    ->update([
                        'order_id'    => $expense->order_id,
                        'supplier_id' => $expense->supplier_id,
                        'payment_method' => 'cash',
                    ]);
                $migrated++;
            }
        }
        $this->command->info("✅ Перенесено даних з expenses: {$migrated}");

        // ── Переносимо дані з order_payments в transactions ───────────
        $payments = DB::table('order_payments')->get();
        $migratedPayments = 0;

        foreach ($payments as $payment) {
            if ($payment->transaction_id) {
                // Знаходимо client_id через order
                $order = DB::table('orders')->where('id', $payment->order_id)->first();
                $clientId = $order?->client_id;

                // Знаходимо basis_id для оплати ремонту
                $basisId = DB::table('transaction_bases')
                    ->where('name', 'Оплата ремонту')
                    ->value('id');

                DB::table('transactions')
                    ->where('id', $payment->transaction_id)
                    ->update([
                        'order_id'       => $payment->order_id,
                        'client_id'      => $clientId,
                        'basis_id'       => $basisId,
                        'payment_method' => $payment->payment_method ?? 'cash',
                    ]);
                $migratedPayments++;
            }
        }
        $this->command->info("✅ Перенесено даних з order_payments: {$migratedPayments}");
    }
}