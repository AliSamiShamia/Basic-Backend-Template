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
        //generate otp and send it to user and when they activate there account we should disable the otp by change the status
        Schema::create('user_otps', function (Blueprint $table) {
            $table->id();
            $table->string('otp');
            $table->string('phone_number');
            $table->timestamp('expired_at')->useCurrent();
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('user_otps');
    }
};
