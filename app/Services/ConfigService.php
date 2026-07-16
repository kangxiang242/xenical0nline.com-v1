<?php


namespace App\Services;


use App\Models\Config;
use Illuminate\Support\Facades\Cache;
class ConfigService
{

    private static $key = "config";

    public static function cache(){
        $config = Config::all();
        foreach($config as $item){
            Cache::put(self::$key.":{$item->name}", $item->content, 3600);
        }
    }

    /**
     * 获取配置值
     * @param $key
     * @param null $default
     * @return mixed
     */
    public static function get($key,$default=null){

        $content = Cache::get(self::$key.':'.$key);

        if(is_null($content)){
            $config = Config::where('name',$key)->first();

            if($config){
                $content = $config->content;
                Cache::put(self::$key.":".$key,$content, 3600);
            }
        }

        return is_null($content)?$default:$content;
    }
}
