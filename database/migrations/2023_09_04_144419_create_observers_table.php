<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObserversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('observers', function (Blueprint $table) {
            $table->id();
            $table->string('uri')->index();
            $table->ipAddress('ip')->index();
            $table->string('host')->nullable();
            $table->string('explain')->nullable();
            $table->string('event')->nullable()->comment('事件');
            $table->string('ipcountry')->nullable();
            $table->string('referer')->nullable();
            $table->string('user_agent')->nullable();
            $table->longText('headers');
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
        Schema::dropIfExists('observers');
    }
}
