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
    Schema::create('counterparty_group_map', function (Blueprint $table) {
        $table->id();

        $table->foreignId('counterparty_id')
              ->constrained('counterparties')
              ->cascadeOnDelete();

        $table->foreignId('group_id')
              ->constrained('counterparty_groups')
              ->cascadeOnDelete();

        $table->unique(['counterparty_id', 'group_id']);
    });
}

public function down(): void
{
    Schema::dropIfExists('counterparty_group_map');
}


};
