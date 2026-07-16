<?php


namespace App\Repositories;


use App\Models\Banner;
use Illuminate\Support\Facades\Cache;
class BannerRepository extends Repository
{
    protected $modelClass = Banner::class;

    public function get($is_cache=true){

        if (Cache::has(config('global.cache.banner')) && $is_cache){
            $banner = Cache::get(config('global.cache.banner'));
        }else{
            $banner = $this->model()->get();
            Cache::set(config('global.cache.banner'),$banner);
        }

        return $banner;
    }

    public function getPageBanner($page,$type=0){
        $banner = $this->get();
        $page = '/'.trim($page,'/');

        return $banner->filter(function($item)use ($page,$type){
            return $item->type == $type && $item->page == $page;
        });



    }

}
