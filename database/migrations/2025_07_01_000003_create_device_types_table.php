<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Типи пристроїв: Смартфон, Ноутбук, Планшет...
        Schema::create('device_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Бренди: Apple, Samsung, Lenovo...
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('device_type_id')->nullable()->constrained()->nullOnDelete()
                  ->comment('null = бренд для всіх типів пристроїв');
            $table->string('logo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['name', 'device_type_id']);
        });

        // Моделі: iPhone 15 Pro, ThinkPad X1...
        Schema::create('models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('device_type_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['brand_id', 'name']);
            $table->index('device_type_id');
        });

        // Конкретні пристрої клієнтів
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('brand_id')->constrained()->restrictOnDelete();
            $table->foreignId('model_id')->constrained()->restrictOnDelete();
            $table->string('serial_number')->nullable();
            $table->string('imei', 20)->nullable()->comment('Для смартфонів');
            $table->string('color')->nullable();
            $table->text('appearance')->nullable()->comment('Зовнішній вигляд при прийомі');
            $table->unsignedSmallInteger('production_year')->nullable();
            $table->timestamps();

            $table->index('serial_number');
            $table->index('imei');
        });

        // Фото пристроїв
        Schema::create('device_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->enum('type', ['front', 'back', 'damage', 'other'])->default('other');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_photos');
        Schema::dropIfExists('devices');
        Schema::dropIfExists('models');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('device_types');
    }
};
