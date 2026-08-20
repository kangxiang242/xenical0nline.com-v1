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

        // 僅記錄前台訪問，排除後台（Filament 面板及登入/登出）訪問記錄
        if ($this->isAdminRequest($request)) {
            return;
        }

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

    /**
     * 判斷是否為後台/管理端請求：是則不記錄訪問日誌
     */
    private function isAdminRequest($request)
    {
        $route = $request->route();
        $routeName = $route ? $route->getName() : null;

        if ($routeName) {
            // Filament 面板內部路由及後台登入/登出路由（含子域名後台）
            if (str_starts_with($routeName, 'filament.')) {
                return true;
            }
            if ($routeName === 'admin.login.submit') {
                return true;
            }
        }

        // 防禦：本地單域名下按後台面板路徑前綴排除
        $adminPanelPath = trim((string) env('ADMIN_PANEL_PATH', ''), '/');
        if ($adminPanelPath !== '' && str_starts_with(trim($request->path(), '/'), $adminPanelPath)) {
            return true;
        }

        return false;
    }

    private function getUrlPath($path){
        $str = substr($path , 0 , 1);
        if($str != '/' ){
            $path = '/'.$path;
        }
        return $path;
    }
}
