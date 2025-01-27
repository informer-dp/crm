<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('work_types', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique(); // Назва типу роботи
        $table->foreignId('device_id')   // Поле для пристрою
              ->constrained('devices')  // Зовнішній ключ до таблиці devices
              ->cascadeOnDelete();      // Авто-видалення при видаленні пристрою
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_types');
    }
};
