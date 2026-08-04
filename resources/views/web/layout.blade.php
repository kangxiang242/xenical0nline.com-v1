<!DOCTYPE html>
@php
    $needsSwiper = request()->is('/');
    $needsWow = request()->is('/') || request()->is('product');
@endphp
<html lang="zh-TW">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="chrome=1,IE=edge">
    <meta http-equiv="content-language" content="zh-tw">
    <meta name="format-detection" content="telephone=no" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
    <link rel="alternate" hreflang="zh-TW" href="{{ url()->current() }}" />
    <link rel="canonical" href="{{ url()->current() }}">
    @if(config('app.m_url'))
        <link rel="alternate" media="only screen and (max-width: 640px)" href="{{ config('app.m_url') }}/{{ trim(request()->path(),'/') }}">
    @endif
    <link rel="shortcut icon" href="{{ \App\Services\ConfigService::get('favicon')?asset('uploads/'.\App\Services\ConfigService::get('favicon')):'/favicon.ico' }}">

    {{-- Open Graph --}}
    <meta property="og:site_name" content="{{ config('app.name') }}" />
    <meta property="og:title" content="@yield('og_title', $layout['seo']->title ?? '')" />
    <meta property="og:description" content="@yield('og_description', $layout['seo']->description ?? '')" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="@yield('og_type', 'website')" />
    <meta property="og:locale" content="zh_TW" />
    @section('og_image')
    @show
    @if(!empty($layout['seo']->img))
    <meta property="og:image" content="{{ asset('uploads/' . $layout['seo']->img) }}" />
    @endif

    {{-- JSON-LD Structured Data --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@type": "WebSite",
        "name": "{{ config('app.name') }}",
        "url": "{{ url('/') }}",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "{{ url('/') }}/search?q={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>
    @yield('jsonld')
    @section('style')
        <link rel="stylesheet" type="text/css" href="{{ asset('static/css/style.css') }}?ver={{ config('app.asset_version') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('static/css/common.css') }}?ver={{ config('app.asset_version') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('static/less/global.css') }}?ver={{ config('app.asset_version') }}"/>
        @if(!is_googlebot())
        <link rel="stylesheet" href="{{ asset('static/font/iconfont.css') }}?ver={{ config('app.asset_version') }}">
        @endif
        @if($needsSwiper)
        <link rel="stylesheet" href="{{ asset('static/swiper4/swiper.min.css') }}?ver={{ config('app.asset_version') }}">
        @endif
        @if($needsWow)
        <link rel="stylesheet" type="text/css" href="{{ asset('static/wow/animate.min.css') }}?ver={{ config('app.asset_version') }}"/>
        @endif
    @show

    <style>html{--color-red:red}body.-ajax .o-loading__ajax{opacity:.5}body.-loading #wrapper,body:not(.-ajax) .o-loading__ajax{opacity:0}body.-loading .o-loading__content{opacity:1}body.-loading.page-index .o-loading__box.-start:before{-webkit-animation:marginLeftIn .5s .5s forwards;animation:marginLeftIn .5s .5s forwards}body.-loading.page-index .o-loading__box.-main:before{-webkit-animation:marginRightIn .5s 1s forwards;animation:marginRightIn .5s 1s forwards}body.-loading.page-index .o-loading__box.-cover:before{-webkit-animation:marginRightIn .5s 1.5s forwards;animation:marginRightIn .5s 1.5s forwards}body:not(.-loading) .o-loading__content:before{margin-left:0}body:not(.-loading).page-index .o-loading__box.-cover:before{margin-right:100vw}.o-loading{width:100vw;height:100vh;position:fixed;top:0;left:0;z-index:100000;pointer-events:none}.page-index .o-loading__box.-start{opacity:1;visibility:visible}.page-index .o-loading__box.-start:before{margin-left:0}.page-index .o-loading__box.-main:before{margin-right:0}.page-index .o-loading__box.-cover{opacity:1;visibility:visible}.page-index .o-loading__box.-cover:before{margin-right:0}.o-loading__ajax{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-align:center;-ms-flex-align:center;align-items:center;-webkit-box-pack:center;-ms-flex-pack:center;justify-content:center;width:100%;height:100%;background:#fff;opacity:.5;-webkit-transition:opacity .3s;transition:opacity .3s}.o-loading__ajax span{width:80px;height:80px;border-radius:50%;border-left:3px solid #ec7021;-webkit-animation:rotate 2s linear infinite;animation:rotate 2s linear infinite}@-webkit-keyframes rotate{to{-webkit-transform:rotate(0);transform:rotate(0)}0%{-webkit-transform:rotate(-1turn);transform:rotate(-1turn)}}@keyframes rotate{to{-webkit-transform:rotate(0);transform:rotate(0)}0%{-webkit-transform:rotate(-1turn);transform:rotate(-1turn)}}.o-loading__content{width:auto;height:100%;position:absolute;top:0;right:0;display:block;overflow:hidden;background:#fff;opacity:0;-webkit-transition:opacity .6s ease 1s;transition:opacity .6s ease 1s}.o-loading__content:before{width:auto;height:100%;content:"";position:relative;display:block;margin-left:100vw;-webkit-transition:margin-left .5s cubic-bezier(.9,0,.1,1) 0s;transition:margin-left .5s cubic-bezier(.9,0,.1,1) 0s}.o-loading__content-frame{width:100vw;height:100%;position:absolute;top:0;right:0;display:block;z-index:1}.o-loading__content-frame-bg{width:100%;height:100%;position:relative;z-index:5}.o-loading__box{width:auto;height:100%;position:absolute;top:0;left:0;display:block;overflow:hidden}.o-loading__box.-start{right:0;left:auto;opacity:0;visibility:hidden}.o-loading__box.-start:before{margin-left:0}.o-loading__box.-main:before{margin-right:100vw}.o-loading__box.-cover{opacity:0;visibility:hidden}.o-loading__box:before{width:auto;height:100%;content:"";position:relative;display:block}.o-loading__logo{width:100%;height:100%;display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-align:center;-ms-flex-align:center;align-items:center;-webkit-box-pack:center;-ms-flex-pack:center;justify-content:center;padding:20px}.o-loading__logo svg{display:block;max-width:100%;max-height:100%}@media (max-width:767px){.o-loading__logo svg{width:176px;height:auto}}.o-loading__cover,.o-loading__main,.o-loading__start{width:100vw;height:100%;position:absolute;top:0;left:0;display:block}.o-loading__cover:before,.o-loading__main:before,.o-loading__start:before{width:100%;height:100%;content:"";position:absolute;top:0;left:0;display:block;background-color:#ff893d;z-index:-1}.o-loading__main:before{background-color:#fff}@-webkit-keyframes marginLeftIn{0%{margin-left:0}to{margin-left:100vw}}@keyframes marginLeftIn{0%{margin-left:0}to{margin-left:100vw}}@-webkit-keyframes marginLeftOut{0%{margin-left:100vw}to{margin-left:0}}@keyframes marginLeftOut{0%{margin-left:100vw}to{margin-left:0}}@-webkit-keyframes marginRightIn{0%{margin-right:0}to{margin-right:100vw}}@keyframes marginRightIn{0%{margin-right:0}to{margin-right:100vw}}@-webkit-keyframes marginRightOut{0%{margin-right:100vw}to{margin-right:0}}@keyframes marginRightOut{0%{margin-right:100vw}to{margin-right:0}}</style>
    <style>

        .o-three-line__static{
            position: absolute;
            z-index: 999999;
            animation-name: line__static_effect;
            animation-duration: 2s;
            animation-timing-function: linear;

            animation-iteration-count: infinite;
            animation-direction: alternate;
            animation-play-state: running;
            /* Safari 与 Chrome: */
            -webkit-animation-name: line__static_effect;
            -webkit-animation-duration: 2s;
            -webkit-animation-timing-function: linear;
            -webkit-animation-iteration-count: infinite;
            -webkit-animation-direction: alternate;
            -webkit-animation-play-state: running;
            left: 50%;
            top: 0;
        }
        @keyframes line__static_effect
        {
            from {
                transform: translateX(-50%)scale(1.5)rotateY(0);
            }
            to {
                transform: translateX(-50%)scale(1.5)rotateY(148deg);
            }
        }

        @-webkit-keyframes line__static_effect /* Safari 与 Chrome */
        {
            from {
                transform: translateX(-50%)scale(1.5)rotateY(0);
            }
            to {
                transform: translateX(-50%)scale(1.5)rotateY(148deg);
            }
        }
    </style>
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
    <script>
        new WOW({
            offset:150,
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
    <!-- Hotjar Tracking Code for https://www.xenicalofficial.com -->
    <script>
        (function(h,o,t,j,a,r){
            h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
            h._hjSettings={hjid:3344599,hjsv:6};
            a=o.getElementsByTagName('head')[0];
            r=o.createElement('script');r.async=1;
            r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
            a.appendChild(r);
        })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
    </script>
</head>
<body class=" {{ request()->is('/')?"page-index":"" }} ">
{{--<div class="o-loading">
    <div class="o-loading__content">
        <div class="o-loading__content-frame">
            <div class="o-loading__content-frame-bg"></div>
            <div class="o-loading__box -start">
                <div class="o-loading__start"></div>
            </div>
            <div class="o-loading__box -main">
                <div class="o-loading__main">
                    <div class="o-loading__logo">
                        <svg xmlns="http://www.w3.org/2000/svg" width="345" height="59" viewBox="0 0 345 59"><path d="M151.435 48.371l2.268 5.672 2.311-5.672h1.451l-3.375 7.925c-.372.863-.84 1.496-1.4 1.897-.56.401-1.25.67-2.074.807l-.317-1.134c.68-.155 1.195-.356 1.545-.604.35-.246.627-.619.84-1.111l.257-.61-3.042-7.17h1.536zm-6.016 5.584v1.192c0 .455-.076.869-.231 1.244-.153.372-.41.742-.775 1.11l-.617-.508c.403-.445.632-.878.689-1.294h-.789v-1.744h1.723zm106.013-7.923v2.34h1.996v1.134h-1.996v3.679c0 .503.108.86.322 1.067.217.21.515.315.9.315.247 0 .5-.042.76-.118v1.192a3.36 3.36 0 01-1.08.161c-.745 0-1.323-.213-1.734-.64-.413-.425-.62-1.048-.62-1.861v-3.795h-1.405v-1.134h1.406v-1.831l1.45-.509zm-134.713 0v2.34h1.997v1.134h-1.997v3.679c0 .503.109.86.323 1.067.217.21.515.315.9.315.246 0 .5-.042.76-.118v1.192a3.36 3.36 0 01-1.08.161c-.745 0-1.324-.213-1.734-.64-.413-.425-.62-1.048-.62-1.861v-3.795h-1.405v-1.134h1.406v-1.831l1.45-.509zm121.062 2.167c.974 0 1.743.318 2.303.958.56.641.84 1.527.84 2.66v.247h-5.168c.047 1.717.817 2.576 2.31 2.576.834 0 1.595-.278 2.283-.83l.446 1.047c-.327.29-.74.52-1.243.689a4.643 4.643 0 01-1.514.256c-1.158 0-2.068-.337-2.728-1.011-.66-.673-.989-1.598-.989-2.77 0-.746.145-1.41.436-1.991a3.22 3.22 0 011.228-1.351c.527-.32 1.126-.48 1.796-.48zm-127.238 0c.517 0 1.007.08 1.473.246.462.165.859.397 1.183.697l-.445 1.032c-.699-.56-1.434-.842-2.211-.842-.468 0-.832.09-1.091.27a.857.857 0 00-.388.746c0 .253.083.458.247.613.161.153.42.275.774.364l1.235.304c.688.155 1.199.4 1.528.732.331.336.494.77.494 1.302 0 .66-.26 1.181-.782 1.564-.52.383-1.23.575-2.131.575-1.263 0-2.258-.31-2.986-.93l.43-1.062a4.152 4.152 0 002.599.873c.498 0 .876-.085 1.136-.255a.815.815 0 00.386-.72c0-.262-.088-.467-.266-.618-.178-.151-.476-.28-.897-.385l-1.205-.292c-.634-.134-1.114-.374-1.443-.718-.331-.345-.495-.774-.495-1.287 0-.66.26-1.194.781-1.6.521-.407 1.214-.61 2.074-.61zm71.156 0c.507 0 .996.084 1.466.254.467.17.845.4 1.134.689l-.446 1.064c-.678-.545-1.363-.816-2.053-.816-.7 0-1.243.23-1.637.69-.393.46-.589 1.108-.589 1.94 0 .834.196 1.477.589 1.928.394.45.938.675 1.637.675.709 0 1.393-.271 2.053-.813l.446 1.061c-.307.292-.697.517-1.171.683a4.461 4.461 0 01-1.486.248c-.727 0-1.36-.157-1.902-.465-.54-.31-.956-.75-1.243-1.316-.286-.567-.429-1.23-.429-1.984 0-.758.15-1.426.452-2.007.3-.582.725-1.033 1.27-1.351.544-.321 1.182-.48 1.91-.48zm-42.467 0c.975 0 1.743.318 2.303.958.56.641.84 1.527.84 2.66v.247h-5.168c.047 1.717.818 2.576 2.312 2.576.833 0 1.593-.278 2.282-.83l.445 1.047c-.327.29-.74.52-1.242.689a4.647 4.647 0 01-1.514.256c-1.159 0-2.068-.337-2.728-1.011-.66-.673-.989-1.598-.989-2.77 0-.746.145-1.41.435-1.991a3.226 3.226 0 011.228-1.351c.528-.32 1.126-.48 1.796-.48zm-38.208.173v4.479c0 .6.125 1.043.368 1.328.243.288.615.43 1.111.43.595 0 1.071-.194 1.429-.58.36-.388.54-.903.54-1.542v-4.115h1.45V55.7h-1.408v-1.223a2.424 2.424 0 01-.946.983 2.722 2.722 0 01-1.365.341c-1.743 0-2.613-.978-2.613-2.936v-4.493h1.434zm66.906 0v4.479c0 .6.125 1.043.368 1.328.243.288.615.43 1.111.43.595 0 1.071-.194 1.429-.58.36-.388.54-.903.54-1.542v-4.115h1.45V55.7h-1.408v-1.223a2.424 2.424 0 01-.946.983 2.722 2.722 0 01-1.365.341c-1.743 0-2.613-.978-2.613-2.936v-4.493h1.434zm20.373-.174c.554 0 1.04.118 1.451.356.41.238.725.575.948 1.01v-1.192h1.435V55.7h-1.435v-1.252a2.241 2.241 0 01-.94 1.004c-.417.234-.905.35-1.459.35-.641 0-1.203-.156-1.686-.466-.484-.31-.86-.75-1.128-1.316-.267-.567-.4-1.229-.4-1.984 0-.757.135-1.426.409-2.007.271-.582.651-1.033 1.14-1.351a2.958 2.958 0 011.665-.48zm33.267 0c.553 0 1.04.118 1.45.356.411.238.726.575.949 1.01v-1.192h1.434V55.7h-1.434v-1.252a2.252 2.252 0 01-.94 1.004c-.417.234-.906.35-1.46.35-.64 0-1.203-.156-1.685-.466-.484-.31-.86-.75-1.128-1.316-.268-.567-.4-1.229-.4-1.984 0-.757.134-1.426.408-2.007.272-.582.652-1.033 1.14-1.351a2.961 2.961 0 011.666-.48zm-60.114 0c.699 0 1.314.154 1.851.465.536.31.95.753 1.243 1.33.292.577.437 1.248.437 2.013 0 .766-.145 1.436-.437 2.007a3.151 3.151 0 01-1.243 1.322c-.537.31-1.152.466-1.85.466-.71 0-1.333-.155-1.869-.466-.535-.31-.95-.75-1.242-1.322-.29-.57-.435-1.241-.435-2.007 0-.765.145-1.436.435-2.013a3.135 3.135 0 011.242-1.33c.536-.31 1.159-.466 1.868-.466zm-67.048-2.85v1.25h-3.46V55.7h-1.493v-9.102h-3.462v-1.25h8.415zm163.556 8.608V55.7h-1.722v-1.744h1.722zM247.57 48.37v7.33h-1.437v-7.33h1.437zM228.82 45v6.687l3.216-3.3h1.795l-3.46 3.517 3.76 3.795h-1.851l-3.46-3.46v3.46h-1.436V45h1.436zm-30.854 3.198c1.733 0 2.597.979 2.597 2.936v4.564h-1.449v-4.477c0-.64-.122-1.105-.366-1.395-.243-.291-.635-.436-1.17-.436-.613 0-1.102.194-1.472.581-.37.387-.553.907-.553 1.556v4.171h-1.437v-5.261c0-.766-.037-1.455-.112-2.065h1.348l.13 1.264c.228-.463.563-.821.996-1.068.436-.246.932-.37 1.488-.37zm-99.986 0c.223 0 .452.033.691.102l-.03 1.336a2.431 2.431 0 00-.846-.145c-.66 0-1.155.203-1.486.604-.329.402-.495.909-.495 1.52v4.083h-1.438v-5.261c0-.766-.037-1.455-.112-2.065h1.348l.129 1.324c.192-.486.49-.856.89-1.113a2.458 2.458 0 011.35-.385zm33.87 0c1.606 0 2.411.979 2.411 2.936v4.564h-1.449v-4.491c0-.63-.108-1.09-.324-1.38-.215-.292-.562-.437-1.04-.437-.556 0-.996.194-1.314.581-.321.387-.48.917-.48 1.585v4.142h-1.451v-4.491c0-.63-.109-1.09-.323-1.38-.217-.292-.564-.437-1.04-.437-.558 0-.995.194-1.322.581-.327.387-.489.917-.489 1.585v4.142h-1.436v-5.261c0-.766-.039-1.455-.115-2.065h1.349l.13 1.22c.21-.444.514-.79.904-1.032.394-.24.852-.362 1.38-.362 1.119 0 1.85.47 2.196 1.411.22-.436.543-.782.968-1.032a2.779 2.779 0 011.445-.379zm82.585 0c1.606 0 2.41.979 2.41 2.936v4.564h-1.448v-4.491c0-.63-.108-1.09-.325-1.38-.215-.292-.562-.437-1.04-.437-.556 0-.995.194-1.314.581-.32.387-.48.917-.48 1.585v4.142h-1.45v-4.491c0-.63-.11-1.09-.324-1.38-.216-.292-.564-.437-1.04-.437-.558 0-.995.194-1.322.581-.327.387-.488.917-.488 1.585v4.142h-1.437v-5.261c0-.766-.039-1.455-.114-2.065h1.348l.131 1.22c.209-.444.513-.79.903-1.032.395-.24.853-.362 1.38-.362 1.12 0 1.851.47 2.196 1.411.221-.436.544-.782.969-1.032a2.776 2.776 0 011.445-.379zm7.465 1.177c-.65 0-1.16.236-1.53.706-.369.47-.554 1.121-.554 1.955 0 .823.185 1.463.553 1.918.37.457.885.685 1.545.685.65 0 1.155-.23 1.514-.691.36-.462.54-1.107.54-1.941 0-.844-.18-1.492-.54-1.947-.36-.457-.868-.685-1.528-.685zm-60.445 0c-.66 0-1.173.229-1.537.684-.363.455-.547 1.105-.547 1.947 0 .863.18 1.517.54 1.964.359.445.87.668 1.53.668.668 0 1.18-.223 1.534-.668.356-.447.534-1.101.534-1.964 0-.842-.18-1.492-.54-1.947-.36-.455-.864-.685-1.514-.685zm27.178 0c-.65 0-1.16.236-1.53.706-.368.47-.554 1.121-.554 1.955 0 .823.186 1.463.554 1.918.37.457.884.685 1.544.685.65 0 1.155-.23 1.515-.691.36-.462.54-1.107.54-1.941 0-.844-.18-1.492-.54-1.947-.36-.457-.869-.685-1.529-.685zm69.36-4.026l-.446 7.457h-.891l-.458-7.457h1.794zm-118.733 3.94c-.546 0-.991.164-1.336.493-.346.33-.564.8-.66 1.41h3.833c-.057-.62-.243-1.092-.56-1.417-.317-.324-.742-.486-1.277-.486zm98.548 0c-.545 0-.99.164-1.336.493-.345.33-.564.8-.66 1.41h3.833c-.057-.62-.243-1.092-.56-1.417-.316-.324-.741-.486-1.277-.486zm9.875-4.114v1.541h-1.677v-1.541h1.677zM154.69 3.986c8.76 0 16.823 5.893 17.908 13.521h-31.236c-.268 1.175-.432 2.274-.432 3.546 0 8.347 6.152 15.111 13.76 15.111 5.599 0 10.421-3.676 12.562-8.955h4.514C169.105 33.528 162.461 38 154.69 38c-10.136 0-18.34-7.594-18.34-16.947 0-9.363 8.204-17.067 18.34-17.067zM132.011 0v37.243h-4.544v-5.13c-3.356 3.527-8.277 5.748-13.76 5.748-10.124 0-18.324-7.576-18.324-16.93 0-9.357 8.2-16.931 18.325-16.931 5.482 0 10.403 2.221 13.76 5.752V0h4.543zM72.737 3.987c10.115 0 18.312 7.585 18.312 16.93l-.011 16.136v.175h-4.544v-5.131c-3.354 3.539-8.275 5.763-13.757 5.763-10.128 0-18.325-7.588-18.325-16.943 0-9.345 8.197-16.93 18.325-16.93zm183.948-.025c10.12 0 18.322 7.583 18.322 16.935 0 9.35-8.201 16.933-18.322 16.933-10.118 0-18.32-7.583-18.32-16.933 0-9.352 8.202-16.935 18.32-16.935zm36.115.18c.695-.022 2.663-.003 3 0h.05v1.811s-2.369-.004-3.16.03c-4.925.221-8.868 4.718-8.868 10.575v20.741h-4.569V17.522c0-3.986 1.768-7.564 4.569-10.016a13.542 13.542 0 012.177-1.553c2-1.15 4.27-1.724 6.801-1.81zm-278.413.042c4.656 0 8.748 2.267 11.146 5.697 2.4-3.43 6.493-5.697 11.147-5.697 7.004 0 12.754 5.108 13.341 11.624.034.365.055.73.055 1.106v20.358h-4.542V16.914c0-6.04-3.966-10.938-8.852-10.938-4.887 0-8.853 4.899-8.853 10.938v20.358h-4.59V16.914c0-6.04-3.965-10.938-8.852-10.938-4.886 0-8.852 4.899-8.852 10.938v20.358H.993V16.914c0-.377.023-.741.055-1.106.587-6.516 6.337-11.624 13.337-11.624zm294.92 0c4.653 0 8.747 2.266 11.146 5.696 2.398-3.43 6.49-5.697 11.146-5.697 7.004 0 12.752 5.11 13.34 11.624.033.365.054.73.054 1.106v20.358h-4.542V16.913c0-6.039-3.963-10.937-8.85-10.937-4.889 0-8.855 4.898-8.855 10.937v20.358h-4.59V16.913c0-6.039-3.965-10.937-8.85-10.937-4.886 0-8.854 4.898-8.854 10.937v20.358h-4.542V16.913c0-.376.023-.741.055-1.106.587-6.513 6.337-11.624 13.34-11.624zM231.495.078c1.2-.06 3.329-.032 3.649-.028l.04.001v1.81s-2.846-.013-3.785.044c-5.14.312-8.845 4.705-8.845 10.561 0 .338-.028.67 0 1h12.63v1.988h-12.63V37.23h-4.57V15.455h-4.568v-1.989h4.569v-.034c0-3.989 1.766-7.565 4.569-10.016a13.525 13.525 0 012.175-1.553c1.999-1.15 4.21-1.651 6.766-1.784zm-38.437 3.907c4.896 0 9.302 1.906 12.405 4.953 2.846 2.794 4.59 6.55 4.59 10.68v17.578h-4.59V19.411c0-7.544-5.563-13.66-12.426-13.66-6.862 0-12.423 6.116-12.423 13.66v17.786h-4.55V19.619c0-4.112 1.726-7.852 4.55-10.644 3.102-3.067 7.53-4.99 12.444-4.99zm-79.35 1.85c-7.598 0-13.748 6.757-13.748 15.095 0 8.337 6.15 15.095 13.749 15.095 7.589 0 13.75-6.758 13.75-15.095 0-8.338-6.161-15.095-13.75-15.095zm-40.971-.013c-7.599 0-13.749 6.757-13.749 15.094 0 8.338 6.15 15.095 13.749 15.095 7.587 0 13.748-6.757 13.748-15.095 0-8.337-6.161-15.094-13.748-15.094zm183.948-.025c-7.591 0-13.75 6.762-13.75 15.1 0 8.337 6.159 15.096 13.75 15.096 7.594 0 13.75-6.76 13.75-15.097s-6.156-15.1-13.75-15.1zm-101.996.026c-5.766 0-10.668 4.144-12.717 9.658h25.646c-1.517-5.603-7.174-9.658-12.929-9.658z" fill="#FF893D" fill-rule="evenodd"></path></svg>
                    </div>
                </div>
            </div>
            <div class="o-loading__box -cover">
                <div class="o-loading__cover">
                    <div class="o-loading__logo">
                        <svg xmlns="http://www.w3.org/2000/svg" width="345" height="59" viewBox="0 0 345 59"><path d="M151.435 48.371l2.268 5.672 2.311-5.672h1.451l-3.375 7.925c-.372.863-.84 1.496-1.4 1.897-.56.401-1.25.67-2.074.807l-.317-1.134c.68-.155 1.195-.356 1.545-.604.35-.246.627-.619.84-1.111l.257-.61-3.042-7.17h1.536zm-6.016 5.584v1.192c0 .455-.076.869-.231 1.244-.153.372-.41.742-.775 1.11l-.617-.508c.403-.445.632-.878.689-1.294h-.789v-1.744h1.723zm106.013-7.923v2.34h1.996v1.134h-1.996v3.679c0 .503.108.86.322 1.067.217.21.515.315.9.315.247 0 .5-.042.76-.118v1.192a3.36 3.36 0 01-1.08.161c-.745 0-1.323-.213-1.734-.64-.413-.425-.62-1.048-.62-1.861v-3.795h-1.405v-1.134h1.406v-1.831l1.45-.509zm-134.713 0v2.34h1.997v1.134h-1.997v3.679c0 .503.109.86.323 1.067.217.21.515.315.9.315.246 0 .5-.042.76-.118v1.192a3.36 3.36 0 01-1.08.161c-.745 0-1.324-.213-1.734-.64-.413-.425-.62-1.048-.62-1.861v-3.795h-1.405v-1.134h1.406v-1.831l1.45-.509zm121.062 2.167c.974 0 1.743.318 2.303.958.56.641.84 1.527.84 2.66v.247h-5.168c.047 1.717.817 2.576 2.31 2.576.834 0 1.595-.278 2.283-.83l.446 1.047c-.327.29-.74.52-1.243.689a4.643 4.643 0 01-1.514.256c-1.158 0-2.068-.337-2.728-1.011-.66-.673-.989-1.598-.989-2.77 0-.746.145-1.41.436-1.991a3.22 3.22 0 011.228-1.351c.527-.32 1.126-.48 1.796-.48zm-127.238 0c.517 0 1.007.08 1.473.246.462.165.859.397 1.183.697l-.445 1.032c-.699-.56-1.434-.842-2.211-.842-.468 0-.832.09-1.091.27a.857.857 0 00-.388.746c0 .253.083.458.247.613.161.153.42.275.774.364l1.235.304c.688.155 1.199.4 1.528.732.331.336.494.77.494 1.302 0 .66-.26 1.181-.782 1.564-.52.383-1.23.575-2.131.575-1.263 0-2.258-.31-2.986-.93l.43-1.062a4.152 4.152 0 002.599.873c.498 0 .876-.085 1.136-.255a.815.815 0 00.386-.72c0-.262-.088-.467-.266-.618-.178-.151-.476-.28-.897-.385l-1.205-.292c-.634-.134-1.114-.374-1.443-.718-.331-.345-.495-.774-.495-1.287 0-.66.26-1.194.781-1.6.521-.407 1.214-.61 2.074-.61zm71.156 0c.507 0 .996.084 1.466.254.467.17.845.4 1.134.689l-.446 1.064c-.678-.545-1.363-.816-2.053-.816-.7 0-1.243.23-1.637.69-.393.46-.589 1.108-.589 1.94 0 .834.196 1.477.589 1.928.394.45.938.675 1.637.675.709 0 1.393-.271 2.053-.813l.446 1.061c-.307.292-.697.517-1.171.683a4.461 4.461 0 01-1.486.248c-.727 0-1.36-.157-1.902-.465-.54-.31-.956-.75-1.243-1.316-.286-.567-.429-1.23-.429-1.984 0-.758.15-1.426.452-2.007.3-.582.725-1.033 1.27-1.351.544-.321 1.182-.48 1.91-.48zm-42.467 0c.975 0 1.743.318 2.303.958.56.641.84 1.527.84 2.66v.247h-5.168c.047 1.717.818 2.576 2.312 2.576.833 0 1.593-.278 2.282-.83l.445 1.047c-.327.29-.74.52-1.242.689a4.647 4.647 0 01-1.514.256c-1.159 0-2.068-.337-2.728-1.011-.66-.673-.989-1.598-.989-2.77 0-.746.145-1.41.435-1.991a3.226 3.226 0 011.228-1.351c.528-.32 1.126-.48 1.796-.48zm-38.208.173v4.479c0 .6.125 1.043.368 1.328.243.288.615.43 1.111.43.595 0 1.071-.194 1.429-.58.36-.388.54-.903.54-1.542v-4.115h1.45V55.7h-1.408v-1.223a2.424 2.424 0 01-.946.983 2.722 2.722 0 01-1.365.341c-1.743 0-2.613-.978-2.613-2.936v-4.493h1.434zm66.906 0v4.479c0 .6.125 1.043.368 1.328.243.288.615.43 1.111.43.595 0 1.071-.194 1.429-.58.36-.388.54-.903.54-1.542v-4.115h1.45V55.7h-1.408v-1.223a2.424 2.424 0 01-.946.983 2.722 2.722 0 01-1.365.341c-1.743 0-2.613-.978-2.613-2.936v-4.493h1.434zm20.373-.174c.554 0 1.04.118 1.451.356.41.238.725.575.948 1.01v-1.192h1.435V55.7h-1.435v-1.252a2.241 2.241 0 01-.94 1.004c-.417.234-.905.35-1.459.35-.641 0-1.203-.156-1.686-.466-.484-.31-.86-.75-1.128-1.316-.267-.567-.4-1.229-.4-1.984 0-.757.135-1.426.409-2.007.271-.582.651-1.033 1.14-1.351a2.958 2.958 0 011.665-.48zm33.267 0c.553 0 1.04.118 1.45.356.411.238.726.575.949 1.01v-1.192h1.434V55.7h-1.434v-1.252a2.252 2.252 0 01-.94 1.004c-.417.234-.906.35-1.46.35-.64 0-1.203-.156-1.685-.466-.484-.31-.86-.75-1.128-1.316-.268-.567-.4-1.229-.4-1.984 0-.757.134-1.426.408-2.007.272-.582.652-1.033 1.14-1.351a2.961 2.961 0 011.666-.48zm-60.114 0c.699 0 1.314.154 1.851.465.536.31.95.753 1.243 1.33.292.577.437 1.248.437 2.013 0 .766-.145 1.436-.437 2.007a3.151 3.151 0 01-1.243 1.322c-.537.31-1.152.466-1.85.466-.71 0-1.333-.155-1.869-.466-.535-.31-.95-.75-1.242-1.322-.29-.57-.435-1.241-.435-2.007 0-.765.145-1.436.435-2.013a3.135 3.135 0 011.242-1.33c.536-.31 1.159-.466 1.868-.466zm-67.048-2.85v1.25h-3.46V55.7h-1.493v-9.102h-3.462v-1.25h8.415zm163.556 8.608V55.7h-1.722v-1.744h1.722zM247.57 48.37v7.33h-1.437v-7.33h1.437zM228.82 45v6.687l3.216-3.3h1.795l-3.46 3.517 3.76 3.795h-1.851l-3.46-3.46v3.46h-1.436V45h1.436zm-30.854 3.198c1.733 0 2.597.979 2.597 2.936v4.564h-1.449v-4.477c0-.64-.122-1.105-.366-1.395-.243-.291-.635-.436-1.17-.436-.613 0-1.102.194-1.472.581-.37.387-.553.907-.553 1.556v4.171h-1.437v-5.261c0-.766-.037-1.455-.112-2.065h1.348l.13 1.264c.228-.463.563-.821.996-1.068.436-.246.932-.37 1.488-.37zm-99.986 0c.223 0 .452.033.691.102l-.03 1.336a2.431 2.431 0 00-.846-.145c-.66 0-1.155.203-1.486.604-.329.402-.495.909-.495 1.52v4.083h-1.438v-5.261c0-.766-.037-1.455-.112-2.065h1.348l.129 1.324c.192-.486.49-.856.89-1.113a2.458 2.458 0 011.35-.385zm33.87 0c1.606 0 2.411.979 2.411 2.936v4.564h-1.449v-4.491c0-.63-.108-1.09-.324-1.38-.215-.292-.562-.437-1.04-.437-.556 0-.996.194-1.314.581-.321.387-.48.917-.48 1.585v4.142h-1.451v-4.491c0-.63-.109-1.09-.323-1.38-.217-.292-.564-.437-1.04-.437-.558 0-.995.194-1.322.581-.327.387-.489.917-.489 1.585v4.142h-1.436v-5.261c0-.766-.039-1.455-.115-2.065h1.349l.13 1.22c.21-.444.514-.79.904-1.032.394-.24.852-.362 1.38-.362 1.119 0 1.85.47 2.196 1.411.22-.436.543-.782.968-1.032a2.779 2.779 0 011.445-.379zm82.585 0c1.606 0 2.41.979 2.41 2.936v4.564h-1.448v-4.491c0-.63-.108-1.09-.325-1.38-.215-.292-.562-.437-1.04-.437-.556 0-.995.194-1.314.581-.32.387-.48.917-.48 1.585v4.142h-1.45v-4.491c0-.63-.11-1.09-.324-1.38-.216-.292-.564-.437-1.04-.437-.558 0-.995.194-1.322.581-.327.387-.488.917-.488 1.585v4.142h-1.437v-5.261c0-.766-.039-1.455-.114-2.065h1.348l.131 1.22c.209-.444.513-.79.903-1.032.395-.24.853-.362 1.38-.362 1.12 0 1.851.47 2.196 1.411.221-.436.544-.782.969-1.032a2.776 2.776 0 011.445-.379zm7.465 1.177c-.65 0-1.16.236-1.53.706-.369.47-.554 1.121-.554 1.955 0 .823.185 1.463.553 1.918.37.457.885.685 1.545.685.65 0 1.155-.23 1.514-.691.36-.462.54-1.107.54-1.941 0-.844-.18-1.492-.54-1.947-.36-.457-.868-.685-1.528-.685zm-60.445 0c-.66 0-1.173.229-1.537.684-.363.455-.547 1.105-.547 1.947 0 .863.18 1.517.54 1.964.359.445.87.668 1.53.668.668 0 1.18-.223 1.534-.668.356-.447.534-1.101.534-1.964 0-.842-.18-1.492-.54-1.947-.36-.455-.864-.685-1.514-.685zm27.178 0c-.65 0-1.16.236-1.53.706-.368.47-.554 1.121-.554 1.955 0 .823.186 1.463.554 1.918.37.457.884.685 1.544.685.65 0 1.155-.23 1.515-.691.36-.462.54-1.107.54-1.941 0-.844-.18-1.492-.54-1.947-.36-.457-.869-.685-1.529-.685zm69.36-4.026l-.446 7.457h-.891l-.458-7.457h1.794zm-118.733 3.94c-.546 0-.991.164-1.336.493-.346.33-.564.8-.66 1.41h3.833c-.057-.62-.243-1.092-.56-1.417-.317-.324-.742-.486-1.277-.486zm98.548 0c-.545 0-.99.164-1.336.493-.345.33-.564.8-.66 1.41h3.833c-.057-.62-.243-1.092-.56-1.417-.316-.324-.741-.486-1.277-.486zm9.875-4.114v1.541h-1.677v-1.541h1.677zM154.69 3.986c8.76 0 16.823 5.893 17.908 13.521h-31.236c-.268 1.175-.432 2.274-.432 3.546 0 8.347 6.152 15.111 13.76 15.111 5.599 0 10.421-3.676 12.562-8.955h4.514C169.105 33.528 162.461 38 154.69 38c-10.136 0-18.34-7.594-18.34-16.947 0-9.363 8.204-17.067 18.34-17.067zM132.011 0v37.243h-4.544v-5.13c-3.356 3.527-8.277 5.748-13.76 5.748-10.124 0-18.324-7.576-18.324-16.93 0-9.357 8.2-16.931 18.325-16.931 5.482 0 10.403 2.221 13.76 5.752V0h4.543zM72.737 3.987c10.115 0 18.312 7.585 18.312 16.93l-.011 16.136v.175h-4.544v-5.131c-3.354 3.539-8.275 5.763-13.757 5.763-10.128 0-18.325-7.588-18.325-16.943 0-9.345 8.197-16.93 18.325-16.93zm183.948-.025c10.12 0 18.322 7.583 18.322 16.935 0 9.35-8.201 16.933-18.322 16.933-10.118 0-18.32-7.583-18.32-16.933 0-9.352 8.202-16.935 18.32-16.935zm36.115.18c.695-.022 2.663-.003 3 0h.05v1.811s-2.369-.004-3.16.03c-4.925.221-8.868 4.718-8.868 10.575v20.741h-4.569V17.522c0-3.986 1.768-7.564 4.569-10.016a13.542 13.542 0 012.177-1.553c2-1.15 4.27-1.724 6.801-1.81zm-278.413.042c4.656 0 8.748 2.267 11.146 5.697 2.4-3.43 6.493-5.697 11.147-5.697 7.004 0 12.754 5.108 13.341 11.624.034.365.055.73.055 1.106v20.358h-4.542V16.914c0-6.04-3.966-10.938-8.852-10.938-4.887 0-8.853 4.899-8.853 10.938v20.358h-4.59V16.914c0-6.04-3.965-10.938-8.852-10.938-4.886 0-8.852 4.899-8.852 10.938v20.358H.993V16.914c0-.377.023-.741.055-1.106.587-6.516 6.337-11.624 13.337-11.624zm294.92 0c4.653 0 8.747 2.266 11.146 5.696 2.398-3.43 6.49-5.697 11.146-5.697 7.004 0 12.752 5.11 13.34 11.624.033.365.054.73.054 1.106v20.358h-4.542V16.913c0-6.039-3.963-10.937-8.85-10.937-4.889 0-8.855 4.898-8.855 10.937v20.358h-4.59V16.913c0-6.039-3.965-10.937-8.85-10.937-4.886 0-8.854 4.898-8.854 10.937v20.358h-4.542V16.913c0-.376.023-.741.055-1.106.587-6.513 6.337-11.624 13.34-11.624zM231.495.078c1.2-.06 3.329-.032 3.649-.028l.04.001v1.81s-2.846-.013-3.785.044c-5.14.312-8.845 4.705-8.845 10.561 0 .338-.028.67 0 1h12.63v1.988h-12.63V37.23h-4.57V15.455h-4.568v-1.989h4.569v-.034c0-3.989 1.766-7.565 4.569-10.016a13.525 13.525 0 012.175-1.553c1.999-1.15 4.21-1.651 6.766-1.784zm-38.437 3.907c4.896 0 9.302 1.906 12.405 4.953 2.846 2.794 4.59 6.55 4.59 10.68v17.578h-4.59V19.411c0-7.544-5.563-13.66-12.426-13.66-6.862 0-12.423 6.116-12.423 13.66v17.786h-4.55V19.619c0-4.112 1.726-7.852 4.55-10.644 3.102-3.067 7.53-4.99 12.444-4.99zm-79.35 1.85c-7.598 0-13.748 6.757-13.748 15.095 0 8.337 6.15 15.095 13.749 15.095 7.589 0 13.75-6.758 13.75-15.095 0-8.338-6.161-15.095-13.75-15.095zm-40.971-.013c-7.599 0-13.749 6.757-13.749 15.094 0 8.338 6.15 15.095 13.749 15.095 7.587 0 13.748-6.757 13.748-15.095 0-8.337-6.161-15.094-13.748-15.094zm183.948-.025c-7.591 0-13.75 6.762-13.75 15.1 0 8.337 6.159 15.096 13.75 15.096 7.594 0 13.75-6.76 13.75-15.097s-6.156-15.1-13.75-15.1zm-101.996.026c-5.766 0-10.668 4.144-12.717 9.658h25.646c-1.517-5.603-7.174-9.658-12.929-9.658z" fill="#FFFFFF" fill-rule="evenodd"></path></svg>
                    </div>
                </div>
            </div>
        </div>
        <img class="o-three-line__static" src="https://www.trustme.com.tw/wp-content/themes/project-theme/src/img/public/stroke-bg.svg" loading="lazy" decoding="async">
    </div>

</div>--}}


<header>
    <div class="wrapper">
        <div class="logo-sec">
            <a href="{{ url('/') }}">
                <div class="logo-wrap hover-logo">
                    <div class="place">
                        <div class="compose">
                            <img class="fra-1" src="{{ asset('static/img/lg/fra-1.png') }}" alt="logo" decoding="async">
                            <img class="fra-2" src="{{ asset('static/img/lg/fra-2.png') }}" alt="logo" decoding="async">
                            <img class="fra-3"  src="{{ asset('static/img/lg/fra-3.png') }}" alt="logo" decoding="async">
                        </div>
                        <div class="intact">
                            <img class="xenical-logo" src="{{ asset('static/img/lg/xenical-1.png') }}" alt="xenical" decoding="async">
                            <p class="text">全球領先健康減肥藥</p>
                        </div>

                    </div>
                </div>
            </a>
        </div>
        <div class="nav-sec">
            <ul class="base">
                <li><a href="{{ url('/') }}" data-track-section="nav.top" data-track-name="nav.top.home" data-observer="頂部-首頁">首頁</a></li>
                <li><a href="{{ url('about') }}" data-track-section="nav.top" data-track-name="nav.top.about" data-observer="頂部-認識羅氏鮮">認識羅氏鮮</a></li>
                <li><a href="{{ url('faq') }}" data-track-section="nav.top" data-track-name="nav.top.faq" data-observer="頂部-營養師解答">營養師解答</a></li>
                <li><a href="{{ url('news') }}" data-track-section="nav.top" data-track-name="nav.top.news" data-observer="頂部-瘦身專欄">瘦身專欄</a></li>
            </ul>
            <div class="aks">
                <a class="btn slim-btn btn-ef2" href="{{ url('compute') }}" data-track-section="header" data-track-name="header.compute_btn" data-observer="頂部-瘦身計算機">瘦身計算機</a>
                <a class="btn shop-btn btn-ef1" href="{{ url('product') }}" data-track-section="header" data-track-name="header.order_btn" data-observer="頂部-線上訂購">線上訂購</a>
            </div>
        </div>
    </div>
</header>

@section('banners')
    @if($layout['banners'] && !$layout['banners']->isEmpty())
        <section class="banner-section">
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    @foreach($layout['banners'] as $item)
                        @if($item->img)
                            <div class="swiper-slide">
                                <a href="{{ $item->href?url($item->href):"javascript:;" }}"><img src="{{ asset('uploads/'.$item->img) }}" alt="{{ $item->alt }}" loading="lazy" decoding="async"></a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @yield('embed-banner')
        </section>
    @endif
@show

@section('breadcrumb')

@show

<main>
    @yield('content')
</main>
<footer>
    <div class="wrapper">

        <div class="main">
            <div class="menu-column">
                <div class="menu">
                    <p class="title">Sale</p>
                    <ul class="nav">
                        <li><a href="{{ url('product') }}" data-track-section="footer.sale" data-track-name="footer.sale.order" data-observer="底部-線上訂購">羅氏鮮線上訂購</a></li>
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
                                    <p class="cn"><span>線上訂購</span></p>
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
        </div>


        <div class="description">
            <div class="partner">
                <div class="icon"><img style="width: 126px" src="{{ asset('static/img/fdausa.webp') }}" alt="fda-usa" loading="lazy" decoding="async"></div>
                <div class="icon"><img style="width: 152px" src="{{ asset('static/img/ema.webp') }}" alt="ema" loading="lazy" decoding="async"></div>

                <div class="icon"><img style="width: 60px" src="{{ asset('static/img/ROCHE.webp') }}" alt="ROCHE" loading="lazy" decoding="async"></div>
                <div class="icon"><img style="width: 140px" src="{{ asset('static/img/CHEPLA.webp') }}" alt="CHEPLA" loading="lazy" decoding="async"></div>

                <div class="icon"><img style="width: 52px" src="{{ asset('static/img/ssl.webp') }}" alt="ssl" loading="lazy" decoding="async"></div>
            </div>
            <p class="copyright">{!! app('cache.config')->get('copyright') !!}</p>
        </div>



    </div>
    <div class="back-top" id="back-top" data-track-section="footer" data-track-name="footer.back_top" data-observer="回到頂部">
        <a href="javascript:;">
            <div class="line" ></div>
            <div class="icon"></div>
            <div class="text ">T<br>O<br>P</div>
        </a>
    </div>
</footer>


</body>

@section('script')

@if($needsSwiper)
<script src="{{ asset('static/swiper4/swiper.min.js') }}?ver={{ config('app.asset_version') }}"></script>
@endif
{!! \App\Services\ConfigService::get('google_ga') !!}
@show
<script>// Loading removed - body starts without -loading class
</script>
<script>
    $('#back-top').click(function (event) {
        event.preventDefault();
        $('html, body').animate({ scrollTop: 0 }, 500);
    })
</script>
</html>
