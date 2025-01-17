<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_order_estimations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderEstimationsTable extends Migration
{
    public function up()
    {
        Schema::create('order_estimations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id'); // Посилання на замовлення
            $table->string('item_name'); // Назва послуги або запчастини
            $table->integer('quantity')->default(1); // Кількість
            $table->decimal('price_per_unit', 10, 2); // Ціна за одиницю
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_estimations');
    }
}
