@extends('web.layout')

@section('style')
    @parent
    <style>
        .order {
            width: 920px;
            margin: 20px auto;
            margin-top: 50px;
        }

        .tips {
            width: 100%;
            font-size: 14px;
            margin: 20px 0;
            line-height: 20px;
        }

        .ordertable .item {
            display: flex;
            border: 1px solid #2CB156;
            overflow: hidden;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .ordertable .item label {
            width: 100px;
            text-align: center;
            padding: 18px 0;
            font-size: 16px;
            line-height: 24px;
            background: #2CB156;
            display: flex;
            color: #fff;
        }
        .ordertable .item label span{
            align-self: center;
            width: 100%;
        }

        .ordertable .item .conta {
            flex: 1;
            text-align: center;
            padding: 18px 0;
            font-size: 16px;
            line-height: 24px;
            color: #000;
        }

        .ordertable .item .conta .shopitem {

            border-bottom: 1px solid #eaeaea;
            margin-bottom: 10px;
            padding-bottom: 10px;
        }

        .ordertable .item .conta .shopitem:last-child{
            border: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .ordertable .item .conta .shopitem .shopImg {
            display: inline-block;
            vertical-align: top;
            width: 80px;
            height: 80px;
            overflow: hidden;
            border: 1px solid #eaeaea;
        }

        .ordertable .item .conta .shopitem .shopImg img {
            width: 100%;
        }

        .ordertable .item .conta .shopitem .shopMsg {
            display: inline-block;
            vertical-align: top;

            height: 80px;
            margin-left: 20px;
            line-height: 80px;
        }

        .ordertable .item .conta .shopitem .shopMsg .name {
            width: 320px;

        }
        .ordertable .item .conta .shopitem .shopMsg .name em{
            font-style: normal;
            color:#f74545;
        }

        .ordertable .item .conta .shopitem .shopMsg span {
            display: inline-block;
            vertical-align: middle;
            width: 138px;
            line-height: 1;
        }

    </style>
@stop

@section('script')
    @parent
    <script src="{{ asset('static/js/sweetalert2.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script>
        if(flash_data){
            Swal.fire({
                title: flash_data.title,
                text: flash_data.message,
                icon: 'success',
                confirmButtonText: '好的'
            })
        }
        document.addEventListener('DOMContentLoaded', function () {
            if (!window.XenicalTracker) return;
            XenicalTracker.conversion('view_order', { order_no: @json($order->no ?? ''), status: 'success' }, 'order_success');
        });
    </script>
@stop



@section('content')
    <div class="container" style="padding-bottom: 100px">
        <div class="wrapper ">
            <div class="order">

                <div class="ordertable">
                    <div class="item">
                        <label><span>訂單號</span></label>
                        <div class="conta">{{ $order->no }}</div>
                        <label><span>下單時間</span></label>
                        <div class="conta">{{ $order->created_at }}</div>
                        <label><span>訂單狀態</span></label>
                        <div class="conta color2"><span>{{ \Illuminate\Support\Arr::get(\App\Models\Order::STATUS_TXT,$order->status) }}</span></div>
                    </div>
                    <div class="item">
                        <label><span>訂單人</span></label>
                        <div class="conta">{{ $order->name }}</div>
                        <label><span>聯絡電話</span></label>
                        <div class="conta">{{ $order->phone }}</div>
                        <label><span>電子信箱</span></label>
                        <div class="conta">{{ $order->email }}</div>
                    </div>


                    <div class="item">
                        <label><span>購物商品</span></label>
                        <div class="conta">
                            @foreach($order->products as $item)
                                <div class="shopitem">
                                    <div class="shopImg">
                                        <img src="{{ asset('uploads/'.$item->product_img) }}" alt="{{ $item->product_name }}" loading="lazy" decoding="async">
                                    </div>
                                    <div class="shopMsg">
                                        <span class="name">{!! $item->is_added?"<em>[加購]</em>":"" !!}{{ $item->product_name }}</span>
                                        <span>× {{ $item->number }}</span>
                                        <span>NT${{ $item->total_price }}</span>
                                    </div>
                                </div>

                            @endforeach
                        </div>
                    </div>

                    <div class="item">
                        <label><span>配送方式</span></label>
                        <div class="conta">
                            {{ $order->delivery_type?"超商(7-11) 取貨付款":"宅配 貨到付款" }}
                        </div>
                        <label><span>訂單總價</span></label>
                        <div class="conta">
                            NT${{ round($order->total_price) }}（{{ $order->freight>0?"含運費$".round($order->freight):"免運費" }}）
                        </div>
                    </div>
                    @if($order->delivery_type > 0)
                        <div class="item">
                            <label><span>門市號</span></label>
                            <div class="conta">
                                {{ $order->shop_no }}
                            </div>
                            <label><span>門市名稱</span></label>
                            <div class="conta">
                                {{ $order->shop_name }}
                            </div>
                        </div>
                    @endif
                    <div class="item">
                        <label><span>地址</span></label>
                        @if($order->delivery_type > 0 && $order->address)
                            <div class="conta">{{ $order->address }}</div>
                        @else
                            <div class="conta">{{ $order->city.$order->county.$order->street.$order->address }}</div>
                        @endif
                    </div>


                    <div class="item">
                        <label><span>訂單備註</span></label>
                        <div class="conta">{{ $order->remarks?:"無" }}</div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
