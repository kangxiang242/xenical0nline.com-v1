<?php


namespace App\Services;


use App\Models\Brand;
use App\Models\Cate;
use App\Models\CollectGoods;
use App\Models\CollectLog;
use App\Models\Product;
use App\Models\ProductAttr;
use App\Models\ProductCate;
use App\Models\ProductTag;
use App\Models\Tag;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use QL\QueryList;

class CollectService
{


    protected $client;

    protected $result;

    public $cate;

    public $brands;

    protected $page_count=1;

    protected $cur_page = 1;

    protected $exist_codes = [];


    public function __construct()
    {
        $this->client = new Client();
    }

    public function handle(){

        $this->exist_codes = CollectGoods::pluck('goods_code')->toArray();

        $this->getPagination('/items',0);

        /*$goods = CollectGoods::where('goods_code','8dbfd0')->first();
        $this->getGoodsInfo($goods);*/



        //$this->getCategories();


        /*sleep(2);
        $this->getBrands();
        sleep(2);*/

        /*sleep(1);
        foreach ($this->cate as $cate){
            foreach($cate['sub'] as $sub){
                $this->getPagination($sub['href'],0);
                sleep(2);
            }
        }*/


    }

    /**
     * 获取所有分类信息
     */
    public function getCategories(){
        $url = 'https://www.17386.com.tw/categories/ecc214';
        $res = $this->request($url);
        $cate[] = $res->find('.main-navigation .p10-0:eq(5)');
        $cate[] = $res->find('.main-navigation .p10-0:eq(6)');
        $cate[] = $res->find('.main-navigation .p10-0:eq(7)');
        $cate[] = $res->find('.main-navigation .p10-0:eq(8)');
        $cate[] = $res->find('.main-navigation .p10-0:eq(9)');
        $cate[] = $res->find('.main-navigation .p10-0:eq(10)');
        $cate[] = $res->find('.main-navigation .p10-0:eq(11)');


        foreach($cate as $item){
            $href = $item->find('.col-xs-10 a')->attr('href');
            $text = $item->find('.col-xs-10 a')->text();
            $sub_li = $item->find('.scrollable-menu li')->htmls();
            $sub = [];
            foreach ($sub_li as $v){
                $n = QueryList::html($v)->find('a');
                $sub[] = [
                    'name'=>$n->text(),
                    'href'=>$n->attr('href')
                ];
            }
            $this->cate[] = [
                'name'=>$text,
                'href'=>$href,
                'sub'=>$sub,
            ];
        }



    }

    /**
     * 获取品牌信息
     */
    public function getBrands(){
        $url = 'https://www.17386.com.tw/categories/ecc214';
        $res = $this->request($url);
        $brand = $res->find('.main-navigation .p10-0:eq(12)');
        $sub_li = $brand->find('#brand-list li')->htmls();
        foreach ($sub_li as $v){
            $n = QueryList::html($v)->find('a');
            $this->brands[] = [
                'name'=>$n->text(),
                'href'=>$n->attr('href')
            ];
        }

    }

    /**
     * 获取分頁數據
     * @param $path
     * @param int $type 0分类商品 1品牌商品
     */
    public function getPagination($path,$type=0){
        try {
            $url = "https://www.17386.com.tw".$path;


            $this->log("開始獲取分頁數據 | URL:{$url}");

            $result = $this->request($url);

            $pagination = $result->find('.pagination-nav li')->texts();

            if ($pagination && $pagination->isNotEmpty()){
                $pager = array_filter($pagination->toArray());
                $page = array_shift($pager);

                $this->cur_page = $page;
                $this->page_count = array_pop($pager);
            }else{
                $this->page_count = 1;
            }

            $this->log("分頁數據獲取完成 | URL:{$url} | 共{$this->page_count}頁");

            for ($i=0;$i<$this->page_count;$i++){
                $page = $i+1;
                $this->getGoods($url."?page=".$page);
                //$this->cur_page = $this->cur_page+1;
            }


        }catch (\Exception $exception){
            $this->log($exception->getMessage(),null,1);
        }
    }

    public function getGoods($url){


        try {
            $result = $this->request($url);

            $productLayout = $result->find('.product-layout')->htmls();

            $collectGoods = [];
            $time = date('Y-m-d H:i:s');

            $this->log("開始抓取商品CODE | URL:{$url}");
            foreach ($productLayout as $key=>$goods){
                $href = QueryList::html($goods)->find('.single-item-title')->attr('href');
                $hrefs = explode('/',trim($href,'/'));
                if(Arr::get($hrefs,1)){
                    $stock = QueryList::html($goods)->find('.red')->html();
                    $g_code = Arr::get($hrefs,1);

                    if(in_array($g_code,$this->exist_codes)){
                        CollectGoods::where('goods_code',$g_code)->update(['is_stock'=>$stock?0:1,'updated_at'=>$time]);
                        sleep(1);
                    }else{
                        $collectGoods[] = [
                            'goods_code'=>$g_code,
                            'is_stock'=>$stock?0:1,
                            'created_at'=>$time,
                            'updated_at'=>$time,
                        ];
                    }
                }else{
                    throw new \Exception("goods_code爲空 | URL:{$url} | 第{$key}個商品");
                }
            }
            if($collectGoods){
                CollectGoods::insert($collectGoods);
            }


            $this->log("商品CODE抓取完畢 | URL:{$url}",$collectGoods);
            sleep(2);
        }catch (\Exception $exception){
            $this->log($exception->getMessage(),null,1);
            throw new \Exception($exception->getMessage());
        }

    }

    /**
     * @param CollectGoods $collectGoods
     */
    public function getGoodsInfo(CollectGoods $collectGoods){
        DB::transaction(function()use($collectGoods){
            $url = "https://www.17386.com.tw/items/".$collectGoods->goods_code;
            $result = $this->request($url);

            $p_cate_name = $result->find('.breadcrumb li:eq(1)')->text();
            $cate_name = $result->find('.breadcrumb li:eq(2)')->text();
            $product_name = $result->find('.prodetail:eq(0) .product-name')->text();

            $goodsInfo = $result->find('.prodetail:eq(0) .product_info li')->texts();

            $sketch=$tags=$code=$brand_name = '';
            foreach($goodsInfo as $item){
                if(strpos($item,'網路價') !== false){
                    continue;
                }

                $a = str_replace(PHP_EOL,'',$item);
                $info = explode(':',$a);

                $key_name = Arr::get($info,0);

                if($key_name == '品牌'){
                    $brand_name = trim(Arr::get($info,1,''));
                }elseif ($key_name == '情趣用品編號'){
                    $code = trim(Arr::get($info,1,''));
                }elseif($key_name == '特色'){
                    $tags = trim(Arr::get($info,1,''));
                }else{
                    $sketch = trim($item);
                }
            }


            //获取销售价
            $untreated_price = $result->find('.prodetail:eq(0) .product_info .dib')->text();
            $price = Arr::get(explode('$',str_replace(',','',$untreated_price)),1);

            //获取市场价
            $untreated_market_price = $result->find('.prodetail:eq(0) .product_info .dib .light-grey')->text();
            $market_price = str_replace(',','',$untreated_market_price);


            $img = $result->find('.prodetail:eq(0)')->prev()->find('.thumbnails img')->attr('src');
            $img = $this->saveImage('https://www.17386.com.tw'.$img);


            $img_resize = config('image.resizes.goods');
            foreach($img_resize as $size){
                app(ImageService::class)->resize(public_path('/uploads/'.$img),$size);
            }



            //获取商品详情
            $describe = $result->find('#item-information .mt20')->html();
            $remote_desc_img = QueryList::html($describe)->find('img')->attrs('src');
            if($remote_desc_img && $remote_desc_img->isNotEmpty()){
                //把商品详情的图片保存到本地，并替换src
                $remote_desc_img->map(function($value,$key)use (&$describe){
                    $local = $this->saveImage($value);
                    if(strpos($local,'.gif') !== false){
                        $search = 'src="'.$value.'"';
                        $attr = 'src="'.$local.'" ';
                    }else{
                        $desc_resize = config('image.resizes.goods-desc');
                        foreach($desc_resize as $size){
                            app(ImageService::class)->resize(public_path('/uploads/'.$local),$size);
                        }


                        /*$attr = ' data-src="'.$local.'" src="'.$resize_local.'" ';
                        $search = 'src="'.$value.'"';*/
                        $search = 'src="'.$value.'"';
                        $attr = 'src="'.$local.'" ';
                    }

                    $describe = str_replace($search,$attr,$describe);

                    return $value;
                });
            }


            //获取属性
            $untreated_attr = explode(PHP_EOL,str_replace("<br>","\r\n",$describe));
            $attr = [];
            foreach($untreated_attr as $item){
                if(strpos($item,'：') !== false){
                    $attrs = explode('：',strip_tags($item));
                    $attr_name = Arr::get($attrs,0);
                    $attr_value = Arr::get($attrs,1);

                    if($attr_name && $attr_value && mb_strlen($attr_name)<=8 && mb_strlen($attr_value)<=100){

                        $attr[] = [
                            'name'=>trim(Arr::get($attrs,0)),
                            'value'=>trim(Arr::get($attrs,1)),
                        ];
                    }
                }
            }

            //是否有加购商品
            $untreated_additional = $result->find('#collapseOne tbody tr')->htmls();
            $additional = [];
            foreach($untreated_additional as $key=>$item){
                $ql = QueryList::html($item);
                $additional[] = [
                    'collect_code'=>$ql->find('#checkbox_addon_item_'.$key)->attr('value'),
                    'price'=>$ql->find('.red')->text(),
                ];
            }



            $brand = 0;
            if ($brand_name){
                $brand = Brand::where('name',$brand_name)->first();
                if(!$brand){
                    $brand = Brand::create([
                        'name'=>$brand_name,
                    ]);
                }
            }

            $tagModel = [];
            if($tags){
                $tags = array_filter(explode(" ",$tags));
                foreach($tags as $tag){
                    $tagModel[] = Tag::firstOrCreate(['title'=>$tag]);
                }
            }

            $cate = null;
            if($p_cate_name){
                $p_cate = Cate::where('name',$p_cate_name)->where('pid',0)->first();
                if(!$p_cate){
                    $p_cate = Cate::create([
                        'pid'=>0,
                        'name'=>$p_cate_name,
                        'sort'=>1,
                    ]);
                }

                if($p_cate){
                    $cate = Cate::where('name',$cate_name)->where('pid',$p_cate->id)->first();

                    if(!$cate){
                        $cate = Cate::create([
                            'pid'=>$p_cate->id,
                            'name'=>$cate_name,
                            'sort'=>1,
                        ]);
                    }
                }
            }





            $time = date('Y-m-d H:i:s');
            $productData = [
                'brand_id'=>$brand?$brand->id:0,
                'code'=>$code,
                'name'=>$product_name,
                'img'=>$img,
                'subtitle'=>$sketch,
                'price'=>$price?:0,
                'market_price'=>$market_price?:0,
                'describe'=>$describe,
                'collect_code'=>$collectGoods->goods_code,
                'is_stock'=>$collectGoods->is_stock,
                'added'=>$additional,
            ];

            $product = Product::where('collect_code',$collectGoods->goods_code)->first();
            if($product){
                Product::where('collect_code',$collectGoods->goods_code)->update($productData);
            }else{
                $product = Product::create($productData);
            }



            if($attr){
                $attr_insert = [];

                foreach($attr as $item){
                    $attr_insert[] = [
                        'product_id'=>$product->id,
                        'name'=>$item['name'],
                        'value'=>$item['value'],
                        'created_at'=>$time,
                        'updated_at'=>$time,
                    ];
                }
                ProductAttr::where('product_id',$product->id)->delete();
                ProductAttr::insert($attr_insert);
            }


            if($cate){
                ProductCate::where('product_id',$product->id)->delete();
                ProductCate::create([
                    'product_id'=>$product->id,
                    'cate_id'=>$cate->id,
                ]);
            }

            if($tagModel){
                ProductTag::where('product_id',$product->id)->delete();
                foreach($tagModel as $item){
                    ProductTag::create([
                        'product_id'=>$product->id,
                        'tag_id'=>$item->id
                    ]);
                }
            }
        });





    }


    /**
     * 保存图片到本地
     *
     * @param $url
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return string
     */
    public function saveImage($url){
        $arr=explode('.', $url);
        $filename = uniqid().'.'.end($arr);
        $path = '/items/'.$filename;
        $this->client->request('GET',$url,['verify' => false,'sink'=>public_path('/uploads'.$path)]);
        return $path;
    }


    /**
     * @param $content
     * @param null $data
     * @param int $is_error
     */
    protected function log($content,$data=null,$is_error=0){
        CollectLog::create([
            'content'=>$content,
            'data'=>is_array($data)?json_encode($data,JSON_UNESCAPED_UNICODE):$data,
            'is_error'=>$is_error
        ]);
    }

    public function request($url){
        $result = $this->client->request('GET',$url,[
            'verify' => false,
            'headers'=>[
                'Cookie'=>'iagree=agreed; '
            ]
        ]);

        return QueryList::html($result->getBody());
    }


}
