<?php


namespace App\Http\Controllers\Web;


use App\Handlers\DeviceTypeHandlers;
use App\Models\Article;
use App\Models\Compute;
use App\Models\Product;
use App\Repositories\FaqRepository;
use App\Services\VehicleService;
use Illuminate\Http\Request;

class PageController extends BaseController
{
    /**
     * 瘦身計算機（/compute）— 與 official 標準模板一致
     */
    public function evaluate(Request $request){

        if($request->ajax() && $request->isMethod('POST')){
            $interpretation = app('cache.config')->get('interpretation');
            if($interpretation){
                $interpretation = json_decode($interpretation,true);
                $bmi_status = 2;
                if($request->bmi <= 18.4){
                    $bmi_status = 1;
                }elseif ($request->bmi >= 18.5 && $request->bmi <= 23.9){
                    $bmi_status = 2;
                }elseif ($request->bmi >= 24.0 && $request->bmi <= 27.9){
                    $bmi_status = 3;
                }elseif ($request->bmi >= 28){
                    $bmi_status = 4;
                }

                $inter = [];
                foreach ($interpretation as $item){
                    if($item['bmi'] == $bmi_status && $item['activity'] == $request->activityLevel){
                        $inter = $item;
                        break;
                    }
                }

                $goods = null;
                if($inter['goods']){
                    $goods = Product::with('attr')->where('id',$inter['goods'])->where('status',1)->first();
                }



                Compute::create([
                    'sex'=>$request->sex,
                    'age'=>$request->age,
                    'height'=>$request->height,
                    'weight'=>$request->weight,
                    'motion_level'=>$request->activityLevel,
                    'bmi'=>$request->bmi,
                    'bmr'=>$request->bmr,
                    'tdee'=>$request->tdee,
                    'ip'=>VehicleService::IP(),
                    'user_agent'=>$request->userAgent()
                ]);

                return template('evaluate-result',compact('inter','goods'))->with('bmi',$request->bmi)->with('tdee',$request->tdee)->with('bmr',$request->bmr);

            }
            return "";

        }

        $css = app('cache.config')->get('page_evaluate_css');
        return template('evaluate', compact('css'));
    }

    public function faq(){
        $faq = app(FaqRepository::class)->all();
        return template('faq', compact('faq'));
    }

    /**
     * BMI 計算機（/bmi）— 本站特有頁面
     */
    public function bmi(Request $request){

        if($request->ajax() && $request->isMethod('POST')){
            $interpretation = app('cache.config')->get('interpretation');
            if($interpretation){
                $interpretation = json_decode($interpretation,true);
                $bmi_status = 2;
                if($request->bmi <= 18.4){
                    $bmi_status = 1;
                }elseif ($request->bmi >= 18.5 && $request->bmi <= 23.9){
                    $bmi_status = 2;
                }elseif ($request->bmi >= 24.0 && $request->bmi <= 27.9){
                    $bmi_status = 3;
                }elseif ($request->bmi >= 28){
                    $bmi_status = 4;
                }

                $inter = [];
                foreach ($interpretation as $item){
                    if($item['bmi'] == $bmi_status && $item['activity'] == $request->activityLevel){
                        $inter = $item;
                        break;
                    }
                }

                $goods = null;
                if($inter['goods']){
                    $goods = Product::with('attr')->where('id',$inter['goods'])->where('status',1)->first();
                }



                Compute::create([
                    'sex'=>$request->sex,
                    'age'=>$request->age,
                    'height'=>$request->height,
                    'weight'=>$request->weight,
                    'motion_level'=>$request->activityLevel,
                    'bmi'=>$request->bmi,
                    'bmr'=>$request->bmr,
                    'tdee'=>$request->tdee,
                    'ip'=>VehicleService::IP(),
                    'user_agent'=>$request->userAgent()
                ]);

                return template('evaluate-result',compact('inter','goods'))->with('bmi',$request->bmi)->with('tdee',$request->tdee)->with('bmr',$request->bmr);

            }
            return "";

        }

        $news = $this->getEvaluateNews();
        $faqs = app(FaqRepository::class)->getByUri('bmi');

        return template('bmi',compact('news', 'faqs'));
    }

    public function bmr()
    {
        $news = $this->getComputeNews();
        $faqs = app(FaqRepository::class)->getByUri('bmr');
        $css = app('cache.config')->get('page_compute_css');

        return template('bmr', compact('news', 'faqs', 'css'));
    }

    private function getComputeNews()
    {
        $article_ids = app('cache.config')->get('page_compute_article_ids');
        if ($article_ids) {
            $article_ids = array_filter(json_decode($article_ids, true), function ($id) {
                return $id !== null && $id !== '';
            });

            if (!empty($article_ids)) {
                return Article::whereIn('id', $article_ids)->where('status', 1)->get();
            }
        }

        return $this->getEvaluateNews();
    }

    /**
     * 體脂肪率計算機（對應 resources/views/web/body-fat.blade.php）
     */
    public function bodyFat(Request $request)
    {
        $news = $this->getBodyFatNews();
        $faqs = app(FaqRepository::class)->getByUri('body-fat');
        $css = app('cache.config')->get('page_body_fat_css');

        return template('body-fat', compact('news', 'faqs', 'css'));
    }

    private function getBodyFatNews()
    {
        $article_ids = app('cache.config')->get('page_body_fat_article_ids');
        if ($article_ids) {
            $article_ids = array_filter(json_decode($article_ids, true), function ($id) {
                return $id !== null && $id !== '';
            });

            if (!empty($article_ids)) {
                return Article::whereIn('id', $article_ids)->where('status', 1)->get();
            }
        }

        return $this->getEvaluateNews();
    }

    private function getEvaluateNews(){
        $article_ids = app('cache.config')->get('page_evaluate_article_ids');
        if($article_ids){
            $article_ids = array_filter(json_decode($article_ids,true), function($id){
                return $id !== null && $id !== '';
            });
            if(!empty($article_ids)){
                return Article::whereIn('id',$article_ids)->where('status',1)->get();
            }
        }

        return [];
    }

    /**
     * 後台 TinyMCE 內文常多包一層 div.art / div.article，與前台 section.editor 重複時剝掉最外層。
     */
    private function normalizePageHtml($html): string
    {
        $html = unwrap_outer_div_by_class($html, 'art');
        return unwrap_outer_div_by_class($html, 'article');
    }

    public function about(){
        $title = app('cache.config')->get('about_title');
        $title_gb = app('cache.config')->get('about_title_gb');
        $title_en = app('cache.config')->get('about_title_en');
        $content = $this->normalizePageHtml(app('cache.config')->get('about_content'));
        $content_gb = $this->normalizePageHtml(app('cache.config')->get('about_content_gb'));
        $html_code = app('cache.config')->get('about_html_code');
        return template('page',compact('title','title_gb','title_en','content','content_gb','html_code'));
    }

    public function guide(){
        $title = app('cache.config')->get('notes_buy_title');
        $title_gb = app('cache.config')->get('notes_buy_title_gb');
        $title_en = app('cache.config')->get('notes_buy_title_en');
        $content = $this->normalizePageHtml(app('cache.config')->get('notes_buy_content'));
        $content_gb = $this->normalizePageHtml(app('cache.config')->get('notes_buy_content_gb'));
        $css = app('cache.config')->get('notes_buy_css');
        return template('page',compact('title','title_en','title_gb','content','content_gb','css'));
    }

    public function paymentDelivery(){
        $title = app('cache.config')->get('page_payment_title');
        $title_gb = app('cache.config')->get('page_payment_title_gb');
        $title_en = app('cache.config')->get('page_payment_title_en');
        $content = $this->normalizePageHtml(app('cache.config')->get('page_payment_delivery'));
        $content_gb = $this->normalizePageHtml(app('cache.config')->get('page_payment_delivery_gb'));
        $css = app('cache.config')->get('page_payment_css');
        return template('page',compact('title','title_en','title_gb','content','content_gb','css'));
    }

    public function afterSales(){
        $title = app('cache.config')->get('page_after_sales_title');
        $title_gb = app('cache.config')->get('page_after_sales_title_gb');
        $title_en = app('cache.config')->get('page_after_sales_title_en');
        $content = $this->normalizePageHtml(app('cache.config')->get('page_after_sales'));
        $content_gb = $this->normalizePageHtml(app('cache.config')->get('page_after_sales_gb'));
        $css = app('cache.config')->get('page_after_sales_css');
        return template('page',compact('title','title_en','title_gb','content','content_gb','css'));
    }

    public function privacy(){
        $title = app('cache.config')->get('page_privacy_title');
        $title_gb = app('cache.config')->get('page_privacy_title_gb');
        $title_en = app('cache.config')->get('page_privacy_title_en');
        $content = $this->normalizePageHtml(app('cache.config')->get('page_privacy_article'));
        $content_gb = $this->normalizePageHtml(app('cache.config')->get('page_privacy_article_gb'));
        return template('page',compact('title','title_en','title_gb','content','content_gb'));
    }

}
