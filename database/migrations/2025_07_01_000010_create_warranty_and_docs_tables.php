<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Квитанції (прийом, передоплата, фінальна)
        Schema::create('order_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->restrictOnDelete();
            $table->enum('type', ['acceptance', 'prepayment', 'final']);
            $table->string('number', 30)->unique()->comment('RC-2024-00142-1');
            $table->decimal('amount', 10, 2)->default(0);
            $table->enum('payment_method', ['cash', 'terminal', 'transfer'])->nullable();
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('printed_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'type']);
        });

        // Правила гарантії (довідник)
        Schema::create('warranty_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Ремонт плати, Заміна екрану...');
            $table->unsignedSmallInteger('duration_days')->default(30);
            $table->text('conditions')->nullable()->comment('Умови гарантії');
            $table->text('exclusions')->nullable()->comment('Що не покривається');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Прив'язка правил гарантії до рядків кошторису
        Schema::create('estimate_works_warranty', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estimate_work_id')->constrained('estimate_works')->cascadeOnDelete();
            $table->foreignId('warranty_rule_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('duration_days')->nullable()
                  ->comment('Override якщо відрізняється від правила');

            $table->unique('estimate_work_id');
        });

        // Гарантійні талони
        Schema::create('warranties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->restrictOnDelete();
            $table->string('number', 30)->unique()->comment('WR-2024-00142');
            $table->date('issued_at');
            $table->date('expires_at');
            $table->enum('status', ['active', 'expired', 'voided', 'claimed'])->default('active');
            $table->text('covered_works')->comment('Snapshot робіт на момент видачі');
            $table->text('terms')->comment('Snapshot умов гарантії');
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('printed_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        // Гарантійні звернення
        Schema::create('warranty_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warranty_id')->constrained()->restrictOnDelete();
            $table->foreignId('new_order_id')->nullable()->constrained('orders')->nullOnDelete()
                  ->comment('Нова гарантійна заявка');
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->date('claim_date');
            $table->text('description')->comment('Опис проблеми від клієнта');
            $table->enum('status', ['open', 'approved', 'rejected', 'resolved'])->default('open');
            $table->text('resolution')->nullable()->comment('Рішення по претензії');
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['warranty_id', 'status']);
        });

        // Шаблони документів (квитанція, гарантія, кошторис)
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['receipt', 'warranty', 'estimate', 'acceptance']);
            $table->string('name');
            $table->text('html_template')->comment('Blade/HTML шаблон для друку');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_templates');
        Schema::dropIfExists('warranty_claims');
        Schema::dropIfExists('warranties');
        Schema::dropIfExists('estimate_works_warranty');
        Schema::dropIfExists('warranty_rules');
        Schema::dropIfExists('order_receipts');
    }
};
