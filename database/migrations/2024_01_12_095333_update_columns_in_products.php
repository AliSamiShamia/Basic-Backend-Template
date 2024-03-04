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
        Schema::table('products', function (Blueprint $table) {
            $table->text('sku')->nullable(false)->change();
            $table->text('description')->nullable()->change();
            $table->text('brief')->nullable()->change();
            $table->decimal('pre_price', 8, 2)->nullable()->change();// Previous price for the product
            $table->integer('stock')->default(10000)->change();
            $table->boolean('is_live')->default(false)->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
