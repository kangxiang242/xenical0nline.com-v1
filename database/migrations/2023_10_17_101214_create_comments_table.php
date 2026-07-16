<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();

            $table->string('phone')->nullable();

            $table->integer('star')->default(1);

            $table->text('content')->nullable();

            $table->ipAddress('ip')->nullable();

            $table->string('user_agent')->nullable();

            $table->tinyInteger('status')->default(1)->comment('1审核通过 0待审核 2审核不通过');

            $table->integer('sort')->default(0);

            $table->timestamp('time')->nullable();

            $table->tinyInteger('is_admin')->default(0)->comment('1管理员添加，0用户评论');

            $table->integer('up')->default(0)->comment('赞');

            $table->integer('total_number')->default(0)->comment('累积数量');

            $table->string('current_purchase')->nullable()->comment('本次已购');

            $table->text('customer_label')->nullable()->comment('顾客标签');

            $table->string('order_no')->nullable()->comment('订单编号');

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
        Schema::dropIfExists('comments');
    }
}
