<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');
            $table->foreignId('brand_id')->constrained('brands')->onDelete('cascade'); // <-- додаємо brand_id
            $table->string('device_model');
            $table->string('serial_number')->nullable();
            $table->text('problem_description');
            $table->string('status')->default('Діагностика');
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
