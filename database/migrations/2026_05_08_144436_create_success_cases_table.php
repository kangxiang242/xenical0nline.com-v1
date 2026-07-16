<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuccessCasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('success_cases', function (Blueprint $table) {
            $table->id();
            $table->string('duration', 50)->default('')->comment('周期');
            $table->string('result', 100)->default('')->comment('效果');
            $table->string('before_image', 255)->nullable()->comment('服用前图片路径');
            $table->string('after_image', 255)->nullable()->comment('服用后图片路径');
            $table->text('content')->comment('顾客引言内容');
            $table->string('name', 50)->default('')->comment('顾客姓名');
            $table->string('age', 20)->default('')->comment('年龄');
            $table->string('occupation', 50)->default('')->comment('职业');
            $table->integer('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态：0=禁用，1=启用');
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
        Schema::dropIfExists('success_cases');
    }
}
