<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSuccessCasesNullable extends Migration
{
    public function up()
    {
        Schema::table('success_cases', function (Blueprint $table) {
            $table->string('before_image', 255)->nullable()->change();
            $table->string('after_image', 255)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('success_cases', function (Blueprint $table) {
            $table->string('before_image', 255)->default('')->change();
            $table->string('after_image', 255)->default('')->change();
        });
    }
}
