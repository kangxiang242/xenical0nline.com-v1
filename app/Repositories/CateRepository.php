<?php


namespace App\Repositories;


use App\Models\Cate;
use Illuminate\Support\Facades\Cache;


class CateRepository extends Repository
{
    protected $modelClass = Cate::class;

    /**
     * 获取指定pid数据
     *
     * @param int $pid
     * @return mixed
     */
    public function getPidCate($pid = 0){
        return $this->getCateAll();
    }

    /**
     * 获取所有分类
     *
     * @return mixed
     */
    public function getSubCate(){
        return $this->getCateAll();
    }

    /**
     * 获取指定pid
     *
     * @param int $id
     * @return mixed
     */
    public function find($id = 0){
        $cate = $this->getCateAll()->keyBy('id');
        if ($cate->has($id)){
            return $cate->get($id);
        }else{
            return null;
        }
    }

    /**
     * 获取所有分类
     *
     * @return mixed
     */
    public function getCateAll(){

        if (Cache::has(config('global.cache.cate'))){
            $cate = Cache::get(config('global.cache.cate'));
        }else{
            $cate = $this->model()->with('sub')->where('pid',0)->orderBy('sort','asc')->get();
            Cache::forever(config('global.cache.cate'),$cate);
        }

        return $cate;
    }

}
