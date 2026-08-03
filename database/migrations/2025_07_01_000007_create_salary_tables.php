<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Налаштування нарахування зарплати (з історією змін)
        Schema::create('salary_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('base_type', ['fixed', 'none'])->default('fixed');
            $table->decimal('base_amount', 10, 2)->default(0)->comment('Фіксована ставка на місяць');
            $table->enum('bonus_type', ['none', 'percent_orders', 'percent_works'])->default('none');
            $table->decimal('bonus_percent', 5, 2)->default(0)->comment('Відсоток від виконаних робіт');
            $table->date('effective_from');
            $table->date('effective_to')->nullable()->comment('null = діє зараз');
            $table->timestamps();

            $table->index(['user_id', 'effective_to']);
        });

        // Постійні надбавки (незалежно від ролі)
        Schema::create('salary_allowances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name')->comment('Надбавка за суміщення, шкідливість...');
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['fixed', 'percent'])->default('fixed');
            $table->boolean('is_active')->default(true);
            $table->date('effective_from');
            $table->timestamps();
        });

        // Розрахункові періоди (зазвичай місяць)
        Schema::create('salary_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('period_from');
            $table->date('period_to');
            $table->decimal('base_earned', 10, 2)->default(0);
            $table->decimal('bonus_earned', 10, 2)->default(0);
            $table->decimal('allowances_total', 10, 2)->default(0);
            $table->decimal('bonuses_total', 10, 2)->default(0)->comment('Разові премії');
            $table->decimal('deductions_total', 10, 2)->default(0)->comment('Утримання');
            $table->decimal('total_accrued', 10, 2)->default(0)->comment('Всього нараховано');
            $table->decimal('total_paid', 10, 2)->default(0);
            $table->enum('status', ['draft', 'approved', 'paid'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'period_from', 'period_to']);
            $table->index(['user_id', 'status']);
        });

        // Разові премії та утримання
        Schema::create('salary_bonuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('period_id')->nullable()->constrained('salary_periods')->nullOnDelete()
                  ->comment('null до прив\'язки до розрахункового періоду');
            $table->string('name')->comment('Премія за місяць, Штраф...');
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['bonus', 'deduction'])->default('bonus');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Виплати зарплати
        Schema::create('salary_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('salary_periods')->restrictOnDelete();
            $table->foreignId('account_id')->constrained()->restrictOnDelete();
            $table->foreignId('transaction_id')->constrained()->restrictOnDelete();
            $table->decimal('amount', 10, 2);
            $table->foreignId('user_id')->constrained()->restrictOnDelete()
                  ->comment('Кому виплачено');
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete()
                  ->comment('Хто виплатив');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_payments');
        Schema::dropIfExists('salary_bonuses');
        Schema::dropIfExists('salary_periods');
        Schema::dropIfExists('salary_allowances');
        Schema::dropIfExists('salary_settings');
    }
};
