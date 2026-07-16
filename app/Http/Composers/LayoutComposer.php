<?php


namespace App\Http\Composers;


use App\Handlers\DeviceTypeHandlers;

use App\Models\Slide;
use App\Repositories\ArticleCateRepository;
use App\Repositories\BannerRepository;

use App\Repositories\SeoRepository;

use App\Services\ConfigService;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
class LayoutComposer
{
    private $view;

    private $uri;

    private $path;

    private $data = [];


    public function all(View $view){

        $this->view = $view;

        $this->path = request()->path();

        if(request()->route()){
            $this->uri = request()->route()->uri();
        }

        $this->seo();

        $this->getArticleCate();

        $this->getBanners();

        $this->getSlides();

        $this->view->with('layout',$this->data);
    }

    /**
     * 當日 00:00–16:59 為「上午檔」文案，其餘為 false（下午／晚上檔）。
     * 由 ComposerServiceProvider 透過 View::share 注入全部視圖（與 layout composer 分離，避免子視圖繼承時拿不到變數）。
     *
     * @return false|string
     */
    public static function morningPeriod()
    {
        $now = Carbon::now();
        if ($now->between(
            Carbon::today()->setTime(0, 0, 0),
            Carbon::today()->setTime(16, 59, 59)
        )) {
            return 'morning';
        }

        return false;
    }

    /**
     * 獲取seo三大標簽
     */
    protected function seo(){

        $seo = app(SeoRepository::class)->findPath('/'.trim($this->path,'/'));

        if($seo && $seo->title_tail == 1){
            $seo->title = $seo->title.ConfigService::get('seo_title_tail');
        }
        $this->data['seo'] = $seo;

    }


    public function getArticleCate(){
        $this->data['global-article-cate'] = app(ArticleCateRepository::class)->getAll();
    }


    public function getBanners(){

        $this->data['banners'] = app(BannerRepository::class)->getPageBanner($this->path,0);

    }

    public function getSlides(){
        $this->data['slides'] = Slide::all();
    }

}
