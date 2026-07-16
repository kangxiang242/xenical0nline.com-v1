<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('img')->nullable()->comment('主圖');
            $table->string('m_img')->nullable()->comment('手機版主圖');
            $table->text('subtitle')->nullable()->comment('副标题');
            $table->decimal('price')->default(0)->comment('价格');
            $table->decimal('market_price')->default(0)->comment('市场价格');
            $table->tinyInteger('status')->default(1)->comment('状态');
            $table->tinyInteger('is_stock')->default(1)->comment('是否有货');
            $table->integer('sort')->default(1);
            $table->longText('describe')->nullable()->comment('商品描述');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
