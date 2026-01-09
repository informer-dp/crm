<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // 1️⃣ Спочатку дроп старого foreign key (якщо існує)
            DB::statement('ALTER TABLE orders DROP FOREIGN KEY orders_counterparty_id_foreign');

            // 2️⃣ Робимо поле nullable
            $table->unsignedBigInteger('counterparty_id')->nullable()->change();

            // 3️⃣ Створюємо FK заново з SET NULL
            $table->foreign('counterparty_id')
                ->references('id')
                ->on('counterparties')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            DB::statement('ALTER TABLE orders DROP FOREIGN KEY orders_counterparty_id_foreign');

            $table->unsignedBigInteger('counterparty_id')->nullable(false)->change();

            $table->foreign('counterparty_id')
                ->references('id')
                ->on('counterparties')
                ->cascadeOnDelete();
        });
    }
};
