<?php

namespace App\Repositories;

use App\Models\ArticleTag;
use App\Models\Article;
use Illuminate\Support\Facades\Cache;

class ArticleTagRepository extends Repository
{
    protected $modelClass = ArticleTag::class;

    public function all($is_cache = true){
        $cacheKey = config('global.cache.article_tag') ?? 'article_tags_all';
        if (Cache::has($cacheKey) && $is_cache){
            return Cache::get($cacheKey);
        }
        $tags = $this->model()->where('status', 1)->orderBy('sort', 'asc')->get();
        Cache::set($cacheKey, $tags);
        return $tags;
    }

    public function getByArticleId($articleId){
        return ArticleTag::whereHas('articles', function($q) use ($articleId) {
            $q->where('articles.id', $articleId);
        })->where('status', 1)->orderBy('sort', 'asc')->get();
    }

    /**
     * 获取分类下的标签及其文章（用于标签联动）
     * @param int $cateId 分类ID
     * @param int $articleLimit 每个标签显示的文章数
     * @param int $tagLimit 取前几个标签
     */
    public function getTagsWithArticlesByCateId($cateId, $articleLimit = 3, $tagLimit = 3, $is_cache = true){
        $cacheKey = config('global.cache.article_tag') . ':cate:' . $cateId;
        if (Cache::has($cacheKey) && $is_cache){
            return Cache::get($cacheKey);
        }

        // 获取该分类下的所有文章
        $articles = Article::where('article_cate_id', $cateId)
            ->where('status', 1)
            ->orderBy('release_at', 'desc')
            ->get();

        if ($articles->isEmpty()) {
            return [];
        }

        // 统计每个标签的文章数量
        $tagCounts = [];
        foreach ($articles as $article) {
            foreach ($article->tags as $tag) {
                if (!isset($tagCounts[$tag->id])) {
                    $tagCounts[$tag->id] = [
                        'tag' => $tag,
                        'count' => 0,
                    ];
                }
                $tagCounts[$tag->id]['count']++;
            }
        }

        if (empty($tagCounts)) {
            return [];
        }

        // 按文章数量降序排序，取前 N 个标签
        $sortedTags = collect($tagCounts)->sortByDesc('count')->take($tagLimit);

        // 构建返回数据，每个标签带上前 N 篇文章
        $result = [];
        foreach ($sortedTags as $item) {
            $tag = $item['tag'];
            $articles = $tag->articles()
                ->where('article_cate_id', $cateId)
                ->where('status', 1)
                ->orderBy('release_at', 'desc')
                ->take($articleLimit)
                ->get();

            $result[] = [
                'id' => $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
                'color' => $tag->color,
                'description' => $tag->description ?? '',
                'articles' => $articles,
            ];
        }

        Cache::set($cacheKey, $result);
        return $result;
    }
}
