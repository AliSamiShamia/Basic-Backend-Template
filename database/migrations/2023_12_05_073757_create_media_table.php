<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mediable_id');
            $table->string('mediable_type');
            $table->string('url');
            $table->enum('type', ['cover', 'gallery', 'image', 'video', 'banner', 'profile'])->default('image');//normal,cover,gallery,...
            $table->enum('priority', ['normal', 'medium', 'high'])->default('normal');
            $table->string('thumb_url');
            $table->string('mime_type');
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
        Schema::dropIfExists('media');
    }
}
