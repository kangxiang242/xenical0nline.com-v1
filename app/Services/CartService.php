<?php


namespace App\Services;


use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class CartService
{
    protected $select = ['id','brand_id','code','name','img','status','is_stock','price','added_price','market_price'];

    protected $added_cart_nums = [];

    public function getCart(){

        $carts = Cookie::get('carts');
        $product = [];
        $added_product = [];
        if($carts){
            $carts = json_decode($carts,true);


            if($carts){
                $ids = $this->getColumnMap($carts,'id');
                $product = Product::whereIn('id',$ids)->where('status',1)->select($this->select)->orderBy(DB::raw('FIND_IN_SET(id, "' . implode(",", $ids) . '"' . ")"))->get();
                $nums = collect($carts)->keyBy('id');
                foreach($product as &$item){
                    $item->cart_num = Arr::get($nums->get($item->id),'num');
                }

                $added_product = $this->getAddedCart();
            }
        }



        return [
            'product'=>$product,
            'added_product'=>$added_product,
            'added_nums'=>$this->added_cart_nums,
        ];


    }

    public function getAddedCart(){
        $added_carts = Cookie::get('added_carts');
        $added_data = [];
        if($added_carts){
            $added_carts = json_decode($added_carts,true);

            $ids = $this->getColumnMap($added_carts,'id');
            $product = Product::whereIn('id',$ids)->where('status',1)->select($this->select)->orderBy(DB::raw('FIND_IN_SET(id, "' . implode(",", $ids) . '"' . ")"))->get()->keyBy('id');
            if($product && $product->isNotEmpty()){

                $product = $product->toArray();

                foreach($added_carts as $item){
                    $added_data[$item['pid']][] = $product[$item['id']];
                    $this->added_cart_nums[$item['pid'].'_'.$item['id']] = $item['num'];
                }
            }


        }
        return $added_data;
    }

    /**
     * 对购物车商品数据进行有序整合
     *
     */
    public function integrateCart(){
        $cart = $this->getCart();
        if(isset($cart['product']) && $cart['product']){
            return $cart['product']->map(function($item, $key)use ($cart){
                $id = $item->id;
                $added_product = [];
                if(isset($cart['added_product'][$id]) && $cart['added_product'][$id]){

                    foreach ($cart['added_product'][$id] as $v){
                        $v['cart_num'] = $cart['added_nums'][$id.'_'.$v['id']];
                        $added_product[] = $v;
                    }
                }
                $item->added_product = $added_product;
                return $item;
            });
        }
        return [];
    }

    /**
     * 直接购买 解析数据
     * @param $keyt
     * @return mixed
     */
    public function keytBuild($keyt){
        $product_ids = json_decode($keyt,true);

        $product = Product::select($this->select)->where('id',$product_ids['id'])->get();

        $added_ids = [];
        $tmp_numbs = [];
        if(isset($product_ids['added']) && count($product_ids['added'])>0){
            foreach($product_ids['added'] as $item){
                $added_ids[] = $item['id'];
                $tmp_numbs[$item['id']] = $item['num'];
            }

            $added_product = Product::whereIn('id',$added_ids)->where('status',1)->select($this->select)->orderBy(DB::raw('FIND_IN_SET(id, "' . implode(",", $added_ids) . '"' . ")"))->get();
            foreach ($added_product as $item){
                $item->cart_num = Arr::get($tmp_numbs,$item->id,1);
            }


        }
        foreach($product as $item){
            $item->cart_num = Arr::get($product_ids,'num',1);
            if(isset($added_product) && $added_product){
                $item->added_product = $added_product->toArray();
            }else{
                $item->added_product = [];
            }
        }
        return $product;

    }

    public function getCartIds(){
        $carts = Cookie::get('carts');
        $ids = [];
        if($carts){
            $carts = json_decode($carts,true);

            if($carts){
                foreach($carts as $item){
                    $ids[] = $item['id'];
                }
            }
        }
        return $ids;
    }


    /**
     * PHP获取二维数组中某一列的值集合
     *
     * @param $arr
     * @param $name
     * @return array
     */
    protected function getColumnMap($arr,$name){
        $arr2 = [];
        foreach ($arr as $key => $value) {
            $arr2[] = $value[$name];
        }
        return $arr2;
    }
}
