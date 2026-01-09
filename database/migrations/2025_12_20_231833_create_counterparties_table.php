<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('counterparties', function (Blueprint $table) {
        $table->id();

        $table->foreignId('contact_id')
              ->constrained('contacts')
              ->cascadeOnDelete();

        $table->enum('type', ['individual', 'company'])
              ->default('individual');

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('counterparties');
}

};
