<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductsPriceToInt extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('price')->default(0)->change();
            $table->integer('market_price')->default(0)->change();
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0)->change();
            $table->decimal('market_price', 10, 2)->default(0)->change();
        });
    }
}
