<?php


namespace App\Repositories;



use App\Models\Brand;
use Illuminate\Support\Facades\Cache;

class BrandRepository extends Repository
{
    protected $modelClass = Brand::class;

    public function getAll(){
        if (Cache::has(config('global.cache.brands'))){
            $brands = Cache::get(config('global.cache.brands'));
        }else{
            $brands = $this->model()->where('status',1)->orderBy('sort','desc')->get();
            Cache::forever(config('global.cache.brands'),$brands);
        }
        return $brands;
    }
}
