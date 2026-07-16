<?php


namespace App\Repositories;


use App\Models\Product;
use Illuminate\Support\Facades\Cache;
class ProductRepository extends Repository
{
    protected $modelClass = Product::class;

    protected $limit = null;

    public function all($is_cache = true){
        if (Cache::has(config('global.cache.products')) && $is_cache){
            $products = Cache::get(config('global.cache.products'));
        }else{
            $products = $this->model()->with('attr')->where('status',1)->orderBy('sort','desc')->get();
            Cache::set(config('global.cache.products'),$products);
        }

        if($this->limit){
            $products = $products->slice(0,$this->limit);
        }

        return $products;
    }

    public function limit($limit = null){
        $this->limit = $limit;
        return $this;
    }

}
