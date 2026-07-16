<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleTagsTables extends Migration
{
    public function up(): void
    {
        // article_tags 表
        Schema::create('article_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('标签名称');
            $table->string('slug')->unique()->comment('标签别名');
            $table->text('description')->nullable()->comment('标签描述');
            $table->json('cat_ids')->nullable()->comment('关联的文章分类ID数组');
            $table->string('color', 7)->default('#1E88E5')->comment('标签颜色');
            $table->integer('sort')->default(0)->comment('排序');
            $table->boolean('status')->default(true)->comment('状态');
            $table->timestamps();

            $table->index('slug');
            $table->index('status');
        });

        // article_tag_relations 中间表
        Schema::create('article_tag_relations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_id');
            $table->unsignedBigInteger('tag_id');
            $table->timestamps();

            $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('article_tags')->onDelete('cascade');

            $table->unique(['article_id', 'tag_id']);
            $table->index('article_id');
            $table->index('tag_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_tag_relations');
        Schema::dropIfExists('article_tags');
    }
}
