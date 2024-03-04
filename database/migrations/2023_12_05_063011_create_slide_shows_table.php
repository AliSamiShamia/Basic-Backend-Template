<?php

use Carbon\Carbon;
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
        Schema::create('slide_shows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('slidable_id')->nullable();
            $table->string('slidable_type')->nullable();
            $table->enum('type', ['none','category', 'product'])->nullable();
            $table->timestamp('expired_at')->default(Carbon::now()->addDays(30));
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
        Schema::dropIfExists('slide_shows');
    }
};
