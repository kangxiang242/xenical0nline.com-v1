<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannerEmbedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banner_embeds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('banner_id');
            $table->foreign('banner_id')->references('id')->on('banners')->onDelete('cascade');
            $table->tinyInteger('type')->default(0)->comment('類型 0圖片 1代碼');
            $table->text('content')->nullable()->comment('嵌入内容');
            $table->text('img_path')->nullable()->comment('嵌入圖片');
            $table->string('img_alt')->nullable()->comment('嵌入圖片ALT');
            $table->string('img_size')->nullable()->comment('圖片大小');
            $table->string('x')->default(0);
            $table->string('y')->default(0);
            $table->text('style')->nullable();
            $table->tinyInteger('debug')->default(0)->comment('是否為調試');
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
        Schema::dropIfExists('banner_embeds');
    }
}
