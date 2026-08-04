@extends('mobile.layout')

@section('style')
    @parent
    <link rel="stylesheet" href="{{ asset('static/mobile/less/product.css') }}?ver={{ config('app.asset_version') }}">
@stop

@section('script')
    @parent
@stop
@section('breadcrumb')
    <ul class="breadcrumb">
        <li><a href="{{ url('/') }}">首頁</a></li>

        <li class="active">線上訂購</li>
    </ul>
@stop

@section('embed-banner')
    <div class="embed-banner">
        <h1 class="embed-title">{!! app('cache.config')->get('page_product_title') !!}</h1>
        <div class="embed-desc">{!! str_replace(PHP_EOL,'<br>',app('cache.config')->get('page_product_desc')) !!}</div>
    </div>
@stop

@section('content')
    <section class="product-container" data-track-section="product_list" data-track-section-view data-track-section-label="商品列表">
        <div class="wrapper">

            <div class="product-main">

                @foreach($products as $key=>$goods)

                    <div class="goods wow animate__animated animate__fadeInUp">
                        <div class="line"></div>
                        
                        <div class="ex-column">
                            <div class="img-wrap"><img src="{{ asset('uploads/'.$goods->img) }}?ver={{ config('app.asset_version') }}" alt="{{ $goods->name }}" loading="lazy" decoding="async"></div>
                            <div class="info">
                                <div class="info-boa">
                                    <div class="title">
                                        <h2 style="font-size: 1.5rem;">{{ $goods->name }}</h2>
                                        <p>{{ $goods->quantity }}{{ $goods->quantity == 1?"盒標準裝":"盒優惠套裝" }}</p>

                                    </div>
                                    <div class="tags">
                                        @if($goods->label)
                                            <p class="tags">
                                                @foreach(explode('|',$goods->label) as $label)
                                                    <span>{{ $label }}</span>
                                                @endforeach
                                            </p>
                                        @endif
                                    </div>
                                    {{-- @if($goods->attr)
                                        <div class="attr">
                                            @foreach($goods->attr as $attr)
                                                <p class="list">
                                                    <span class="attr-name">{{ $attr->name }}：</span>
                                                    <span class="attr-value">{{ $attr->value }}</span>
                                                </p>
                                            @endforeach
                                        </div>
                                    @endif --}}
                                    <div class="fix-price">
                                        <div class="price">
                                            <span class="now">${{ round($goods->price) }}</span>
                                            @if($goods->market_price-$goods->price > 0)
                                                <span class="discount deline">${{ $goods->market_price }}</span>
                                            @else
                                                <span class="discount">官方標準售價</span>
                                            @endif
                                        </div>

                                        <div class="btn">
                                            <a href="{{ url('checkout/'.$goods->id) }}" class="checkout" data-track-section="product_list" data-track-name="product.list.checkout" data-observer="立即訂購-{{ $goods->name }}">立即訂購</a>
                                            <a href="{{ url('product/'.$goods->id) }}" class="goinfo" data-track-section="product_list" data-track-name="product.list.detail" data-observer="查看詳情-{{ $goods->name }}">查看詳情</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        

                    </div>
                @endforeach



            </div>

        </div>
    </section>
@endsection
