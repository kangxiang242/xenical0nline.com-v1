<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlidesTable extends Migration
{
    public function up()
    {
        Schema::create('slides', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100)->comment('标签标题');
            $table->string('desc', 200)->comment('描述');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('slides');
    }
}
