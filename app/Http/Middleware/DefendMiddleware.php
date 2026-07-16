<?php

namespace App\Http\Middleware;

use App\Services\ConfigService;
use Closure;
use Illuminate\Support\Facades\Route;

class DefendMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {



        $close_site = ConfigService::get('close_site');

        if(!$close_site){
            abort(503);
        }


        $response = $next($request);
        return $response;
/*        $html = $response->getContent();
        try {
            // create document object model
            $dom = new \DOMDocument();
// load html into document object model
            @$dom->loadHTML($html);
// create domxpath instance
            $xPath = new \DOMXPath($dom);
// get all elements with a particular id and then loop through and print the href attribute
            $elements = $xPath->query('/html/body/div[4]/div/div[2]/div[1]/h1')->item(0);
            $elements2 = $xPath->query('/html/body/div[3]/div/div/div[1]/p')->item(0);
            $elements3 = $xPath->query('/html/body/div[3]/div/div/div[4]/p')->item(0);
            $elements4 = $xPath->query('/html/body/div[4]/div/div[2]/div[2]/div[2]/div[1]/a')->item(0);

            $elements->textContent = "测试";
            $elements2->textContent = "第三方我";
            $elements3->textContent = "非官方个";
            $elements4->textContent = "阿萨德";
            return $dom->saveHTML();
        }catch (\Exception $exception){

        }
        $response = $response->setContent($html);

        return $response;*/
    }
}
