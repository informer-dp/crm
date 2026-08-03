<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Постачальники
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['local', 'online_shop', 'marketplace', 'subcontractor', 'other'])
                  ->default('local');
            $table->string('contact_person')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Категорії запчастин (з вкладеністю)
        Schema::create('part_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('part_categories')->nullOnDelete();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Запчастини (довідник)
        Schema::create('parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('part_categories')->nullOnDelete();
            $table->string('name');
            $table->string('sku')->nullable()->unique()->comment('Внутрішній артикул');
            $table->string('unit', 10)->default('шт')->comment('шт, м, компл');
            $table->decimal('retail_price', 10, 2)->default(0)->comment('Роздрібна ціна за замовч.');
            $table->integer('stock_qty')->default(0)->comment('Поточний залишок');
            $table->integer('min_stock_qty')->default(0)->comment('Мінімальний залишок для сповіщення');
            $table->boolean('track_stock')->default(true)->comment('Вести облік залишку');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('name');
            $table->index('stock_qty');
        });

        // Ціни постачальників для запчастин
        Schema::create('part_supplier_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->decimal('unit_cost', 10, 2)->default(0)->comment('Остання закупівельна ціна');
            $table->string('supplier_sku')->nullable()->comment('Артикул постачальника');
            $table->boolean('is_preferred')->default(false)->comment('Основний постачальник');
            $table->timestamp('updated_at')->nullable();

            $table->unique(['part_id', 'supplier_id']);
        });

        // Донорські пристрої
        Schema::create('donor_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('brand_id')->constrained()->restrictOnDelete();
            $table->foreignId('model_id')->constrained()->restrictOnDelete();
            $table->text('condition')->nullable()->comment('Опис стану донора');
            $table->decimal('purchase_price', 10, 2)->default(0)->comment('Скільки заплатили за донора');
            $table->decimal('allocated_cost', 10, 2)->default(0)->comment('Вже списано на запчастини');
            $table->timestamps();
        });

        // Закупівлі
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete()
                  ->comment('null якщо закупівля на склад, не під замовлення');
            $table->enum('status', ['pending', 'ordered', 'received', 'partially_returned', 'returned'])
                  ->default('pending');
            $table->string('source_url')->nullable()->comment('Посилання на товар (AliExpress, Rozetka)');
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
        });

        // Позиції закупівлі
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->foreignId('part_id')->nullable()->constrained()->nullOnDelete()
                  ->comment('null якщо позиція не прив\'язана до довідника');
            $table->string('name')->comment('Назва (якщо без part_id або для уточнення)');
            $table->integer('qty_ordered')->default(1);
            $table->integer('qty_received')->default(0);
            $table->integer('qty_returned')->default(0);
            $table->decimal('unit_cost', 10, 2)->default(0)->comment('Наша закупівельна ціна');
            $table->decimal('unit_price', 10, 2)->default(0)->comment('Роздрібна ціна для кошторису');
            $table->foreignId('donor_device_id')->nullable()->constrained()->nullOnDelete()
                  ->comment('Якщо запчастина знята з донора');
            $table->timestamps();
        });

        // Рухи запчастин на складі (серце складу)
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_id')->constrained()->restrictOnDelete();
            $table->enum('type', [
                'purchase',           // надійшло від постачальника
                'return_to_supplier', // повернули постачальнику
                'order_use',          // списано на заявку
                'order_return',       // повернули з заявки на склад
                'donor_extract',      // знято з донорського пристрою
                'write_off',          // списано (брак, втрата)
                'adjustment'          // ручне коригування
            ]);
            $table->integer('qty')->comment('+ надходження, - витрата');
            $table->decimal('unit_cost', 10, 2)->default(0)->comment('Собівартість одиниці');
            $table->decimal('unit_price', 10, 2)->default(0)->comment('Роздрібна ціна');
            $table->nullableMorphs('reference')->comment('order, purchase, donor_device');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()
                  ->comment('Хто провів операцію');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['part_id', 'type']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('donor_devices');
        Schema::dropIfExists('part_supplier_prices');
        Schema::dropIfExists('parts');
        Schema::dropIfExists('part_categories');
        Schema::dropIfExists('suppliers');
    }
};
