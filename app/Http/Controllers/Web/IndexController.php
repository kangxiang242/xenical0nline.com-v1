<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\FaqRepository;
use App\Repositories\NewRepository;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(ProductRepository $productRepository,Request $request){
        // 首頁商品區：僅顯示指定 id（順序 2 → 3 → 5）
        $homeProductIds = [2, 3, 5];
        $allProducts = $productRepository->all();
        $products = collect($homeProductIds)
            ->map(function ($id) use ($allProducts) {
                return $allProducts->firstWhere('id', $id);
            })
            ->filter();
        $faqs = app(FaqRepository::class)->getByUri('/')->slice(0, 6);


        $for_people_untreated = app('cache.config')->get('for_people');
        $for_people = [];
        if($for_people_untreated){
            $for_people = json_decode($for_people_untreated);
        }


        $trouble_untreated = app('cache.config')->get('trouble');
        $trouble = [];
        if($trouble_untreated){
            $trouble = json_decode($trouble_untreated);
        }

        $trade_show_untreated = app('cache.config')->get('trade_show');
        $trade_show = [];
        if($trade_show_untreated){
            $trade_show = json_decode($trade_show_untreated,true);
        }

        $news = app(NewRepository::class)->newNews(3);

        // 獲取首頁用戶計數
        $userCount = \App\Services\ConfigService::get('index_user_count', '124344649');

        // 成功案例
        $successCases = \App\Models\SuccessCase::where('status', 1)->orderBy('sort', 'asc')->get();

        return template('index',compact('products','faqs','for_people','trouble','trade_show','news','userCount','successCases'));
    }

}
