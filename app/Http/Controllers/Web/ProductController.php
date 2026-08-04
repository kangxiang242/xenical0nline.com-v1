<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use App\Models\Brand;
use App\Models\Comment;
use App\Models\CommentImage;
use App\Models\CommentLabel;
use App\Models\Product;
use App\Models\ProductCate;
use App\Models\Slide;
use App\Models\ProductTag;
use App\Models\Spu;
use App\Models\Theme;
use App\Repositories\BrandRepository;
use App\Repositories\CateRepository;
use App\Repositories\ProductRepository;
use App\Repositories\TagRepository;
use App\Services\CartService;
use Illuminate\Http\Request;
use Carbon\Carbon;
class ProductController extends Controller
{
    public function index(ProductRepository $productRepository){
        $products = $productRepository->all();
        // id=1 固定排在列表最後（其餘順序不變）
        [$notOne, $one] = $products->partition(function ($p) {
            return (int) $p->id !== 1;
        });
        $products = $notOne->concat($one)->values();

        $slides = Slide::all();

        return template('product.index',compact('products', 'slides'));
    }


    public function show($id){
        $goods = Product::with(['attr', 'faqs'])->where('id',$id)->where('status',1)->first();

        if(!$goods){
            abort(404);
        }

        $skus = Product::where('status',1)->where('id','<>',$id)->get();

        $comment = Comment::where('status',1)->get()->shuffle();

        $comment_images = CommentImage::where('status',0)->get()->shuffle();

        foreach ($comment as $key => $item) {

            if($item->is_comment_image == 1 && !$item->comment_image && $comment_images->isNotEmpty()){
                $comment_image = $comment_images->pop();
                $item->comment_image = $comment_image->image;
                $item->save();

            }


            if ($key == 0) {
                $item->time_at = mt_rand(1, 10) . '分钟前';
            } elseif ($key == 1) {
                $item->time_at = mt_rand(30, 59) . '分钟前';
            } elseif ($key == 2) {
                $item->time_at = mt_rand(1, 12) . '小时前';
            } elseif ($key == 3) {
                $item->time_at = mt_rand(13, 24) . '小时前';
            } elseif ($key == 4) {
                $item->time_at = '昨天';
            } else {
                // 计算减少的天数
                $days = floor(($key - 4) / 4);
                // 获取当前日期，并减去相应的天数
                $date = Carbon::now()->subDays($days);
                $item->time_at = $date->format('m月d日');
            }
        }



        $comment_labels = CommentLabel::orderBy('sort')->get();

        $un_product_details_gb = app('cache.config')->get('product_details_gb');
        $product_details_gb = [];
        if($un_product_details_gb){
            $product_details_gb = json_decode($un_product_details_gb,true);
        }

        $un_product_details = app('cache.config')->get('product_details');
        $product_details = [];
        if($un_product_details){
            $product_details = json_decode($un_product_details,true);
        }

        $slides = Slide::all();

        $now = Carbon::now();
        $showCountdown = $now->between(
            Carbon::today()->setTime(15, 0, 0),
            Carbon::today()->setTime(16, 59, 59)
        );

        // official 版商品詳情視圖使用 $goods->details 輸出藥品訊息區塊；
        // 本站資料存在 config product_details，轉成 HTML 供新視圖渲染。
        $detailsHtml = '';
        if (!empty($product_details)) {
            $parts = [];
            foreach ($product_details as $item) {
                // 與原站視圖一致：desc 允許後台 HTML（原視圖以 {!! !!} 輸出）
                $parts[] = '<h3>'.($item['title'] ?? '').'</h3>'
                    .'<p>'.str_replace(PHP_EOL, '<br>', $item['desc'] ?? '').'</p>';
            }
            $detailsHtml = implode('', $parts);
        }
        $goods->setAttribute('details', $detailsHtml);

        return template('product.show',compact('goods','skus','comment','comment_labels','product_details_gb','product_details','slides','showCountdown'));
    }


    public function commentUp(Request $request){
        $comment = Comment::find($request->id);
        if($request->like == 1){
            $comment->up = $comment->up+1;
        }else{
            $comment->up = $comment->up-1;
        }
        if($comment->up < 0){
            $comment->up = 0;
        }

        $comment->save();
    }
}
