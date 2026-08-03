<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('phone', 20)->nullable();
            $table->string('position')->nullable()->comment('Посада');
            $table->enum('salary_type', ['fixed', 'percent', 'combined'])->default('fixed');
            $table->decimal('base_salary', 10, 2)->default(0)->comment('Фіксована ставка');
            $table->decimal('bonus_percent', 5, 2)->default(0)->comment('Відсоток від виконаних робіт');
            $table->string('telegram_chat_id')->nullable();
            $table->string('avatar')->nullable();
            $table->string('color', 7)->nullable()->comment('Колір у календарі #RRGGBB');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
