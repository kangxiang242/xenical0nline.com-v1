<?php


namespace App\Services;


use App\Exceptions\MsgException;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
class OrderService
{
    public function store(array $data){
        return DB::transaction(function () use($data) {

            $ip_count = $this->getDayIpOrderCount(request()->ip());
            if($ip_count>=5){
                throw new MsgException("您今日下單數過多，請理性消費！");
            }

            $product_data = Arr::get($data,'product');
            $products = $this->getProducts(array_keys($product_data));
            if(!$products){
                throw new MsgException("商品數據提交有誤!");
            }

            $product_price = $this->countOrderPrice($products,$product_data);
            $insert_data = [
                'no'=>$this->makeOrderNo(),
                'inside_no'=>$this->makeOrderInsideNo(),
                'ip'=>request()->ip(),
                'user_agent'=>request()->header('user-agent'),
                'product_price'=>$product_price,
            ];
            if($product_price>3000){
                $freight = 0;
            }else{
                $freight = 150;
            }
            $insert_data['freight'] = $freight;
            $insert_data['total_price'] = $freight+$product_price;
            $insert_data2 = Arr::only($data,['name','phone','email','city','county','address','remarks']);
            $insert_data = array_merge($insert_data,$insert_data2);
            $order = Order::create($insert_data);

            $order_product = [];
            $time = date('Y-m-d H:i:s');
            foreach ($products as $item){
                $num = Arr::get($product_data,$item->id,1);
                $order_product[] = [
                    'order_id'=>$order->id,
                    'product_id'=>$item->id,
                    'product_name'=>$item->name,
                    'product_img'=>$item->img,
                    'number'=>$num,
                    'unit_price'=>$item->price,
                    'total_price'=>$num*$item->price,
                    'product'=>$item->toJson(),
                    'created_at'=>$time,
                    'updated_at'=>$time,
                ];
            }

            if($order_product){
                OrderProduct::insert($order_product);
            }else{
                throw new MsgException("提交失敗，提交數據有誤");
            }
            return $order;
        });


    }

    /**
     * 获取商品信息
     * @param array $ids
     * @return mixed
     */
    public function getProducts($ids = []){
        return Product::whereIn('id',$ids)->where('status',1)->get();
    }

    /**
     * 计算商品总价格
     * @param $products
     * @param $product_data
     * @return float|int
     */
    public function countOrderPrice($products,$product_data){
        $product_price = 0;
        foreach($products as $item){

            $num = Arr::get($product_data,$item->id,1);
            $product_price += $item->price*$num;
        }
        return $product_price;
    }

    /**
     * 根據IP獲取今日下單數
     * @param $ip
     * @return mixed
     */
    public function getDayIpOrderCount($ip){
        return Order::whereBetWeen('created_at',[Carbon::now()->startOfDay(),Carbon::now()->endOfDay()])->where('ip',$ip)->count();
    }


    /**
     * 生成订单号
     * @return string
     */
    public function makeOrderNo(){
        $no = date('YmdHi').rand(1000,9999);
        $order = Order::where('no',$no)->first();
        if($order){
            $this->makeOrderNo();
        }
        return $no;
    }

    /**
     * 生成内部订单号
     * @return string
     */
    public function makeOrderInsideNo(){
        $count = Order::whereBetWeen('created_at',[Carbon::now()->startOfDay(),Carbon::now()->endOfDay()])->count();
        return 'R1-'.date('YmdHi').'-'.($count+1);
    }
}
