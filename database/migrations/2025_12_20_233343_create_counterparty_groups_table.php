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
    Schema::create('counterparty_groups', function (Blueprint $table) {
        $table->id();

        $table->string('name');
        $table->unsignedTinyInteger('discount')->default(0);
        $table->text('notes')->nullable();

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('counterparty_groups');
}

};
