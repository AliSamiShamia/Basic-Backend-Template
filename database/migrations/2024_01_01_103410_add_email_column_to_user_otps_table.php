<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_otps', function (Blueprint $table) {
            $table->string('email')->nullable()->after('phone_number');
            $table->string('phone_number')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_otps', function (Blueprint $table) {
            $table->dropColumn(['email']);
            $table->string('phone_number')->nullable(false)->change();
        });
    }
};
