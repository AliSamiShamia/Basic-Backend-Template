<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_item_params', function (Blueprint $table) {
            $table->id();
            $table->string('value');
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->on('orders')->references('id')->cascadeOnDelete();
            $table->unsignedBigInteger('order_item_id');
            $table->foreign('order_item_id')->on('order_items')->references('id')->cascadeOnDelete();
            $table->unsignedBigInteger('param_id');
            $table->foreign('param_id')->on('product_parmas')->references('id')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_item_params');
    }
};
