<?php


namespace App\Repositories;


use App\Models\AdminRands;
use App\Exceptions\MsgException;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Services\SoaService;
use App\Services\VehicleService;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Rizhou\Control\Supply\StoreSynchronizing;

class OrderRepository extends Repository
{
    protected $modelClass = Order::class;

    /**
     * 通过订单号获取订单
     * @param $no
     * @return mixed
     */
    public function getByNo($no){
        $order = $this->model()->whereNo($no)->first();
        if(!$order){
            $order = AdminRands::whereNo($no)->first();
        }
        return $order;
    }

    /**
     * 通过电话邮箱获取订单
     * @param $phone
     * @param $email
     * @return mixed
     */
    public function getByPhoneEmail($phone,$email){
        $order = $this->model()->where('email',$email)->where('phone',$phone)->orderBy('id','desc')->first();
        if(!$order){
            $order = AdminRands::where('email',$email)->where('phone',$phone)->orderBy('id','desc')->first();
        }
        return $order;
    }

    /**
     * 订单生成
     * @param array $data
     * @param array $products
     * @return mixed
     */
    public function store(array $data,$products){


        return DB::transaction(function () use($data,$products) {

            $ip_count = $this->getDayIpOrderCount(request()->ip());

            if($ip_count>=5){
                throw new MsgException("您今日下單數過多，請理性消費！");
            }


            $product_price = $this->countOrderPrice($products);

            $insert_data = [
                'no'=>$this->makeOrderNo(),
                'inside_no'=>$this->makeOrderInsideNo(),
                'ip'=>VehicleService::IP(),
                'ipcountry' => request()->header('cf-ipcountry'),
                'user_agent'=>request()->header('user-agent'),
                'product_price'=>$product_price,
                'delivery_type'=>Arr::get($data,'order_type', 0)
            ];

            if(Arr::get($data,'order_type') > 0){
                $store_id = Arr::get($data,'store_id');

                if(!$store_id){
                    throw new MsgException("便利店選擇有誤!");
                }
                $city = Arr::get($data,'city');
                $county = Arr::get($data,'county');
                $street = Arr::get($data,'street');

                if(Arr::get($data,'order_type') == 1){
                    $shop_guard = '711';
                }elseif(Arr::get($data,'order_type') == 2){
                    $shop_guard = 'ezship';
                }else{
                    throw new MsgException("便利店數據有誤！");
                }

                $shops = StoreSynchronizing::make($shop_guard)->getShop($city,$county,$street);

                if(!$shops){
                    throw new MsgException("便利店信息有誤!");
                }
                $shop = [];
                foreach($shops as $item){
                    if($item['shop_no'] == $store_id){
                        $shop = $item;
                        break;
                    }
                }
                if(!$shop){
                    throw new MsgException("便利店信息有誤2!");
                }
                $insert_data['address'] = $shop['shop_address'];
                $insert_data['shop_no'] = $shop['shop_no'];
                $insert_data['shop_name'] = $shop['shop_name'];
                $insert_data['shop_type'] = Arr::get($shop,'shop_type',0)+1;
                $insert_data['shop_data'] = $shop;
            }else{
                $insert_data['address'] = Arr::get($data,'address') ?? '';
            }

            $freight_where = \App\Services\ConfigService::get('freight_where',0);
            if($product_price>$freight_where){
                $freight = 0;
            }else{
                $freight = \App\Services\ConfigService::get('freight',0);
            }
            $insert_data['freight'] = $freight;
            $insert_data['total_price'] = $freight+$product_price;
            $insert_data2 = Arr::only($data,['name','phone','email','city','county','street','remarks','delivery_time']);
            $insert_data = array_merge($insert_data,$insert_data2);

            /*$correct = true;
            if($products->first()->quantity <= 6){
                if (request()->header('cf-ipcountry') == 'TW'){
                    $dis_ips = ['103.137','61.222','61.227','111.252','est','test','TEST','Test','策','丹','测试','測試','訂單','订单','1234','9876','5432','4321','5678','testmail','163.com','qq.com'];
                    $cur_ip = request()->header('cf-connecting-ip',request()->ip());
                    $is_ts = false;
                    foreach ($dis_ips as $v){
                        if(strpos($cur_ip,$v) !== false || strpos($insert_data['name'],$v) !== false || strpos($insert_data['email'],$v) !== false || strpos($insert_data['phone'],$v) !== false || strpos($insert_data['remarks'],$v) !== false){
                            $is_ts = true;
                            break;
                        }
                    }
                    if(!$is_ts && rand(1,2) == 2){
                        $correct = false;
                    }

                }
            }

            if($correct){
                $order = $this->model()->create($insert_data);
            }else{
                try {
                    $insert_data['is_soa'] = 0;
                    $order = AdminRands::create($insert_data);
                    try {
                        SoaService::create($order,$products->first()->id);
                    }catch (\Exception $exception){

                    }
                }catch (\Exception $exception){
                    $order = $this->model()->create($insert_data);
                }



            }*/

            $order = $this->model()->create($insert_data);


            $order_product = [];
            $time = date('Y-m-d H:i:s');
            foreach ($products as $item){
                $num = 1;
                $order_product[] = [
                    'order_id'=>$order->id,
                    'product_id'=>$item->id,
                    'product_name'=>$item->name.$item->sub_name,
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
     * @return float|int
     */
    public function countOrderPrice($products){
        $product_total_price = 0;
        foreach($products as $item){

            $product_total_price += $item->price;


        }

        return $product_total_price;
    }

    /**
     * 根據IP獲取今日下單數
     * @param $ip
     * @return mixed
     */
    public function getDayIpOrderCount($ip){
        return $this->model()->whereBetWeen('created_at',[Carbon::now()->startOfDay(),Carbon::now()->endOfDay()])->where('ip',$ip)->count();
    }


    /**
     * 生成订单号
     * @return string
     */
    public function makeOrderNo(){
        $no = date('YmdHi').rand(1000,9999);
        $order = $this->model()->where('no',$no)->first();
        if($order){
            return $this->makeOrderNo();
        }
        return $no;
    }

    /**
     * 生成内部订单号
     * @return string
     */
    public function makeOrderInsideNo(){
        $count = $this->model()->whereBetWeen('created_at',[Carbon::now()->startOfDay(),Carbon::now()->endOfDay()])->count();
        return 'R1-'.date('YmdHi').'-'.($count+1);
    }
}
