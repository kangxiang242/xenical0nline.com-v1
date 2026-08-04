<!DOCTYPE html>
@php
    $needsWow = request()->is('/') || request()->is('product');
@endphp
<html lang="zh-TW" style="font-size: 62.5%">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="renderer" content="webkit">
    <meta http-equiv="content-language" content="zh-tw">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="format-detection" content="telephone=no">
    @if(app('cache.config')->get('google_verify_type') == 1)
        {!! app('cache.config')->get('google_verify_code') !!}
    @endif
    @if(isset($layout['seo']))
        <title>{{ isset($layout['seo'])?$layout['seo']->title:"" }}</title>
    @else
        @hasSection('title')
            <title>@yield('title')</title>
        @else
            <title>{{ isset($layout['seo'])?$layout['seo']->title:"" }}</title>
        @endif
    @endif

    @hasSection('keywords')
        <meta name="keywords" content="@yield('keywords')"/>
    @else
        <meta name="keywords" content="{{ isset($layout['seo'])?$layout['seo']->key_word:"" }}"/>
    @endif

    @hasSection('description')
        <meta name="description" content="@yield('description')"/>
    @else
        <meta name="description" content="{{ isset($layout['seo'])?$layout['seo']->description:"" }}"/>
    @endif

    <link rel="alternate" hreflang="zh-TW" href="{{ config('app.url') }}/{{ trim(request()->path(),'/') }}" />
    <link rel="canonical" href="{{ config('app.url') }}/{{ trim(request()->path(),'/') }}">

    <link rel="shortcut icon" href="{{ asset_upload(app('cache.config')->get('favicon'),'/favicon.ico') }}">
    @section('style')
        <link rel="stylesheet" type="text/css" href="{{ asset('static/css/style.css') }}?ver={{ config('app.asset_version') }}"/>
        @if(!is_googlebot())
        <link rel="stylesheet" href="{{ asset('static/font/iconfont.css') }}?ver={{ config('app.asset_version') }}">
        @endif
        <link rel="stylesheet" href="{{ asset('static/mobile/less/global.css') }}?ver={{ config('app.asset_version') }}">
        @if($needsWow)
        <link rel="stylesheet" type="text/css" href="{{ asset('static/wow/animate.min.css') }}?ver={{ config('app.asset_version') }}"/>
        @endif
    @show

    @php
        $trackingWebHost = parse_url(config('app.url'), PHP_URL_HOST);
        $trackingMobileHost = parse_url(config('app.m_url') ?: config('app.url'), PHP_URL_HOST);
    @endphp
    <script src="{{ asset('static/js/jquery.min.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script>
        window.__TRACKING_CONFIG__ = {
            webHost: @json($trackingWebHost),
            mobileHost: @json($trackingMobileHost),
            endpoint: '/observer/store',
            enabled: @json(!app()->environment('local')),
            debug: @json(app()->environment('local')),
            assetVersion: @json(config('app.asset_version')),
            pluginBase: @json(asset('static/js/tracker-plugins') . '/')
        };
    </script>
    @include('components.tracking-page')
    <script src="{{ asset('static/js/tracker.js') }}?ver={{ config('app.asset_version') }}" defer></script>
    <script src="{{ asset('static/js/observer.js') }}?ver={{ config('app.asset_version') }}" defer></script>
    @if($needsWow)
    <script src="{{ asset('static/wow/wow.min.js') }}?ver={{ config('app.asset_version') }}"></script>
    @endif
    <script>
        var clientWidth = document.documentElement.clientWidth;
        ;(function (doc, win, undefined) {
            var docEl = doc.documentElement,
                resizeEvt = 'orientationchange' in win? 'orientationchange' : 'resize',
                recalc = function () {
                    clientWidth = docEl.clientWidth;
                    if(docEl.clientWidth > 768){
                        clientWidth = 768

                    }
                    docEl.style.fontSize = clientWidth / 37.5 + 'px';
                };
            if (doc.addEventListener === undefined) return;
            win.addEventListener(resizeEvt, recalc, false);
            doc.addEventListener('DOMContentLoaded', recalc, false)
        })(document, window);
        if(clientWidth > 768){
            clientWidth = 768
        }
        document.documentElement.style.fontSize = clientWidth / 37.5 + 'px';
    </script>
    <script>
        document.addEventListener('dblclick', function (e) {
            e.preventDefault();
        }, { passive: false });
    </script>
    @if($needsWow)
    <script>
        new WOW({
            offset:50,
        }).init();
    </script>
    @endif
    <script>
        var is_ajax_get_cart = 0;
        var flash_data = '{!! session()->get('flash') !!}';
        if(flash_data){
            flash_data = JSON.parse('{!! session()->get('flash') !!}');
        }else{
            flash_data = false;
        }

        var province = [];

        var free_shipping_where = parseInt("{{ \App\Services\ConfigService::get('freight_where',0) }}");
        var free_shipping_freight = parseInt("{{ \App\Services\ConfigService::get('freight',0) }}");

    </script>


</head>
<body>

@section('header')
    <header>
        <div class="logo-wrap">
            <a href="{{ url('/') }}">
                <img class="logo-img" src="{{ asset('static/img/m.logo2.webp') }}?ver={{ config('app.asset_version') }}" alt="logo" decoding="async">
            </a>
        </div>
        <div class="right-wrap">
       {{--     <div class="online"><a href="{{ url('product') }}">線上訂購</a></div>--}}
            <div class="menu"><a class="show-menu" href="javascript:;" data-track-section="nav" data-track-name="nav.menu.open" data-observer="側欄-打開"><i class="iconfont">&#xe62c;</i></a></div>
        </div>
    </header>
    <div class="online-buy">
        <a href="{{ url('product') }}" data-track-section="header" data-track-name="header.order_btn" data-observer="頂部-線上訂購"><i class="iconfont">&#xe811;</i>線上訂購</a>
    </div>
@show

@section('menu')
    <section class="menu-section">
        <div class="menu-head">
            <a href="javascript:;" class="close-menu" data-track-section="nav" data-track-name="nav.menu.close" data-observer="側欄-關閉"><i class="iconfont">&#xe62f;</i></a>
        </div>
        <ul class="menu-list">
            <li class="menu-item">
                <ul class="menu-dropdown">
                    <li>
                        <a href="{{ url('/') }}" data-track-section="nav.drawer" data-track-name="nav.drawer.home" data-observer="側欄-首頁">首頁 <i class="iconfont">&#xe775;</i></a>
                    </li>

                </ul>
            </li>
            <li class="menu-item">
                <a href="javascript:;">Sale</a>
                <ul class="menu-dropdown">
                    <li>
                        <a href="{{ url('product') }}" data-track-section="nav.drawer" data-track-name="nav.drawer.product" data-observer="側欄-線上訂購">羅氏鮮網路訂購<i class="iconfont">&#xe775;</i></a>
                    </li>
                    <li>
                        <a href="{{ url('guide') }}" data-track-section="nav.drawer" data-track-name="nav.drawer.guide" data-observer="側欄-購前須知">購前須知<i class="iconfont">&#xe775;</i></a>
                    </li>
                    <li>
                        <a href="{{ url('payment-delivery') }}" data-track-section="nav.drawer" data-track-name="nav.drawer.payment" data-observer="側欄-付款與配送">付款與配送<i class="iconfont">&#xe775;</i></a>
                    </li>
                    <li>
                        <a href="{{ url('after-sales') }}" data-track-section="nav.drawer" data-track-name="nav.drawer.after_sales" data-observer="側欄-售後服務">售後服務<i class="iconfont">&#xe775;</i></a>
                    </li>
                </ul>
            </li>
            <li class="menu-item">
                <a href="javascript:;">About</a>
                <ul class="menu-dropdown">
                    <li>
                        <a href="{{ url('about') }}" data-track-section="nav.drawer" data-track-name="nav.drawer.about" data-observer="側欄-認識羅氏鮮">認識羅氏鮮<i class="iconfont">&#xe775;</i></a>
                    </li>
                </ul>
            </li>

            <li class="menu-item">
                <a href="javascript:;">Q&A</a>
                <ul class="menu-dropdown">
                    <li>
                        <a href="{{ url('faq') }}" data-track-section="nav.drawer" data-track-name="nav.drawer.faq" data-observer="側欄-營養師解答">營養師解答<i class="iconfont">&#xe775;</i></a>
                    </li>
                </ul>
            </li>

            <li class="menu-item">
                <a href="javascript:;">Articles</a>
                <ul class="menu-dropdown">
                    <li>
                        <a href="{{ url('news') }}" data-track-section="nav.drawer" data-track-name="nav.drawer.news" data-observer="側欄-瘦身專欄">瘦身專欄<i class="iconfont">&#xe775;</i></a>
                    </li>
                </ul>
            </li>

            <li class="menu-item">
                <a href="javascript:;">Service</a>
                <ul class="menu-dropdown">
                    <li>
                        <a href="{{ url('check') }}" data-track-section="nav.drawer" data-track-name="nav.drawer.check" data-observer="側欄-訂單追蹤">訂單追蹤<i class="iconfont">&#xe775;</i></a>
                    </li>
                    <li>
                        <a href="{{ url('message') }}" data-track-section="nav.drawer" data-track-name="nav.drawer.message" data-observer="側欄-取得協助">取得協助<i class="iconfont">&#xe775;</i></a>
                    </li>
                    <li>
                        <a href="{{ url('compute') }}" data-track-section="nav.drawer" data-track-name="nav.drawer.compute" data-observer="側欄-瘦身計算機">瘦身計算機<i class="iconfont">&#xe775;</i></a>
                    </li>
                </ul>
            </li>

        </ul>
    </section>
@show

<main>
@section('banner')
    @if($layout['banners'] && !$layout['banners']->isEmpty())
        <section class="banner-section">
            <div class="banner-main">
                @foreach($layout['banners'] as $key=>$item)
                    @if($item->m_img)
                        <a href="{{ $item->href?url($item->href):"javascript:;" }}"><img src="{{ asset_upload($item->m_img) }}" alt="{{ $item->alt }}" decoding="async"></a>
                    @endif
                @endforeach
            </div>
            @yield('embed-banner')
        </section>
    @endif
@show

@section('breadcrumb')

@show

@yield('content')



<footer>
    {{--<div class="head">
        <a href="{{ url('/') }}">
            <div class="logo-wrap">
                <div class="place">
                    <div class="compose">
                        <img class="fra-1" src="{{ asset('static/img/lg/fraw-1.png') }}" alt="logo" loading="lazy" decoding="async">
                        <img class="fra-2" src="{{ asset('static/img/lg/fraw-2.png') }}" alt="logo" loading="lazy" decoding="async">
                        <img class="fra-3"  src="{{ asset('static/img/lg/fraw-3.png') }}" alt="logo" loading="lazy" decoding="async">
                    </div>
                    <div class="intact">
                        <img class="xenical-logo" src="{{ asset('static/img/lg/xenical-2.png') }}" alt="xenical" loading="lazy" decoding="async">

                    </div>

                </div>
                <p class="text white">全球領先健康減肥藥</p>
            </div>
        </a>
    </div>--}}

    <div class="main">
        <div class="menu-column">

            <div class="menu">
                <p class="title">Sale</p>
                <ul class="nav">
                    <li><a href="{{ url('product') }}" data-track-section="footer.sale" data-track-name="footer.sale.order" data-observer="底部-線上訂購">羅氏鮮網路訂購</a></li>
                    <li><a href="{{ url('guide') }}" data-track-section="footer.sale" data-track-name="footer.sale.guide" data-observer="底部-購前須知">購前須知</a></li>
                    <li><a href="{{ url('payment-delivery') }}" data-track-section="footer.sale" data-track-name="footer.sale.payment" data-observer="底部-付款與配送">付款與配送</a></li>
                    <li><a href="{{ url('after-sales') }}" data-track-section="footer.sale" data-track-name="footer.sale.after_sales" data-observer="底部-售後服務">售後服務</a></li>
                </ul>
            </div>
            <div class="menu">
                <p class="title">About</p>
                <ul class="nav">
                    <li><a href="{{ url('about') }}" data-track-section="footer.about" data-track-name="footer.about.link" data-observer="底部-認識羅氏鮮">認識羅氏鮮</a></li>
                </ul>
            </div>
            <div class="menu">
                <p class="title">Q&A</p>
                <ul class="nav">
                    <li><a href="{{ url('faq') }}" data-track-section="footer.qa" data-track-name="footer.qa.faq" data-observer="底部-營養師解答">營養師解答</a></li>
                </ul>
            </div>
            <div class="menu">
                <p class="title">Articles</p>
                <ul class="nav">
                    <li><a href="{{ url('news') }}" data-track-section="footer.articles" data-track-name="footer.articles.news" data-observer="底部-瘦身專欄">瘦身專欄</a></li>
                </ul>
            </div>
            <div class="menu">
                <p class="title">Service</p>
                <ul class="nav">
                    <li><a href="{{ url('check') }}" data-track-section="footer.service" data-track-name="footer.service.check" data-observer="底部-訂單追蹤">訂單追蹤</a></li>
                    <li><a href="{{ url('message') }}" data-track-section="footer.service" data-track-name="footer.service.message" data-observer="底部-取得協助">取得協助</a></li>
                    <li><a href="{{ url('compute') }}" data-track-section="footer.service" data-track-name="footer.service.compute" data-observer="底部-瘦身計算機">瘦身計算機</a></li>
                </ul>
            </div>
        </div>

        <div class="contact-column">
            <div class="topic">
                <div class="item">
                    <a href="{{ url('product') }}" data-track-section="footer.contact" data-track-name="footer.contact.order" data-observer="底部-線上訂購">
                        <div class="col">
                            <div class="icon"><i class="iconfont">&#xe64f;</i></div>
                            <div class="text">
                                <p class="en">Buy Online</p>
                                <p class="cn"><span>網路訂購</span></p>
                            </div>
                            <div class="arrow-right"><i class="iconfont">&#xe613;</i></div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="address">
                {!! str_replace(PHP_EOL,'<br/>',app('cache.config')->get('foot_text')) !!}
            </div>
        </div>

        <div class="description">
            <div class="partner">
                <div class="icon"><img  style="width: 12.6rem" src="{{ asset('static/img/fdausa.webp') }}" alt="fda-usa" loading="lazy" decoding="async"></div>
                <div class="icon"><img style="width: 15.2rem" src="{{ asset('static/img/ema.webp') }}" alt="ema" loading="lazy" decoding="async"></div>
                <!-- <div class="icon"><img  style="width: 14.5rem" src="{{ asset('static/img/fdataiwan.png') }}" alt="台湾fda" loading="lazy" decoding="async"></div> -->
                <div class="icon"><img  style="width: 5rem" src="{{ asset('static/img/ROCHE.webp') }}" alt="ROCHE" loading="lazy" decoding="async"></div>
                <div class="icon"><img  style="width: 12rem" src="{{ asset('static/img/CHEPLA.webp') }}" alt="CHEPLA" loading="lazy" decoding="async"></div>
                <!-- <div class="icon"><img  style="width: 12.2rem" src="{{ asset('static/img/heimao.png') }}" alt="黑猫宅急便" loading="lazy" decoding="async"></div>
                <div class="icon"><img  style="width: 2.6rem" src="{{ asset('static/img/7-11.png') }}" alt="7-11" loading="lazy" decoding="async"></div> -->
                <div class="icon"><img style="width: 5.2rem" src="{{ asset('static/img/ssl.webp') }}" alt="ssl" loading="lazy" decoding="async"></div>
            </div>
            <p class="copyright">{!! app('cache.config')->get('copyright') !!}</p>
        </div>
    </div>
</footer>


</main>
</body>


@section('script')
    {!! app('cache.config')->get('google_ga') !!}
    <script>
        $('.show-menu').click(function () {
            $('.menu-section').addClass('-show');
            $('body').append('<div class="shade"></div>');
            $('body').addClass('overflow-hidden')
        });
        $('.close-menu').click(function(){
            $('.menu-section').removeClass('-show');
            $('.shade').remove();
            $('body').removeClass('overflow-hidden')
        });

        $('body').on('click','.shade',function(){
            if (window.XenicalTracker) {
                XenicalTracker.track('click', 'nav.menu.close_shade', { section: 'nav', label: '側欄-遮罩關閉', explain: '側欄-遮罩關閉' });
            }
            $('.menu-section').removeClass('-show');
            $('.shade').remove();
            $('body').removeClass('overflow-hidden')
        });
    </script>

@show
</html>
