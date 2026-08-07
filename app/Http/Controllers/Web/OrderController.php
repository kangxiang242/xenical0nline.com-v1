<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\MsgException;
use App\Http\JsonResponse;
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
            $checkType = $request->input('check_type', 'order_id');

            if($checkType === 'order_id'){
                // 订单号查询
                $orderNo = trim($request->input('order_id', ''));
                if(!$orderNo){
                    return JsonResponse::make()->statusCode(422)->status(false)->message('請填寫訂單編號')->send();
                }
                $orderNo = str_replace(' ', '', $orderNo);
                if(!preg_match('/^\d{16}$/', $orderNo)){
                    return JsonResponse::make()->statusCode(422)->status(false)->message('訂單編號格式不正確')->send();
                }
                $order = $this->orderRepository->getByNo($orderNo);
            }else{
                // 联络资讯查询
                $validator = Validator::make($request->all(), [
                    'email' => 'required|email',
                    'phone' => 'required',
                ],[
                    'email.required'=>'請填寫電子信箱',
                    'email.email'=>'電子信箱格式錯誤',
                    'phone.required'=>'請填寫訂購電話',
                ]);

                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return JsonResponse::make()->statusCode(422)->status(false)->message($errors->first())->send();
                }

                $order = $this->orderRepository->getByPhoneEmail(str_replace(' ','',$request->phone),$request->email);
            }

            if($order){
                return JsonResponse::make()
                    ->message('訂單查詢成功')
                    ->redirect(url('check/'.$order->no.'?source=check'))
                    ->flash()
                    ->send();
            }else{
                return JsonResponse::make()->statusCode(400)->status(false)->message('您所查詢的訂單不存在')->send();
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
