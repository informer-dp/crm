<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CounterpartyGroup;

class CounterpartyGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CounterpartyGroup::insert([
    ['name'=>'Клієнти','discount'=>0],
    ['name'=>'Постачальники','discount'=>0],
    ['name'=>'Підрядники','discount'=>0],
    ['name'=>'Співробітники','discount'=>0],
]);

    }
}
