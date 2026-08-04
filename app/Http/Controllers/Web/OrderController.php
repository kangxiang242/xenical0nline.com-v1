<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\MsgException;
use App\Http\Requests\OrderStoreRequest;
use App\Models\Product;
use App\Repositories\FaqRepository;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Validator;
class OrderController extends BaseController
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * OrderController constructor.
     * @param OrderRepository $orderRepository
     */
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }


    /**
     * 订单查询
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function check(Request $request){
        $faqs = app(FaqRepository::class)->getByUri('check');

        if($request->isMethod('POST')){
            $checkType = $request->input('check_type', 'contact');

            if($checkType === 'order_id'){
                // 订单号查询（本站特有：訂單編號查詢）
                $orderNo = trim($request->input('order_id', ''));
                if(!$orderNo){
                    return response()->json(['code'=>400,'message'=>'請填寫訂單編號']);
                }
                $orderNo = str_replace(' ', '', $orderNo);
                if(!preg_match('/^\d{16}$/', $orderNo)){
                    return response()->json(['code'=>400,'message'=>'訂單編號格式不正確']);
                }
                $order = $this->orderRepository->getByNo($orderNo);
            }else{
                // 联络资讯查询（與 official 一致：電話 + 信箱 + 驗證碼）
                $validator = Validator::make($request->all(), [
                    'phone' => 'required',
                    'email' => 'required|email',
                    'captcha_code'=>'required|captcha',
                ],[        
                    'phone.required'=>'請填寫聯絡電話',
                    'email.required'=>'請填寫電子郵箱',
                    'captcha_code.required'=>'請填寫驗證碼',
                    'email.email'=>'郵箱格式錯誤',
                    'captcha_code.captcha'=>'驗證碼驗證錯誤',
                ]);

                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return response()->json(['code'=>400,'message'=>$errors->first()]);
                }

                $order = $this->orderRepository->getByPhoneEmail(str_replace(' ','',$request->phone),$request->email);
            }

            if($order){
                return response()->json(['code'=>200,'message'=>'訂單查詢成功','jump'=>url('check/'.$order->no.'?source=check')]);
            }else{
                return response()->json(['code'=>400,'message'=>'您所查詢的訂單不存在']);
            }
        }

        return template('order.check')->with('faqs', $faqs);
    }

    /**
     * 订单下单成
     * @param $no
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function checking($no){

        $order = $this->orderRepository->getByNo($no);

        if(!$order){
            abort(404);
        }
        return template('order.show',compact('order'));
    }

    /**
     * 订单结算页
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function checkout($id,Request $request){
        $goods = Product::where('id',$id)->where('status',1)->first();
        if (!$goods){
            abort(404);
        }

        $products = Product::where('status',1)->orderBy('sort','desc')->get();

        //token 防止多次提交
        $form_token = md5(time());
        //将token存入session
        $request->session()->put('form_token',$form_token);
        return template('order.checkout',compact('form_token','goods','products'));
    }


    /**
     * 订单提交
     * @param OrderStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(OrderStoreRequest $request){
        try {

            $form_token = $request->input('form_token');
            if( !$request->session()->get('form_token') || $request->session()->get('form_token')!=$form_token ){
                throw new MsgException('請勿重複提交！');
            }
            $request->session()->put('form_token',null);

            $products = Product::where('id',$request->goods_id)->where('status',1)->get();
            if(!$products || $products->isEmpty()){
                throw new MsgException('商品数据有误！');
            }

            $order = $this->orderRepository->store($request->all(),$products);

            session()->flash('flash',json_encode(['type'=>'tips','msg'=>'訂單提交成功','sub_msg'=>'我們將會儘快為您安排發貨','code'=>200,'form_data'=>request()->except('_token','_method')],JSON_UNESCAPED_UNICODE));

            return response()->json([
                'status' => 'success',
                'redirect' => url('check/'.$order->no),
                'order_no' => $order->no,
            ]);
        }catch (MsgException $exception){
            Log::error('Order MsgException: '.$exception->getMessage(), ['ip'=>request()->ip(), 'data'=>request()->except('_token')]);
            return response()->json(['code'=>400,'msg'=>$exception->getMessage()],400);
        }catch (QueryException $exception){
            Log::error('Order QueryException: '.$exception->getMessage(), ['ip'=>request()->ip(), 'sql_data'=>request()->except('_token')]);
            return response()->json(['code'=>400,'msg'=>'系統出現異常！'],400);
        }catch (\Exception $exception){
            Log::error('Order Exception: '.$exception->getMessage(), ['ip'=>request()->ip(), 'trace'=>$exception->getTraceAsString()]);
            return response()->json(['code'=>400,'msg'=>'系統出現異常！'],400);
        }

    }
}
