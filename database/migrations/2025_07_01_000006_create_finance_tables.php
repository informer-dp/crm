<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Рахунки (каси)
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Готівкова каса, Термінал, Розрахунковий рахунок');
            $table->enum('type', ['cash', 'terminal', 'bank'])->default('cash');
            $table->decimal('balance', 12, 2)->default(0);
            $table->string('currency', 3)->default('UAH');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Транзакції (фінансовий журнал)
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->restrictOnDelete();
            $table->enum('type', ['income', 'expense', 'transfer']);
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_after', 12, 2)->comment('Баланс рахунку після операції');
            $table->nullableMorphs('reference')->comment('order, purchase, expense, salary, transfer');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()
                  ->comment('Хто провів операцію');
            $table->string('description')->nullable();
            $table->date('transaction_date');
            $table->timestamps();

            $table->index(['account_id', 'transaction_date']);
            $table->index('transaction_date');
        });

        // Перекази між рахунками
        Schema::create('account_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_account_id')->constrained('accounts')->restrictOnDelete();
            $table->foreignId('to_account_id')->constrained('accounts')->restrictOnDelete();
            $table->decimal('amount', 12, 2);
            $table->foreignId('transaction_from_id')->constrained('transactions')->restrictOnDelete();
            $table->foreignId('transaction_to_id')->constrained('transactions')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Оплати по заявках
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->restrictOnDelete();
            $table->foreignId('account_id')->constrained()->restrictOnDelete();
            $table->foreignId('transaction_id')->constrained()->restrictOnDelete();
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'terminal', 'transfer'])->default('cash');
            $table->enum('type', ['prepayment', 'final', 'refund'])->default('final');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['order_id', 'type']);
        });

        // Категорії витрат
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('expense_categories')->nullOnDelete();
            $table->string('name')->comment('Оренда, Комунальні, Реклама...');
            $table->enum('type', ['fixed', 'variable', 'one_time'])->default('variable');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Витрати
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('expense_categories')->restrictOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete()
                  ->comment('null якщо ще не оплачено (борг)');
            $table->foreignId('transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->boolean('is_paid')->default(false);
            $table->date('expense_date');
            $table->date('due_date')->nullable()->comment('Термін оплати якщо не одразу');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_paid', 'due_date']);
            $table->index('expense_date');
        });

        // Борги перед постачальниками
        Schema::create('supplier_debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->foreignId('purchase_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount_total', 10, 2);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('amount_remaining', 10, 2);
            $table->enum('status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->date('due_date')->nullable();
            $table->timestamps();

            $table->index(['supplier_id', 'status']);
        });

        // Погашення боргів перед постачальниками
        Schema::create('supplier_debt_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debt_id')->constrained('supplier_debts')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->restrictOnDelete();
            $table->foreignId('transaction_id')->constrained()->restrictOnDelete();
            $table->decimal('amount', 10, 2);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_debt_payments');
        Schema::dropIfExists('supplier_debts');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('order_payments');
        Schema::dropIfExists('account_transfers');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('accounts');
    }
};
