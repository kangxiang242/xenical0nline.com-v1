<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterObserversHeadersNullable extends Migration
{
    public function up()
    {
        Schema::table('observers', function (Blueprint $table) {
            $table->longText('headers')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('observers', function (Blueprint $table) {
            $table->longText('headers')->nullable(false)->change();
        });
    }
}
