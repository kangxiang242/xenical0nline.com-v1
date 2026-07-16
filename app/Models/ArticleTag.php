<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ArticleTag extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'cat_ids',
        'color',
        'sort',
        'status',
    ];

    protected $casts = [
        'cat_ids' => 'array',
        'status' => 'integer',
        'sort' => 'integer',
    ];

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_tag_relations', 'tag_id', 'article_id')
            ->where('articles.status', 1)
            ->orderBy('article_tag_relations.id', 'desc');
    }
}
