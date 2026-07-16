<?php


namespace App\Repositories;


use App\Models\Tag;
use Illuminate\Support\Facades\Cache;
class TagRepository extends Repository
{
    protected $modelClass = Tag::class;

    public function getShowTags($is_cache=true){

        if (Cache::has(config('global.cache.show_tags')) && $is_cache){
            $tags = Cache::get(config('global.cache.show_tags'));
        }else{
            $tags = $this->model()->where('is_show',1)->get();
            Cache::set(config('global.cache.show_tags'),$tags);
        }

        return $tags;
    }
}
