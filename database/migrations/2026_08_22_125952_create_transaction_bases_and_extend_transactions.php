<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Таблиця статей руху коштів
        Schema::create('transaction_bases', function (Blueprint $table) {
            $table->id();
            $table->string('group')->nullable();
            $table->string('name');
            $table->enum('flow_type', ['income', 'expense', 'both', 'internal'])
                  ->default('both');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Додаємо нові поля в transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('basis_id')
                  ->nullable()->after('user_id')
                  ->constrained('transaction_bases')->nullOnDelete();

            $table->foreignId('order_id')
                  ->nullable()->after('basis_id')
                  ->constrained()->nullOnDelete();

            $table->foreignId('supplier_id')
                  ->nullable()->after('order_id')
                  ->constrained()->nullOnDelete();

            $table->foreignId('client_id')
                  ->nullable()->after('supplier_id')
                  ->constrained()->nullOnDelete();

            $table->string('counterparty_name')
                  ->nullable()->after('client_id');

            $table->enum('payment_method', ['cash', 'terminal', 'transfer'])
                  ->default('cash')->after('counterparty_name');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['basis_id']);
            $table->dropForeign(['order_id']);
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['client_id']);
            $table->dropColumn([
                'basis_id', 'order_id', 'supplier_id',
                'client_id', 'counterparty_name', 'payment_method'
            ]);
        });
        Schema::dropIfExists('transaction_bases');
    }
};