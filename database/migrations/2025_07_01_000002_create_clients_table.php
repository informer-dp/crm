<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()
                  ->comment('Якщо клієнт зареєстрований на сайті');
            $table->enum('type', ['individual', 'legal'])->default('individual');
            $table->string('name')->comment('ПІБ або назва компанії');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->string('tax_code', 20)->nullable()->comment('ЄДРПОУ або ІПН');
            $table->string('contact_person')->nullable()->comment('Контактна особа для юросіб');
            $table->string('contact_phone', 20)->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_vip')->default(false);
            $table->boolean('is_blacklisted')->default(false);
            $table->text('blacklist_reason')->nullable();
            $table->timestamps();

            $table->index('phone');
            $table->index('name');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
