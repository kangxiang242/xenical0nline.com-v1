<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShopIntoOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shop_no',100)->nullable()->after('user_agent');
            $table->string('shop_name',100)->nullable()->after('shop_no');
            $table->json('shop_data')->nullable()->after('shop_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('shop_no');
            $table->dropColumn('shop_name');
            $table->dropColumn('shop_data');
        });
    }
}
