@extends('web::layout.layout')

@section('style')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('static/less/product.css') }}?ver={{ config('app.asset_version') }}"/>
@stop

@section('script')
    <script src="{{ asset('static/js/price-animator.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('static/a/js/jquery.easing.1.3.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('static/a/js/jquery.parallax-scroll.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script>
        var productCards = document.querySelectorAll('.product-main .product-card .product-title .product-sub-name');
        if (productCards.length) {
            if ('IntersectionObserver' in window) {
                var cardFocusObserver = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('sub-name-focus');
                        } else {
                            entry.target.classList.remove('sub-name-focus');
                        }
                    });
                }, {
                    root: null,
                    // 卡片进到视窗中间（上、下各 20%）时触发焦点状态
                    rootMargin: '-20% 0px -20% 0px',
                    threshold: 0
                });

                productCards.forEach(function(card) {
                    cardFocusObserver.observe(card);
                });
            } else {
                // 不支持 IntersectionObserver 时提供降级样式
                productCards.forEach(function(card) {
                    card.classList.add('sub-name-focus');
                });
            }
        }
    </script>
@stop

@section('content')
<main class="page-product">
    {{--
    @section('embed-banner')
        <div class="embed-banner wrapper column">
            <p class="en-title">Buy Online</p>
            <h1 class="embed-title">{!! app('cache.config')->get('page_product_title') !!}</h1>
            <div class="embed-desc">{!! str_replace(PHP_EOL,'<br>',app('cache.config')->get('page_product_desc')) !!}</div>
        </div>
    @stop
    --}}
    @include('web.widgets.head-banner')
    @include('web.widgets.breadcrumb', ['itemsHtml' => '<li class="breadcrumb__item">訂購羅氏鮮減肥藥療程組合</li>'])
    <section class="product-container" aria-label="產品列表" data-track-section-view data-track-section="product.list" data-track-section-label="產品列表">
        <h2 class="sr-only">羅氏鮮減肥藥療程訂購</h2>
        <ul class="product-main">
            @foreach($products as $key=>$goods)
                <li class="product-card" data-product-id="{{ $goods->id }}">
                    <div class="img-wrap">
                        <img src="{{ asset('uploads/'.$goods->img) }}?ver={{ config('app.asset_version') }}" alt="{{ $goods->name }}">
                    </div>
                    <!-- <p class="product-en-name">Xenical<sup>®</sup></p> -->

                    <h3 class="product-title">{{ $goods->name }}<strong class="product-sub-name">{{ $goods->sub_name }}</strong></h3>
                    @if($goods->label)
                        <ul class="tags">   
                            @foreach(explode('|',$goods->label) as $label)
                                <li class="tag-item">
                                    <span class="tick"><svg class="tickicon" viewBox="0 0 1024 1024"><use href="#icon-tickicon"></use></svg></span>
                                    <p class="tag-text">{{ $label }}</p>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <dl class="attr">
                        @foreach($goods->attr as $attr)
                            <dt class="attr-name">{{ $attr->name }}</dt>
                            <dd class="attr-value">{{ $attr->value }}</dd>
                        @endforeach
                    </dl>
                    <div class="product-bottom">
                        <div class="price-box" data-market-price="{{ $goods->market_price }}" data-price="{{ $goods->price }}">
                            @if($goods->discount_percent > 0)
                            <div class="mk-price-box">
                                <p class="mk-price"><span class="twd">NT$</span>{{ $goods->market_price }}</p>
                                <p class="discount">-<span class="descount-num">{{ $goods->discount_percent }}</span>%</p>
                            </div>
                            @else
                                <p class="discount-no">官方標準售價</p>
                            @endif
                            <p class="price"><span class="twd">NT$</span><span class="price-number">{{ $goods->price }}</span></p>
                        </div>
                        <a class="btn-ef1" href="{{ url('checkout/'.$goods->id) }}" data-observer="立即訂購-{{ $goods->name }}" data-track-section="product.list" data-track-name="product.list.checkout">{{ (int) $goods->id === 1 ? '立即訂購' : '免運訂購' }}<svg class="btn-icon buy-icon"><use href="#icon-buyicon"></use></svg></a>
                        <a class="goinfo" href="{{ url('product/'.$goods->id) }}" data-observer="了解更多-{{ $goods->name }}" data-track-section="product.list" data-track-name="product.list.detail">了解更多<svg class="btn-icon"><use href="#icon-arrowicon"></use></svg></a>
                    </div>
                    @include('web.widgets.qa', ['faqs' => $goods->faqs])
                </li>
            @endforeach
        </ul>
    </section>
</main>
@include('web.widgets.update-box')
@endsection
