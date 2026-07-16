<?php

namespace App\Listeners;

use App\Events\AccessEvents;
use App\Handlers\DeviceTypeHandlers;
use App\Models\AccessLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Arr;

class AccessLogListeners
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  AccessEvents  $event
     * @return void
     */
    public function handle(AccessEvents $event)
    {
        $request = $event->request;

        $data = [
            'url'=>$this->getUrlPath($request->path()),
            'method'=>$request->method(),
            'host'=>$request->getHost(),
            'referer'=>Arr::get($_SERVER,'HTTP_REFERER'),
            'ip'=>$request->header('cf-connecting-ip',$request->ip()),
            'user_agent'=>$request->userAgent(),
            'device'=>DeviceTypeHandlers::getDevice(),
            'crawler'=>DeviceTypeHandlers::getCrawler()
        ];
        AccessLog::create($data);
    }

    private function getUrlPath($path){
        $str = substr($path , 0 , 1);
        if($str != '/' ){
            $path = '/'.$path;
        }
        return $path;
    }
}
