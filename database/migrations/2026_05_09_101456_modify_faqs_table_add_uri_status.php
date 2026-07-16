<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyFaqsTableAddUriStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('faqs', function (Blueprint $table) {
            // 重命名字段
            $table->renameColumn('questions', 'title');
            $table->renameColumn('answers', 'content');

            // 新增字段
            $table->string('uri')->default('/')->after('id');
            $table->boolean('status')->default(true)->after('uri');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('faqs', function (Blueprint $table) {
            $table->dropColumn(['uri', 'status']);
            $table->renameColumn('title', 'questions');
            $table->renameColumn('content', 'answers');
        });
    }
}
