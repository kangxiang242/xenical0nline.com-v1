<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ArticleCate extends Model
{
    /**
     * 获取博客文章的评论
     */

    public function faqs()
    {
        return $this->hasMany(Faq::class,'category_id');
    }

    protected $casts = ['options' => 'json', 'options_gb' => 'json'];

    public function article()
    {
        return $this->hasMany(Article::class);
    }

}
