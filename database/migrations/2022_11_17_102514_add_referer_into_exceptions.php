<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRefererIntoExceptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exceptions', function (Blueprint $table) {
            $table->string('referer')->nullable()->after('method');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exceptions', function (Blueprint $table) {
            $table->dropColumn('referer');
        });
    }
}
