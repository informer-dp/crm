<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrderStatus;

$statuses = [
    ['Прийняте', 'accepted'],
    ['Поставлене в роботу', 'in_work'],
    ['Діагностика', 'diagnostic'],
    ['Узгодження з клієнтом', 'approval'],
    ['Очікування деталей', 'waiting_parts'],
    ['Ремонт', 'repair'],
    ['Готове', 'ready'],
    ['Видане', 'finished'],
    ['Скасоване', 'canceled'],
    ['Архівне', 'archived'],
];

foreach ($statuses as $i => $s) {
    OrderStatus::create([
        'name' => $s[0],
        'code' => $s[1],
        'sort_order' => $i
    ]);
}


class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
    }
}
