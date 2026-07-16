<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleTagRelation extends Model
{
    protected $fillable = [
        'article_id',
        'tag_id',
    ];

    public function tag(): BelongsTo
    {
        return $this->belongsTo(ArticleTag::class, 'tag_id');
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'article_id');
    }
}
