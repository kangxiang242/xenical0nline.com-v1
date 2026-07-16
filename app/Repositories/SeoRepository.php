<?php


namespace App\Repositories;


use App\Models\Seo;
use Illuminate\Support\Facades\Cache;
class SeoRepository extends Repository
{
    protected $modelClass = Seo::class;

    public function get($is_cache = true){
        if (Cache::has(config('global.cache.seo')) && $is_cache){
            $seo = Cache::get(config('global.cache.seo'));
        }else{
            $seo = $this->model()->get();
            Cache::set(config('global.cache.seo'),$seo);
        }
        return $seo;
    }

    /**
     * 根据uri获取对应的seo标签
     * @param $uri
     * @return mixed
     */
    public function findPath($uri){
        $seo = $this->get()->keyBy('path');
        return $seo->get($uri);
    }



}
