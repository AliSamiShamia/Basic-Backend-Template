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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('sku');
            $table->text('description');
            $table->text('brief');
            $table->decimal('price', 8, 2);// Main price for the product
            $table->decimal('pre_price', 8, 2);// Previous price for the product
            $table->float('weight')->nullable();//To know the product weight, it is not required
            $table->float('stock')->default(0);
            $table->boolean('is_trending')->default(false);
            $table->boolean('is_live');
            $table->boolean('is_featured')->default(false);
            $table->unsignedBigInteger('discount_id')->nullable();
            $table->foreign('discount_id')->references('id')->on('discounts')->cascadeOnDelete();
            // Additional columns as needed
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
