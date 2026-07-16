<?php


namespace App\Repositories;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class Repository
{
    protected $modelClass;

    public function model(){
        return new $this->modelClass;
    }


    public function cache($key,\Closure $closure){
        if(Cache::has($key) && $data = Cache::get($key)){
            $data = Cache::get($key);
        }else{
            $data = call_user_func($closure);
            Cache::forever($key,$data);
        }
        return $data;
    }
}
