<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Головна таблиця заявок
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number', 20)->unique()->comment('SC-2024-00142');
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->foreignId('device_id')->constrained()->restrictOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete()
                  ->comment('Хто прийняв заявку');
            $table->enum('type', ['repair', 'express', 'diagnostic', 'maintenance'])
                  ->default('repair');
            $table->enum('priority', ['normal', 'urgent'])->default('normal');
            $table->enum('status', [
                'new', 'diagnosed', 'approved', 'in_progress',
                'waiting_parts', 'ready', 'issued', 'cancelled'
            ])->default('new');
            $table->text('malfunction')->comment('Скарга клієнта його словами');
            $table->text('diagnosis')->nullable()->comment('Технічний висновок інженера');
            $table->text('notes')->nullable()->comment('Внутрішні нотатки');
            $table->string('check_code', 6)->comment('6-значний код для перевірки статусу без входу');
            $table->decimal('prepayment', 10, 2)->default(0);
            $table->date('estimated_date')->nullable()->comment('Очікувана дата готовності');
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('priority');
            $table->index('type');
            $table->index('created_at');
        });

        // Виконавці заявки (інженери)
        Schema::create('order_engineers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->boolean('is_primary')->default(false)->comment('Головний відповідальний');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['order_id', 'user_id']);
        });

        // Журнал зміни статусів
        Schema::create('order_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()
                  ->comment('Хто змінив статус');
            $table->string('status_from', 20)->nullable();
            $table->string('status_to', 20);
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->index('order_id');
        });

        // Кошторис (один на заявку)
        Schema::create('estimates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('works_total', 10, 2)->default(0);
            $table->decimal('parts_total', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->enum('discount_type', ['fixed', 'percent'])->default('fixed');
            $table->decimal('total', 10, 2)->default(0);
            $table->boolean('is_approved')->default(false)->comment('Погоджено клієнтом');
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Рядки кошторису — роботи
        Schema::create('estimate_works', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estimate_id')->constrained()->cascadeOnDelete();
            $table->string('name')->comment('Назва роботи');
            $table->enum('work_type', ['own', 'subcontract'])->default('own');
            $table->foreignId('engineer_id')->nullable()->constrained('users')->nullOnDelete()
                  ->comment('Виконавець, null якщо підрядна');
            $table->foreignId('subcontractor_id')->nullable()->constrained('suppliers')->nullOnDelete()
                  ->comment('Підрядний СЦ, null якщо власна');
            $table->decimal('price', 10, 2)->default(0)->comment('Ціна для клієнта');
            $table->decimal('cost', 10, 2)->default(0)->comment('Собівартість');
            $table->integer('quantity')->default(1);
            $table->decimal('total', 10, 2)->default(0);
            $table->boolean('is_warranty')->default(false);
            $table->enum('status', ['pending', 'in_progress', 'done', 'sent_to_sub', 'returned_from_sub'])
                  ->default('pending');
            $table->timestamps();
        });

        // Рядки кошторису — запчастини
        Schema::create('estimate_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estimate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('part_id')->nullable()->constrained()->nullOnDelete()
                  ->comment('null якщо запчастина не зі складу');
            $table->string('name')->comment('Назва (заповнюється якщо не зі складу)');
            $table->decimal('price', 10, 2)->default(0)->comment('Роздрібна ціна для клієнта');
            $table->decimal('cost', 10, 2)->default(0)->comment('Наша закупівельна ціна');
            $table->integer('quantity')->default(1);
            $table->decimal('total', 10, 2)->default(0);
            $table->boolean('is_own_part')->default(false)->comment('Запчастина клієнта');
            $table->timestamps();
        });

        // Завдання для інженерів
        Schema::create('order_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'done'])->default('pending');
            $table->enum('priority', ['normal', 'high'])->default('normal');
            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['assigned_to', 'status']);
        });

        // Коментарі до заявки
        Schema::create('order_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('body');
            $table->boolean('is_internal')->default(true)
                  ->comment('false = видно клієнту на сайті');
            $table->timestamps();

            $table->index(['order_id', 'is_internal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_comments');
        Schema::dropIfExists('order_tasks');
        Schema::dropIfExists('estimate_parts');
        Schema::dropIfExists('estimate_works');
        Schema::dropIfExists('estimates');
        Schema::dropIfExists('order_status_history');
        Schema::dropIfExists('order_engineers');
        Schema::dropIfExists('orders');
    }
};
