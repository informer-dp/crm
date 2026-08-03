<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Орендарі (для продажу CRM іншим СЦ)
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Назва СЦ');
            $table->string('slug')->unique()->comment('Унікальний ідентифікатор');
            $table->enum('plan', ['basic', 'pro', 'enterprise'])->default('basic');
            $table->boolean('is_active')->default(true);
            $table->date('trial_ends_at')->nullable();
            $table->date('plan_expires_at')->nullable();
            $table->timestamps();
        });

        // Налаштування системи (ключ-значення)
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->text('value')->nullable();
            $table->enum('type', ['string', 'integer', 'boolean', 'json'])->default('string');
            $table->string('group')->default('general')
                  ->comment('general, receipt, warranty, notifications, salary');
            $table->timestamps();

            $table->unique('key');
            $table->index('group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('tenants');
    }
};
