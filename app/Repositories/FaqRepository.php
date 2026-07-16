<?php


namespace App\Repositories;


use App\Models\Faq;
use Illuminate\Support\Facades\Cache;
class FaqRepository extends Repository
{
    protected $modelClass = Faq::class;

    protected $limit=null;

    public function all($is_cache = true){
        if (Cache::has(config('global.cache.faq')) && $is_cache){
            $faq = Cache::get(config('global.cache.faq'));
        }else{
            $faq = $this->model()->orderBy('sort','desc')->get();
            Cache::set(config('global.cache.faq'),$faq);
        }
        if($this->limit){
            $faq = $faq->slice(0,$this->limit);
        }
        return $faq;
    }

    public function getByUri($uri, $is_cache = true){
        $cacheKey = config('global.cache.faq') . ':' . $uri;
        if (Cache::has($cacheKey) && $is_cache){
            return Cache::get($cacheKey);
        }
        $faq = $this->model()->where('uri', $uri)->where('status', 1)->orderBy('sort')->get();
        Cache::set($cacheKey, $faq);
        return $faq;
    }

    public function limit($limit = null){
        $this->limit = $limit;
        return $this;
    }
}
