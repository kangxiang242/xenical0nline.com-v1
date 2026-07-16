<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExceptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exceptions', function (Blueprint $table) {
            $table->id();
            $table->ipAddress('ip')->nullable()->comment('訪問IP');
            $table->string('ip_country')->nullable()->comment('访问地區');
            $table->integer('status_code')->default(400)->comment('状态码');
            $table->text('message')->nullable()->comment('异常信息');
            $table->string('uri')->nullable()->comment('路径');
            $table->string('method')->nullable()->comment('请求方法');
            $table->text('user_agent')->nullable()->comment('载具信息');
            $table->json('parameters')->comment('请求参数');
            $table->json('headers')->comment('请求头');
            $table->json('trace');
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
        Schema::dropIfExists('exceptions');
    }
}
