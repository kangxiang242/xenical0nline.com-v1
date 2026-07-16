<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShopTypeIntoOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->tinyInteger('shop_type')->default(0)->after('shop_name')->comment('0非便利店 1=711 2=全家 3=OK 4=萊爾富');
        });
        \App\Models\Order::where('delivery_type',1)->update(['shop_type'=>1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('shop_type');
        });
    }
}
