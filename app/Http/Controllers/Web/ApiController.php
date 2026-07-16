<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Config;
use App\Services\ConfigService;
use App\Services\SitemapService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ApiController extends Controller
{
    public function clearRedis(Request $request){
        $keys = $request->keys;
        if($keys){
            $keys = explode(',',$keys);
            foreach($keys as $v){
                Cache::forget($v);
            }
        }
    }

    public function cacheConfig(){
        ConfigService::cache();
    }

    public function robots(){
        $config = Config::where('name','robots')->first();
        if($config){
            return response($config->content)->header('Content-type','text/plain');
        }else{
            return response('')->header('Content-type','text/plain');
        }
    }

    public function sitemap(){
        $xml = app(SitemapService::class)->generate();
        return response($xml)->header('Content-type','text/xml');
    }
}
