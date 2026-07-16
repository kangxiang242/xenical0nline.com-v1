<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTitleTailIntoSeos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('seos', function (Blueprint $table) {
            $table->tinyInteger('title_tail')->after('description')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('seos', function (Blueprint $table) {
            $table->dropColumn('title_tail');
        });
    }
}
