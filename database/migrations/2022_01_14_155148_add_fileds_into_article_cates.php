<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFiledsIntoArticleCates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('article_cates', function (Blueprint $table) {
            $table->integer('sort')->default(1)->after('name')->comment('排序，从小到大');
            $table->tinyInteger('status')->default(1)->after('sort')->comment('1正常 0关闭');
            $table->string('uri')->nullable()->after('status')->comment('路径');
            $table->longText('desc')->nullable()->after('uri')->comment('描述');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('article_cates', function (Blueprint $table) {
            $table->dropColumn(['sort','status','uri','desc']);
        });
    }
}
