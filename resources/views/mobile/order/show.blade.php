@extends('mobile.layout')

@section('style')
    @parent
    <style>
        .container{
            padding: 0 1.5rem;
        }
        .box-content {

            width: 100%;
            margin: 0 auto;
        }



        .order {
            margin-top: 2rem;
        }

        .order .item {
            margin-bottom: 2rem;
        }

        .order .row {
            border: 0.1rem solid #47C47D;

            margin-bottom: 1.6rem;

            box-sizing: border-box;
            flex-wrap: wrap;
            align-items: stretch;
            display: table;
            overflow: hidden;

            width: 100%;
            height: 4rem;
            border-radius: 0.6rem;
        }

        .order .row .left {
            background-color: #47C47D;
            color: #fff;

            width: 2rem;

            text-align: center;
            font-size: 1.4rem;
            height: auto;
            line-height: initial;
            word-break: break-all;
        }

        .order .row .right {
            padding-left: 8px;
            font-size: 1.4rem;
            width: 4.5rem;
            height: 100%;
            border-right: none !important;
            display: table-cell;
            vertical-align: middle;
            word-break: break-all;
            color: #262626;
            padding-right: 1rem;
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .order .row div {
            display: table-cell;
            vertical-align: middle;
        }


        .row-product{
            border: 0.1rem solid #47C47D;
            color: #262626;
            border-radius: 0.6rem;

            margin-bottom: 2rem;
        }

        .row-product .title{
            font-size: 1.4rem;
            background: #47C47D;
            color: #fff;
            line-height: 4rem;
            padding-left: 3rem;
            height: 4rem;
        }


        .row-product img {
            width: 8rem;

        }

        .row-product .product{
            position: relative;
            border-bottom: 0.01rem solid #666;
            margin-bottom: 0.2rem;
            padding: 1rem;
            display: flex;
            align-self: center;
        }

        .row-product .product:last-child{
            border: none;
            margin: 0;
        }

        .row-product .goods-info{

            margin-left: 2rem;
            height: 100%;
            align-self: center;
        }

        .row-product .goods-name{
            font-size: 1.6rem;


        }
        .row-product .goods-price{
            font-size: 1.4rem;
            margin-top: 2rem;
        }


    </style>
@stop

@section('script')
    @parent
    <script src="{{ asset('static/js/sweetalert2.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script>
        $('#banner').remove();

        if(flash_data){
            Swal.fire({
                icon:'success',
                title: flash_data.title,
                text: flash_data.message,
                color:'#000',
                width:'auto',
                backdrop:false,
                timer:2000,
                timerProgressBar:false,
                showConfirmButton:false,
            })
        }
        document.addEventListener('DOMContentLoaded', function () {
            if (!window.XenicalTracker) return;
            XenicalTracker.conversion('view_order', { order_no: @json($order->no ?? ''), status: 'success' }, 'order_success');
        });
    </script>
@stop



@section('content')
    <div class="container">

        <div class="row box-content">
            <div class="order">
                <div class="item">
                    <div class="row">
                        <div class="left">訂單號</div>
                        <div class="right">{{ $order->no }}</div>
                    </div>
                    <div class="row">
                        <div class="left">訂單狀態</div>
                        <div class="right">{{ \Illuminate\Support\Arr::get(\App\Models\Order::STATUS_TXT,$order->status) }}</div>
                    </div>


                    <div class="row-product">
                        <p class="title">訂單產品</p>
                        <div class="product-main">
                            @foreach($order->products as $item)

                                <div class="product clearfix">
                                    <div class="goods-img"><img src="{{ asset_upload($item->product_img) }}" alt="{{ $item->product_name }}" loading="lazy" decoding="async"></div>
                                    <div class="goods-info">
                                        <p class="goods-name">{{ $item->product_name }}</p>
                                        <p class="goods-price">NT${{ round($item->total_price) }}</p>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="row">
                        <div class="left">支付總額</div>
                        <div class="right">${{ round($order->total_price) }}（{{ $order->freight>0?"含運費$".round($order->freight):"免運費" }}）</div>
                    </div>

                    <div class="row">
                        <div class="left">生產時間</div>
                        <div class="right">{{ $order->created_at }}</div>
                    </div>

                </div>

                <div class="item">
                    <div class="row">
                        <div class="left">收件人姓名</div>
                        <div class="right">{{ $order->name }}</div>
                    </div>

                    <div class="row">
                        <div class="left">聯絡電話</div>
                        <div class="right">{{ $order->phone }}</div>
                    </div>

                    <div class="row">
                        <div class="left">電子信箱</div>
                        <div class="right">{{ $order->email }}</div>
                    </div>
                </div>

                <div class="item">
                    <div class="row">
                        <div class="left">配送方式</div>
                        <div class="right">
                            @if($order->delivery_type == 1)
                                超商(7-11) 取貨付款
                            @elseif($order->delivery_type == 2)
                                @if($order->shop_type == 2)
                                    超商(全家) 取貨付款
                                @elseif($order->shop_type == 3)
                                    超商(OK) 取貨付款
                                @else
                                    超商(萊爾富) 取貨付款
                                @endif
                            @else
                                宅配 貨到付款
                            @endif

                        </div>
                    </div>
                    @if($order->delivery_type > 0)
                        <div class="row">
                            <div class="left">門市號</div>
                            <div class="right">
                                {{ $order->shop_no }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="left">門市名稱</div>
                            <div class="right">
                                {{ $order->shop_name }}
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="left">收件地址</div>
                        <div class="right">{{ $order->city.$order->county.$order->street.$order->address }}</div>
                    </div>

                    @if($order->delivery_type == 0)
                        <div class="row">
                            <div class="left">收件方式</div>
                            <div class="right">本人收件</div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="left">送貨時段</div>
                        <div class="right">{{ \Illuminate\Support\Arr::get(\App\Models\Order::DELIVERY_TIME,$order->delivery_time?:1) }}</div>
                    </div>
                </div>

                <div class="item">
                    <div class="row">
                        <div class="left">訂單備註</div>
                        <div class="right">{{ $order->remarks?:"無" }}</div>
                    </div>
                </div>

            </div>

        </div>



    </div>
@endsection
