<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComputesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('computes', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('sex')->default(1)->comment('性别 1男 2女');
            $table->integer('age')->default(0)->comment('年龄');
            $table->integer('height')->default(0)->comment('身高');
            $table->integer('weight')->default(0)->comment('体重');
            $table->tinyInteger('motion_level')->default(0)->comment('运动强度');
            $table->string('bmi')->default(0)->comment('身体质量指数');
            $table->string('bmr')->default(0)->comment('基礎代謝');
            $table->string('tdee')->default(0)->comment('消耗熱量');
            $table->ipAddress('ip')->nullable()->comment('ip');
            $table->text('user_agent')->nullable()->comment('载具');
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
        Schema::dropIfExists('computes');
    }
}
