<?php


namespace App\Services;


use App\Models\Product;
use Illuminate\Support\Arr;

class SoaService
{
    private $soa_host='https://strongsales.xyz';

    private $soa_token='5ee598dcc46705a44421b5c2872f8c96';

    private $post_data = [];


    protected function makeOrderNo(){
        return date('YmdHi').rand(1000,9999);
    }




    public function sendSoa(){
        $api_url = rtrim($this->soa_host).'/api/order/store2';
        $token = $this->soa_token;
        if (!isset($this->post_data['no'])){
            $this->post_data['no'] = $this->makeOrderNo();
        }

        $headers = [
            'Authorization:'.$token
        ];


        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $api_url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); //  如果不是https的就注释 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, 'SOA/5.0'); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($this->post_data)); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $res = curl_exec($curl); // 执行操作
        curl_close($curl); // 关闭CURL会话
        return json_decode($res,true);
    }

    public function __set($name, $value)
    {
        $this->post_data[$name] = $value;
    }

    public static function create($order,$product_id){

        $soa = new SoaService();
        $soa->merchant_id = 5;
        $soa->no = $order->no;
        $soa->name = $order->name;
        $soa->phone = $order->phone;
        $soa->email = $order->email;
        $soa->total_price = $order->total_price;
        $soa->goods_price = $order->total_price;
        $soa->freight = 0;
        $soa->delivery_type = $order->delivery_type;
        $soa->city = $order->city;
        $soa->county = $order->county;
        $soa->street = $order->street;
        $soa->address = $order->address;
        $soa->remarks = $order->remarks;
        $soa->ip = $order->ip;
        $soa->user_agent = $order->user_agent;
        $soa->store_name = $order->shop_name;
        $soa->store_no = $order->shop_no;

        $product = Product::find($product_id);

        $soa->goods = [[
            'goods_name'=>'羅氏鮮',
            'unit'=>'盒',
            'number'=>$product->quantity,
            'unit_price'=>$order->total_price,
            'total_price'=>$order->total_price,
        ]];

        $soa->sendSoa();

        $order->is_soa = 1;

        $access_log = new \App\Models\AccessLog();
        $access_log->where('ip',$order->ip)->delete();
    }
}
