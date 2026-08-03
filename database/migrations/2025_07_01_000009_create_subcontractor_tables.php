<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Замовлення підрядним СЦ
        Schema::create('subcontractor_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete()
                  ->comment('Підрядний СЦ');
            $table->foreignId('order_id')->constrained()->restrictOnDelete()
                  ->comment('Наша заявка');
            $table->enum('status', ['pending', 'sent', 'in_progress', 'ready', 'received'])
                  ->default('pending');
            $table->decimal('cost', 10, 2)->default(0)->comment('Сума підряду');
            $table->date('sent_at')->nullable();
            $table->date('expected_at')->nullable();
            $table->date('received_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Роботи що входять у підрядне замовлення
        Schema::create('subcontractor_order_works', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subcontractor_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('estimate_work_id')->constrained('estimate_works')->cascadeOnDelete();

            $table->unique(['subcontractor_order_id', 'estimate_work_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subcontractor_order_works');
        Schema::dropIfExists('subcontractor_orders');
    }
};
