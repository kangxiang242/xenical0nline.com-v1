@extends('mobile.layout')
@php
    $freight_where = \App\Services\ConfigService::get('freight_where',0);
    $freight_price = \App\Services\ConfigService::get('freight',0);

    $delivery_type_all = \App\Services\ConfigService::get('delivery_type',[]);
    if($delivery_type_all){
        $delivery_type_all = json_decode(\App\Services\ConfigService::get('delivery_type',[]),true);
    }
@endphp
@section('style')
    @parent
    <link rel="stylesheet" href="{{ asset('static/mobile/less/checkout.css') }}?ver={{ config('app.asset_version') }}">
@stop

@section('script')
    @parent
    <script src="{{ asset('static/js/xie.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('static/js/jquery.contip.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('static/js/sweetalert2.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('static/js/xarea.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('static/js/api.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script>
        $(".form-control").blur(function(){
            if($(this).val()){
                $(this).addClass('focus');
                $(this).removeClass('red-error');
            }else{
                $(this).removeClass('focus');
                $(this).addClass('red-error');
            }
        });

        $("select[name='goods_ids']").change(function(){
            var id = $(this).val();
            var img = $(this).find("option:selected").attr('data-img');
            //var name = $(this).find("option:selected").text();
            var p1_name = $(this).find("option:selected").attr('data-name');
            var p2_name = $(this).find("option:selected").attr('data-sub-name');

            $("input[name='goods_id']").val(id);
            $('.goods-title .p1').text(p1_name);
            $('.goods-title .p2').text(p2_name);
            $('.goods-img').attr('src',img);


            var price = parseInt($(this).find("option:selected").attr('data-price'));
            var market_price = parseInt($(this).find("option:selected").attr('data-market-price'));

            $('#goods-price').text(format(market_price));

            var discount_elem = $('#discount-price');
            if(market_price-price>0){
                discount_elem.text(format(market_price-price));
                discount_elem.parents('dl').show();
            }else{
                discount_elem.text(0);
                discount_elem.parents('dl').hide();
            }

            var freight_where = parseInt("{{ $freight_where }}");
            var freight_price = parseInt("{{ $freight_price }}");

            var order_price = price;
            if(freight_where > price){
                order_price  += freight_price;
                $('#freight-price').text("NT$"+format(freight_price));
                $('#top-freight-price').text("含運費NT$"+freight_price);
            }else{
                $('#freight-price').text("免費");
                $('#top-freight-price').text("免費配送");
            }

            $('#order-price').text(" "+format(order_price));
            $('#top-order-price').text(format(order_price))


        });

        function format (num) {
            var reg=/\d{1,3}(?=(\d{3})+$)/g;
            return (num + '').replace(reg, '$&,');
        }
        $(document).ready(function() {
            var offsetH = $(document).height();
            var offsetW = $(window).height();
            if((offsetH - offsetW) > 10){
                $('.top-fixed').addClass('start')

            }else{
                $('.top-fixed').removeClass('start')
            }


        });
    </script>
@stop

@section('content')
    <div class="checkout-header">
        <div class="wrapper">
            <a href="{{ url('/') }}">
                <img src="{{ asset('static/img/m.logo2.png') }}" width="180px" alt="logo" decoding="async">
            </a>
{{--            <h1 class="page-title">安全結賬</h1>--}}
        </div>
    </div>

    <div class="top-fixed">
        <div class="order">
            <div class="total">
                <p class="price">訂單總額NT$ <span class="top-order-price" id="top-order-price">
                        @if($goods->price<$freight_where)
                            {{ round($goods->price+$freight_price) }}
                        @else
                            {{ round($goods->price) }}
                        @endif
                    </span></p>
                <p class="sub">(<span id="top-freight-price">
                        @if($goods->price<$freight_where)
                            含運費NT${{ round($freight_price) }}
                        @else
                            免運費
                        @endif</span>)</p>
            </div>
            <button class="submit-btn" onclick="$('#order-form').submit()"  data-track-section="checkout" data-track-name="checkout.submit.click" data-observer="提交訂單按鈕">提交訂單</button>
        </div>
    </div>

    <div class="checkout-container">
        <form method="POST" action="{{ url('order') }}" id="order-form" data-track-checkout onsubmit="return orderStore();">
            {{ csrf_field() }}
            <div class="checkout-wrapper">


                <div class="main">

                    <div class="card">
                        <p class="title">訂單內容</p>
                        <div class="product-main">

                            <div class="goods">
                                <div class="img-wrap">
                                    <img class="goods-img" src="{{ asset('uploads/'.$goods->img) }}" alt="{{ $goods->name }}" loading="lazy" decoding="async">
                                </div>
                                <div class="info">
                                    <div class="goods-title">
                                        <p class="p1">{{ $goods->name }}</p>
                                        <p class="p2">{{ $goods->quantity }}{{ $goods->quantity == 1?"盒標準裝":"盒優惠套裝" }}</p>
                                    </div>
                                    <input type="hidden" name="goods_id" value="{{ $goods->id }}">
                                    <input type="hidden" name="version" value="{{ config('app.asset_version') }}">
                                    <select class="change" name="goods_ids" data-track-field="goods_ids">
                                        <option data-price="{{ $goods->price }}" data-market-price="{{ $goods->market_price }}" data-img="{{ asset('uploads/'.$goods->img) }}" value="{{ $goods->id }}" data-name="{{ $goods->name }}" data-sub-name="{{ $goods->sub_name }}">更換套裝</option>
                                        @foreach($products as $item)
                                            <option data-price="{{ $item->price }}" data-market-price="{{ $item->market_price }}" data-img="{{ asset('uploads/'.$item->img) }}" value="{{ $item->id }}" data-name="{{ $item->name }}" data-sub-name="{{ $item->sub_name }}">{{ $item->sub_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{-- <div class="delivery">
                                <span>預計寄出日期：{{ date('Y-m-d',strtotime("+1 day")) }}</span>
                            </div> --}}
                            <div class="census">
                                <div class="compute">
                                    <dl>
                                        <dt>
                                            <p class="p-title">商品原價</p>
                                        </dt>
                                        <dd>
                                            NT$<span id="goods-price">{{ number_format(round($goods->market_price)) }}</span>
                                        </dd>
                                    </dl>

                                    <dl style="display: {{ $goods->market_price-$goods->price>0?"flex":'none' }}">
                                        <dt>
                                            <p class="p-title">套裝優惠</p>
                                        </dt>
                                        <dd>
                                            -NT$<span id="discount-price">{{ number_format(round($goods->market_price-$goods->price)) }}</span>
                                        </dd>
                                    </dl>

                                    <dl>
                                        <dt>
                                            <p class="p-title">運費<span class="grep">（訂購套裝可享受官方免費配送服務）</span></p>
                                        <!--  <p class="p-text">訂購套裝可享受官方免運費服務</p> -->
                                        </dt>
                                        <dd>
                                                <span id="freight-price">
                                                    @if($goods->price<$freight_where)
                                                        NT${{ round($freight_price) }}
                                                    @else
                                                        免費
                                                    @endif
                                                </span>
                                        </dd>
                                    </dl>
                                    <dl>
                                        <dt>
                                            <p class="p-title">税項</p>
                                        </dt>
                                        <dd>價格已包含</dd>
                                    </dl>
                                </div>

                                <div class="count">
                                    <dl>
                                        <dt>
                                            <p class="p-title">訂單總值</p>
                                        </dt>
                                        <dd style="color: #ff9b3e;font-size: 2rem">
                                            NT$<span id="order-price">
                                                @if($goods->price<$freight_where)
                                                    {{ number_format(round($goods->price+$freight_price)) }}
                                                @else
                                                    {{ number_format(round($goods->price)) }}
                                                @endif
                                                </span>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>

                    </div>
                    {{-- <div class="card">
                        <p class="title">訂單</p>
                        
                    </div> --}}
                    <div class="card">
                        <p class="title">配送訊息</p>
                        <div class="mater">
                            <div class="form-group">
                                <input class="form-control" data-validate="required:請問如何稱呼您" type="text" name="name" data-track-field="name" id="name">
                                <label class="shut" for="name">請問如何稱呼您</label>
                            </div>
                            <div class="form-group">
                                <input class="form-control" data-validate="required:請留下您收件時使用的電話號碼|mobile:請留下正確的電話號碼" type="tel" name="phone" data-track-field="phone" id="phone" maxlength="10" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                                <label class="shut" for="phone">請留下您收件時使用的電話號碼</label>
                            </div>
                            <div class="form-group">
                                <input class="form-control" data-validate="required:請預留您的電子信箱|email:請留下正確的電子信箱" type="email" name="email" data-track-field="email" id="email">
                                <label class="shut" for="email">請預留您的電子信箱</label>
                            </div>
                            <div class="form-group">
                                <p class="form-group-title">請選擇配送方式</p>
                                <div class="radio-box">

                                    <div class="form-radio">
                                        <input type="radio" id="order-type-1" name="order_type" data-track-field="order_type" value="1" checked>
                                        <label class="radio-label" for="order-type-1">
                                            <svg class="sevenicon" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:cc="http://creativecommons.org/ns#" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" xml:space="preserve" width="271.95663" height="264.24695" style="fill-rule:evenodd" viewBox="0 0 272.68729 257.44435" id="svg2" version="1.1" inkscape:version="0.48.1 " sodipodi:docname="AJAX.svg"><metadata id="metadata40"><rdf:RDF><cc:Work rdf:about=""><dc:format>image/svg+xml</dc:format><dc:type rdf:resource="http://purl.org/dc/dcmitype/StillImage"/></cc:Work></rdf:RDF></metadata><sodipodi:namedview pagecolor="#ffffff" bordercolor="#666666" borderopacity="1" objecttolerance="10" gridtolerance="10" guidetolerance="10" inkscape:pageopacity="0" inkscape:pageshadow="2" inkscape:window-width="1280" inkscape:window-height="1004" id="namedview38" showgrid="false" inkscape:zoom="1.1149061" inkscape:cx="244.05554" inkscape:cy="140.10103" inkscape:window-x="-8" inkscape:window-y="-8" inkscape:window-maximized="1" inkscape:current-layer="svg2" fit-margin-top="0" fit-margin-left="0" fit-margin-right="0" fit-margin-bottom="0"/>
                                                <defs id="defs4"><inkscape:perspective sodipodi:type="inkscape:persp3d" inkscape:vp_x="0 : 150 : 1" inkscape:vp_y="0 : 1000 : 0" inkscape:vp_z="300 : 150 : 1" inkscape:persp3d-origin="150 : 100 : 1" id="perspective42"/>
                                                <style type="text/css" id="style6">

                                                    .fil1 {fill:#008061;fill-rule:nonzero}
                                                    .fil3 {fill:#ED1B2D;fill-rule:nonzero}
                                                    .fil2 {fill:#F5821F;fill-rule:nonzero}
                                                    .fil0 {fill:white;fill-rule:nonzero}

                                                </style>

                                                </defs>
                                                <g id="g2862" transform="translate(-14.3907,-17.5756)"><polygon transform="matrix(0.89812868,0,0,0.89515596,15.772391,15.321752)" class="fil0" points="0,0 300.806,0 300.806,292.277 0,292.277 " id="polygon10" style="fill:#ffffff;fill-rule:nonzero"/><polygon class="fil1" points="14.3907,13.3111 287.078,13.3111 287.078,278.268 14.3907,278.268 " id="polygon12" style="fill:#008061;fill-rule:nonzero"/><path class="fil0" d="m 182.519,260.835 0,10.6078 -65.4031,0 0,-10.6276 -49.4907,0 c -4.83579,0 -8.77484,-5.05423 -8.77484,-11.2863 L 38.14156,40.0879 c 0,-6.23585 4.83579,-11.2942 10.803,-11.2942 l 199.991,0 c 5.92834,0 10.7641,5.05837 10.7641,11.2942 l -20.4749,209.441 c 0,6.23203 -3.93921,11.2863 -8.77484,11.2863 l -47.931,0.0198 z" id="path14" style="fill:#ffffff;fill-rule:nonzero" inkscape:connector-curvature="0"/><path class="fil2" d="m 74.5675,59.459 133.887,0 c -13.1823,4.94142 -53.313,32.1125 -63.3357,50.3806 l -70.4339,0 -0.117429,-50.3806 z" id="path16" style="fill:#f5821f;fill-rule:nonzero" inkscape:connector-curvature="0"/><path class="fil3" d="m 181.272,203.817 c -1.91105,18.2445 -1.98912,40.4624 -1.98912,63.8 l -58.0318,0 c 0,-23.3376 0.97512,-45.5555 2.8857,-63.8 l 57.1352,0 z" id="path18" style="fill:#ed1b2d;fill-rule:nonzero" inkscape:connector-curvature="0"/><polygon class="fil1" points="91.4939,177.453 91.4939,184.524 82.212,184.524 82.212,191.598 91.4939,191.598 91.4939,199.703 70.2776,199.703 70.2776,161.986 91.4939,161.986 91.4939,170.387 82.212,170.387 82.212,177.453 " id="polygon20" style="fill:#008061;fill-rule:nonzero"/><polygon class="fil1" points="140.828,177.453 140.828,184.524 131.586,184.524 131.586,191.598 140.828,191.598 140.828,199.703 119.652,199.703 119.652,161.986 140.828,161.986 140.828,170.387 131.586,170.387 131.586,177.453 " id="polygon22" style="fill:#008061;fill-rule:nonzero"/><polygon class="fil1" points="195.194,177.453 195.194,184.524 185.912,184.524 185.912,191.598 195.194,191.598 195.194,199.703 173.978,199.703 173.978,161.986 195.194,161.986 195.194,170.387 185.912,170.387 185.912,177.453 " id="polygon24" style="fill:#008061;fill-rule:nonzero"/><polygon class="fil1" points="107.406,161.986 107.406,191.598 116.687,191.598 116.687,199.703 95.4718,199.703 95.4718,161.986 " id="polygon26" style="fill:#008061;fill-rule:nonzero"/><polygon class="fil1" points="157.95,187.477 153.425,161.986 143.013,161.986 148.94,199.703 164.267,199.703 171.443,161.986 162.59,161.986 " id="polygon28" style="fill:#008061;fill-rule:nonzero"/><polygon class="fil1" points="200.225,161.986 211.692,161.986 211.692,199.699 200.225,199.699 " id="polygon30" style="fill:#008061;fill-rule:nonzero"/><path class="fil1" d="m 211.691,173.183 c 0.62443,-4.13025 6.47422,-3.02241 6.47422,-0.66331 l 0,27.183 11.5049,0 0,-31.9681 c 0,-6.77823 -9.5551,-9.50443 -17.901,-3.53721 l -0.0781,8.98564 z" id="path32" style="fill:#008061;fill-rule:nonzero" inkscape:connector-curvature="0"/><path class="fil3" d="m 186.654,156.682 c 1.16967,-14.4341 20.9425,-38.0054 40.0914,-44.1948 l 0,-55.6845 c -52.6885,23.572 -86.9308,59.2219 -94.3404,99.7387 l 54.2489,0.14053 z" id="path34" style="fill:#ed1b2d;fill-rule:nonzero" inkscape:connector-curvature="0"/></g>
                                            </svg>
                                            <span class="text">7-ELEVEN 統一超商<span>取貨付款</span></span>
                                        </label>
                                    </div>

                                    <div class="form-radio">
                                        <input type="radio" id="order-type-0" name="order_type" value="0">
                                        <label class="radio-label" for="order-type-0">
                                            <svg class="kulo" version="1.2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1548 1123" width="1548" height="1123">
                                                <title>e_ir2021_2_00_A3-1 (1)-pdf-svg (2)-svg</title>
                                                <style>
                                                    .s0 { fill: #fdd000 } 
                                                    .s1 { fill: #000000 } 
                                                </style>
                                                <path id="Path 77" class="s0" d="m0.4 562.1c0-373.3 343.4-561.6 775.9-561.6 432.6 0 771 188.3 771 561.6 0 371.7-338.4 556.7-771 560-432.5 4.9-775.9-188.3-775.9-560z"/>
                                                <path id="Path 78" class="s1" d="m1431.8 444.9l-262.6 168.4 87.6 355.2-3.4 3.3h-97.4l-4.9-1.7-156.9-189.9h-259.2l-156.8 189.9-5 1.7h-97.4l-3.3-3.3 59.5-237.9-52.9-62.8v61.2c0 89.2-72.6 160.2-160.1 161.8h-54.5l-3.3-3.3v-36.3l1.7-3.3 56.1-38v-56.2l-76-38-4.9-6.6v-34.6l3.3-3.3h77.6v-5c-6.6 0-19.8-1.7-33-5-19.8-8.2-38-21.4-49.6-39.6-13.2-19.8-19.8-42.9-19.8-74.3v-97.5h18.2l61.1 61.1h19.8 3.3v-3.3c0 0-23.1-11.5-41.3-51.2-9.9-26.4-16.5-56.1-16.5-92.5v-176.7h19.8l112.3 117.3h176.6l112.3-117.3h19.8v176.7c0 33.1-4.9 62.8-14.8 87.6-6.6 14.8-14.9 23.1-14.9 23.1v4.9h462.3l277.3-105.7 5 1.7 14.9 64.4z"/>
                                                <path id="Path 79" fill-rule="evenodd" class="s0" d="m0.4 562.1c0-373.3 343.4-561.6 775.9-561.6 432.6 0 771 188.3 771 561.6 0 371.7-338.4 556.7-771 560-432.5 4.9-775.9-188.3-775.9-560zm442.4-150.3c5-6.6 3.3-14.8-4.9-21.5-3.3-1.6-33.1-23.1-79.3-26.4-11.5-1.6-19.8 5-19.8 14.9 0 11.5 5 18.2 18.2 18.2 34.6 3.3 64.4 18.1 69.3 19.8 5 1.6 13.2 1.6 16.5-5zm-39.6 181.7c3.3-1.6 26.4-13.2 51.2-14.8 9.9-1.7 13.2-5 13.2-13.3 0-8.2-5-13.2-14.9-11.5-34.6 1.6-56.1 18.1-57.8 19.8-6.6 5-6.6 9.9-4.9 14.9 3.3 6.6 9.9 6.6 13.2 4.9zm-56.1-4.9c3.3-5 1.6-9.9-3.3-14.9-3.3-1.7-24.8-18.2-59.5-19.8-8.2-1.7-14.8 3.3-14.8 11.5 0 8.3 3.3 11.6 13.2 13.3 26.4 1.6 47.8 13.2 51.1 14.8 3.4 1.7 10 1.7 13.3-4.9zm189.8-171.8c5-1.7 34.7-16.5 69.4-19.8 13.2 0 18.1-6.7 18.1-18.2-1.6-9.9-8.2-16.5-19.8-14.9-46.2 3.3-77.6 24.8-80.9 26.4-8.2 6.7-8.2 14.9-4.9 21.5 4.9 6.6 13.2 6.6 18.1 5z"/>
                                                <path id="Path 80" class="s1" d="m1431.8 444.9l-262.6 168.4 87.6 355.2-3.4 3.3h-97.4l-4.9-1.7-156.9-189.9h-259.2l-156.8 189.9-5 1.7h-97.4l-3.3-3.3 59.5-237.9-52.9-62.8v61.2c0 89.2-72.6 160.2-160.1 161.8h-54.5l-3.3-3.3v-36.3l1.7-3.3 56.1-38v-56.2l-76-38-4.9-6.6v-34.6l3.3-3.3h77.6v-5c-6.6 0-19.8-1.7-33-5-19.8-8.2-38-21.4-49.6-39.6-13.2-19.8-19.8-42.9-19.8-74.3v-97.5h18.2l61.1 61.1h19.8 3.3v-3.3c0 0-23.1-11.5-41.3-51.2-9.9-26.4-16.5-56.1-16.5-92.5v-176.7h19.8l112.3 117.3h176.6l112.3-117.3h19.8v176.7c0 33.1-4.9 62.8-14.8 87.6-6.6 14.8-14.9 23.1-14.9 23.1v4.9h462.3l277.3-105.7 5 1.7 14.9 64.4z"/>
                                                <path id="Path 81" class="s0" d="m518.8 411.8c-3.3-6.6-3.3-14.8 4.9-21.5 3.3-1.6 34.7-23.1 80.9-26.4 11.6-1.6 18.2 5 19.8 14.9 0 11.5-4.9 18.2-18.1 18.2-34.7 3.3-64.4 18.1-69.4 19.8-4.9 1.6-13.2 1.6-18.1-5zm-92.5 5c-4.9-1.7-34.7-16.5-69.3-19.8-13.2 0-18.2-6.7-18.2-18.2 0-9.9 8.3-16.5 19.8-14.9 46.2 3.3 76 24.8 79.3 26.4 8.2 6.7 9.9 14.9 4.9 21.5-3.3 6.6-11.5 6.6-16.5 5zm-36.3 171.8c-1.7-5-1.7-9.9 4.9-14.9 1.7-1.7 23.2-18.2 57.8-19.8 9.9-1.7 14.9 3.3 14.9 11.5 0 8.3-3.3 11.6-13.2 13.3-24.8 1.6-47.9 13.2-51.2 14.8-3.3 1.7-9.9 1.7-13.2-4.9zm-56.2 4.9c-3.3-1.6-24.7-13.2-51.1-14.8-9.9-1.7-13.2-5-13.2-13.3 0-8.2 6.6-13.2 14.8-11.5 34.7 1.6 56.2 18.1 59.5 19.8 4.9 5 6.6 9.9 3.3 14.9-3.3 6.6-9.9 6.6-13.3 4.9z"/>
                                            </svg>
                                            <span class="text">黑貓宅急便<span>貨到付款</span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <p class="form-group-title" id="order-type-title">配送至門市</p>
                                <div class="form-select">
                                    <div class="select-box" id="load-1">
                                        <select name="city" data-track-field="city" id="city" data-validate="required:請選擇縣市">
                                            <option value="0">選擇縣市</option>
                                        </select>
                                    </div>

                                    <div class="select-box" id="load-2">
                                        <select name="county" data-track-field="county" id="county" data-validate="required:請選擇地區">
                                            <option value="0">選擇地區</option>
                                        </select>
                                    </div>

                                    <div class="select-box" id="load-3">
                                        <select name="street" data-track-field="street" id="street" data-validate="required:請選擇路段">
                                            <option value="0">選擇路段</option>
                                        </select>
                                    </div>

                                </div>

                                <div class="form-address" id="form-address-row">
                                    <input class="form-control" data-validate-condition="order_type:0" data-validate="required:請輸入詳細地址" type="text" name="address" data-track-field="address" id="address">
                                    <label class="shut" for="address">詳細地址</label>
                                </div>

                                <div class="form-store" id="form-store-row">
                                    <input type="hidden" name="store_name" id="store-name-input" value="">
                                    <input type="hidden" name="store_address" id="store-address-input" value="">
                                </div>
                            </div>


                            <div class="form-group">
                                <p class="form-group-title">訂單留言</p>
                                <textarea class="form-textarea" name="remarks" data-track-field="remarks" ></textarea>
                            </div>

                        </div>
                    </div>

                    <div class="others-sec">
                        <!-- <div class="others">
                            <p class="title">隱私保護說明：</p>
                            <div class="other-sub">
                                <p style="line-height: 1.5;">為促進環境保護，我們將採用<strong>紙盒全新包裝</strong>。包裝外無任何標示字樣及顧客個資，請放心選購。</p>
                                <svg version="1.1" class="packageicon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                viewBox="0 0 400 400" enable-background="new 0 0 400 400" xml:space="preserve">
                                    <g style="transform: translate(-380px,-560px);">
                                        <polygon fill="#CFB594" points="593.681,654.906 690.7,701.207 565.869,738.273 467.468,688.141 	"/>
                                        <polygon fill="#BCA286" points="690.126,833.889 565.641,878.104 565.641,737.836 690.7,701.207 	"/>
                                        <polygon fill="#F0DCC0" points="467.468,688.141 467.731,818.383 565.641,878.104 565.641,737.836 	"/>
                                        <polygon fill="#E0D3C2" points="503.856,706.543 631.332,672.873 644.787,679.277 518.163,713.838 	"/>
                                        <polygon fill="#E0D3C2" points="522.395,673.727 619.738,721.99 636.41,717.201 537.614,669.707 	"/>
                                        <polygon fill="#DAC4AE" points="619.738,721.99 636.41,717.201 636.41,755.25 620.336,762.26 	"/>
                                        <polygon fill="#F7E8D5" points="503.856,706.543 518.163,713.838 518.163,754.994 503.799,746.102 	"/>
                                    </g>
                                </svg>
                                <p style="line-height: 1.5;">為保障交易隱私安全，訂購時建議使用代稱。</br>商品送達後<strong>僅透過簡訊</strong>通知取貨，簡訊內容亦<strong>不會提及</strong>內容物及店家，<strong>絕不會致電打擾</strong>，請顧客儘快留意簡訊即可。</p>
                                <img src="/static/img/message.jpg" alt="" loading="lazy" decoding="async">
                            </div>
                        </div> -->
                        <div class="sales-sec">
                            <div class="sales-item">
                                <span class="sales-ico"><i class="iconfont">&#x100d2;</i></span>
                                <span class="sales-title">正品保證</span>
                                <!-- <span class="sales-sub">100%原裝正品煙油</span> -->
                            </div>
                            <div class="sales-item">
                                <span class="sales-ico">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="1" y="3" width="15" height="13" rx="2"/>
                                        <polyline points="16,8 20,8 23,11 23,16 16,16"/>
                                        <circle cx="5.5" cy="18.5" r="2.5"/>
                                        <circle cx="18.5" cy="18.5" r="2.5"/>
                                        <polyline points="6,11 8,13 11,9"/>
                                    </svg>
                                </span>
                                <span class="sales-title">套裝免運</span>
                                <!-- <span class="sales-sub">享受全台灣配送免運費</span> -->
                            </div>
                            <div class="sales-item">
                                <span class="sales-ico">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12,6 12,12 16,14"/>
                                    </svg>
                                </span>
                                <span class="sales-title">當天發貨</span>
                                <!-- <span class="sales-sub">每天18點寄出當天訂單</span> -->
                            </div>
                            <div class="sales-item">
                                <span class="sales-ico">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                                        <line x1="7" y1="7" x2="7.01" y2="7"/>
                                    </svg>
                                </span>
                                <span class="sales-title">套裝優惠</span>
                                <!-- <span class="sales-sub">滿3300元減150元</span> -->
                            </div>
                            <div class="sales-item">
                                <span class="sales-ico">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="1 4 1 10 7 10"/>
                                        <polyline points="23 20 23 14 17 14"/>
                                        <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                                    </svg>
                                </span>
                                <span class="sales-title">7天退換承諾</span>
                                <!-- <span class="sales-sub">產品質量問題免費退換</span> -->
                            </div>
                            <div class="sales-item">
                                <span class="sales-ico">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                        <polyline points="9,12 11,14 15,10"/>
                                    </svg>
                                </span>
                                <span class="sales-title">安全支付</span>
                                <!-- <span class="sales-sub">加密協議保護個資安全</span> -->
                            </div>
                            <div class="sales-item">
                                <span class="sales-ico">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                        <rect x="9" y="10" width="6" height="4" rx="0.5"/>
                                        <path d="M10 10V8a2 2 0 0 1 4 0v2"/>
                                    </svg>
                                </span>
                                <span class="sales-title">100%隱密包裝</span>
                                <!-- <span class="sales-sub">包裝絕無商品明細內容</span> -->
                            </div>
                            <div class="sales-item">
                                <span class="sales-ico">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/>
                                    </svg>
                                </span>
                                <span class="sales-title">好評回購</span>
                                <!-- <span class="sales-sub">20萬+買家回購評價</span> -->
                            </div>
                            <div class="sales-item">
                                <span class="sales-ico">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                        <path d="M9 12h6"/>
                                        <path d="M12 9v6"/>
                                    </svg>
                                </span>
                                <span class="sales-title">美國FDA認證</span>
                                <!-- <span class="sales-sub">高品質原料與生產工藝</span> -->
                            </div>
                        </div>
                    </div>



                </div>

            </div>
        </form>
    </div>
@endsection
