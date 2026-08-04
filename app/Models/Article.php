<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Article extends Model
{
    protected function casts(): array
    {
        return [
            'release_at' => 'datetime',
        ];
    }

    protected $fillable = [
        'title', 'brief', 'img', 'img_alt', 'content', 'sort', 'status',
        'seo_title', 'seo_keyword', 'seo_description', 'custom_css',
        'article_cate_id', 'read_num', 'real_read_num', 'release_at'
    ];

    public function cate()
    {
        return $this->belongsTo(ArticleCate::class,'article_cate_id');
    }

    public function tags()
    {
        return $this->belongsToMany(ArticleTag::class, 'article_tag_relations', 'article_id', 'tag_id')
            ->where('article_tags.status', 1)
            ->orderBy('article_tags.sort', 'asc');
    }

    public function getTagIdsAttribute()
    {
        return $this->tags()->pluck('article_tags.id')->toArray();
    }

    public function scopeWithTagIds($query, $tagIds)
    {
        if ($tagIds && is_array($tagIds) && !empty($tagIds)) {
            return $query->whereHas('tags', function ($q) use ($tagIds) {
                $q->whereIn('article_tag_relations.tag_id', $tagIds);
            });
        }
        return $query;
    }
}
