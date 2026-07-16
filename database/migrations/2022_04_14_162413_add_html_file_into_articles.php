<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHtmlFileIntoArticles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->tinyInteger('mode')->default(0)->after('brief')->comment('0在线编辑器 1自定义代码文章');
            $table->string('html_file')->nullable()->after('content')->comment('自定义布局html');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('mode');
            $table->dropColumn('html_file');

        });
    }
}
