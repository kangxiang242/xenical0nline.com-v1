<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="chrome=1,IE=edge">
    <meta http-equiv="content-language" content="zh-tw">
    <meta name="format-detection" content="telephone=no" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <link rel="canonical" href="{{ config('app.url') }}/{{ trim(request()->path(),'/') }}">
    <link rel="shortcut icon" href="{{ \App\Services\ConfigService::get('favicon')?asset('uploads/'.\App\Services\ConfigService::get('favicon')):'/favicon.ico' }}">
    @section('style')
    @show
    {!! vite_tags() !!}
    @stack('schema')

    @php
        $trackingPage = ['page_type' => 'unknown', 'goods_id' => null, 'article_id' => null, 'cms_uri' => null];
        if (request()->is('/')) {
            $trackingPage['page_type'] = 'home';
        } elseif (request()->is('product') && !request()->route('id')) {
            $trackingPage['page_type'] = 'product_list';
        } elseif (request()->is('product/*')) {
            $trackingPage['page_type'] = 'product_detail';
            $trackingPage['goods_id'] = request()->route('id');
        } elseif (request()->is('checkout/*')) {
            $trackingPage['page_type'] = 'checkout';
            $trackingPage['goods_id'] = request()->route('id');
        } elseif (request()->is('bmi')) {
            $trackingPage['page_type'] = 'bmi';
        } elseif (request()->is('bmr')) {
            $trackingPage['page_type'] = 'bmr';
        } elseif (request()->is('body-fat')) {
            $trackingPage['page_type'] = 'body_fat';
        } elseif (request()->is('news') || (request()->is('news/*') && !request()->route('id'))) {
            $trackingPage['page_type'] = 'news_list';
        } elseif (request()->is('news/*') && request()->route('id')) {
            $trackingPage['page_type'] = 'news_detail';
            $trackingPage['article_id'] = request()->route('id');
        } elseif (request()->is('news/*/*')) {
            $trackingPage['page_type'] = 'news_detail';
            $trackingPage['article_id'] = request()->route('id');
        } elseif (request()->is('message')) {
            $trackingPage['page_type'] = 'message';
        } elseif (request()->is('check') || request()->is('check/*')) {
            $trackingPage['page_type'] = 'order_check';
        } elseif (request()->is('about', 'guide', 'payment-delivery', 'after-sales', 'privacy')) {
            $trackingPage['page_type'] = 'cms';
            $trackingPage['cms_uri'] = trim(request()->path(), '/');
        }
        $trackingWebHost = parse_url(config('app.url'), PHP_URL_HOST);
    @endphp
    <script>
        window.__TRACKING_CONFIG__ = {
            endpoint: @json(url('/observer/store')),
            webHost: @json($trackingWebHost),
            pluginBase: @json(rtrim(asset('static/js/tracker-plugins/'), '/') . '/'),
            assetVersion: @json(config('app.asset_version')),
            enabled: @json(!app()->environment('testing'))
        };
        window.__TRACKING_PAGE__ = @json($trackingPage);
    </script>
    <script src="{{ asset('static/js/jquery.min.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('static/js/tracker.js') }}?ver={{ config('app.asset_version') }}" defer></script>
    <script src="{{ asset('static/js/observer.js') }}?ver={{ config('app.asset_version') }}" defer></script>

    @php
        $__flashRaw = session('flash');
        $__flashData = false;
        if ($__flashRaw !== null && $__flashRaw !== '') {
            $__decoded = json_decode($__flashRaw, true);
            $__flashData = (json_last_error() === JSON_ERROR_NONE) ? $__decoded : false;
        }
    @endphp
    <script>
        var is_ajax_get_cart = 0;
        var flash_data = @json($__flashData);

        var province = [];

        var free_shipping_where = parseInt("{{ \App\Services\ConfigService::get('freight_where',0) }}");
        var free_shipping_freight = parseInt("{{ \App\Services\ConfigService::get('freight',0) }}");

    </script>
    
</head>
<body @yield('body_attributes')>

@include('web.layout.header')

@yield('content')


@include('web.layout.footer')

@stack('update-box')
@stack('qa-js')
@stack('tick-scroll')
@stack('rice-scroll')
@include('web.svg-sprite')
<script>
    (function () {
        document.addEventListener('DOMContentLoaded', function () {
            var btn = document.querySelector('.menu-btn');
            var nav = document.querySelector('.mobile-nav');
            if (!btn || !nav) {
                return;
            }
            function openNav() {
                nav.classList.add('is-open');
                nav.setAttribute('aria-hidden', 'false');
                btn.classList.add('close-menu');
                btn.setAttribute('aria-expanded', 'true');
                document.body.classList.add('mobile-nav-open');
                if (window.XenicalTracker) {
                    XenicalTracker.track('click', 'nav.menu.open', { section: 'nav', label: '側欄-打開', explain: '側欄-打開' });
                }
            }
            function closeNav() {
                nav.classList.remove('is-open');
                nav.setAttribute('aria-hidden', 'true');
                btn.classList.remove('close-menu');
                btn.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('mobile-nav-open');
                if (window.XenicalTracker) {
                    XenicalTracker.track('click', 'nav.menu.close', { section: 'nav', label: '側欄-關閉', explain: '側欄-關閉' });
                }
            }
            function toggleNav() {
                if (nav.classList.contains('is-open')) {
                    closeNav();
                } else {
                    openNav();
                }
            }
            btn.addEventListener('click', toggleNav);
            nav.querySelectorAll('.mobile-nav__link').forEach(function (a) {
                a.addEventListener('click', closeNav);
            });
            nav.addEventListener('click', function (e) {
                if (e.target === nav) {
                    closeNav();
                }
            });
            nav.setAttribute('aria-hidden', 'true');
            btn.setAttribute('aria-expanded', 'false');
        });
    })();
</script>
@php
    $pillHomeHero = request()->is('/');
    $pillFxExcluded = request()->is('checkout/*')
        || request()->is('check')
        || request()->is('check/*')
        || request()->is('product')
        || request()->is('product/*');
@endphp
@if($pillHomeHero)
    @include('web.widgets.pill-fx', ['pillHomeHero' => true])
@elseif(!$pillFxExcluded)
    @include('web.widgets.pill-fx')
@endif
@stack('pill-fx')

<script src="{{ asset('static/js/xie.js') }}?ver={{ config('app.asset_version') }}"></script>
{!! \App\Services\ConfigService::get('google_ga') !!}
@yield('script')
<script>
    $('#back-top').click(function (event) {
        event.preventDefault();
        $('html, body').animate({ scrollTop: 0 }, 500);
    })
</script>
</body>
</html>
