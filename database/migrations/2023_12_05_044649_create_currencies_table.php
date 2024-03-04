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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Currency code (e.g., USD, EUR)
            $table->string('name'); // Currency name (e.g., US Dollar, Euro)
            $table->string('symbol'); // Currency symbol (e.g., $, €)
            $table->decimal('rate', 10, 4); // Exchange rate (e.g., 1 USD = X EUR)
            $table->boolean('is_main')->default(false);
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
        Schema::dropIfExists('currencies');
    }
};
