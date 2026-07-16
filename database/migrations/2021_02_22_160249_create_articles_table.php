<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_cate_id')->comment('分类ID');
            $table->foreign('article_cate_id')->references('id')->on('article_cates')->onDelete('restrict')->onUpdate('cascade');
            $table->string('title')->comment('标题');
            $table->text('brief')->nullable(true)->comment('文章简介');
            $table->string('img')->nullable(true)->comment('文章主图');
            $table->string('img_alt')->nullable(true);
            $table->integer('read_num')->default(0)->comment('阅读数');
            $table->integer('real_read_num')->default(0)->comment('真实阅读数');
            $table->text('content')->nullable(true)->comment('内容');
            $table->integer('sort')->default(1)->comment("排序");
            $table->tinyInteger('status')->default(0)->comment("0草稿 1正常");
            $table->text('seo_title')->nullable(true);
            $table->text('seo_keyword')->nullable(true);
            $table->text('seo_description')->nullable(true);
            $table->timestamp('release_at')->comment("发布时间");
            $table->softDeletes();
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
        Schema::dropIfExists('articles');
    }
}
