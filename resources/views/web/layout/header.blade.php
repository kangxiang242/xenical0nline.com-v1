@php
    $isHomePage = trim(request()->path(), '/') === '';
@endphp
<header class="main-header{{ $isHomePage ? '' : ' main-header--inner' }}">
    <a class="logo" href="/">
        <img class="logo-img" src="/static/img/logo-header.svg" alt="Xenical 羅氏鮮" width="200" height="50" decoding="async" fetchpriority="high" />
    </a>
    @if(!request()->is('checkout/*') && !request()->is('check/*') && !request()->is('order/*'))
    <nav class="nav">
        <ul class="web-nav">
            <li class="nav-item"><a class="nav-item-link" href="{{ url('/') }}" data-observer="頂部-首頁" data-track-section="header" data-track-name="header.home">首頁</a></li>
            <li class="nav-item"><a class="nav-item-link" href="{{ url('about') }}" data-observer="頂部-羅氏鮮介紹" data-track-section="header" data-track-name="header.about">羅氏鮮介紹</a></li>
            <li class="nav-item"><a class="nav-item-link" href="{{ url('news') }}" data-observer="頂部-減肥知識" data-track-section="header" data-track-name="header.news">減肥知識分享</a></li>
            <li class="has-dropdown">
                <span class="dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false">健康計算</span>
                <ul class="dropdown">
                    <li><a class="btn-ef2" href="/bmi" data-observer="頂部-BMI計算" data-track-section="header" data-track-name="header.bmi">BMI計算<svg class="arrowicon"><use href="#icon-arrowicon"/></svg></a></li>
                    <li><a class="btn-ef2" href="/bmr" data-observer="頂部-BMR計算" data-track-section="header" data-track-name="header.bmr">BMR計算<svg class="arrowicon"><use href="#icon-arrowicon"/></svg></a></li>
                    <li><a class="btn-ef2" href="/body-fat" data-observer="頂部-體脂肪計算" data-track-section="header" data-track-name="header.body_fat">體脂肪率計算<svg class="arrowicon"><use href="#icon-arrowicon"/></svg></a></li>
                </ul>
            </li>
            
            <li><a class="btn-ef1" href="/product" data-observer="頂部-線上訂購" data-track-section="header" data-track-name="header.order_btn">線上訂購減肥藥</a></li>
            
            
        </ul>
    </nav>
    <button class="menu-btn" type="button" aria-label="開啟選單" aria-expanded="false" aria-controls="mobile-nav-drawer">
        <span class="bar bar--1"></span>
        <span class="bar bar--2"></span>
        <span class="bar bar--3"></span>
    </button>
    @else
        <div class="pro">
            <svg class="pro-icon" viewBox="0 0 1024 1024"><use href="#icon-safeicon"></use></svg>
            <div class="pro-running"></div>
            <span class="pro-text">SSL加密保護中</span>
        </div>
    @endif
    <nav class="mobile-nav" id="mobile-nav-drawer" aria-label="移動端主選單" aria-hidden="true">
        <div class="mobile-nav__list">
            <p class="en-title">Home</p>
            <ul class="mobile-nav__sublist" role="list">
                <li class="mobile-nav__item"><a class="mobile-nav__link" href="{{ url('/') }}" data-observer="移動-首頁" data-track-section="nav.mobile" data-track-name="nav.mobile.home">首頁<svg class="mobile-nav__arrow" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-arrowicon"></use></svg></a></li>
            </ul>
            <p class="en-title">Product</p>
            <ul class="mobile-nav__sublist" role="list">
                <li class="mobile-nav__item"><a class="mobile-nav__link" href="{{ url('about') }}" data-observer="移動-羅氏鮮介紹" data-track-section="nav.mobile" data-track-name="nav.mobile.about">減肥藥羅氏鮮介紹<svg class="mobile-nav__arrow" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-arrowicon"></use></svg></a></li>
                <li class="mobile-nav__item"><a class="mobile-nav__link" href="{{ url('product') }}" data-observer="移動-線上訂購" data-track-section="nav.mobile" data-track-name="nav.mobile.product">羅氏鮮減肥藥線上訂購<svg class="mobile-nav__arrow" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-arrowicon"></use></svg></a></li>
            </ul>
            <p class="en-title">Slimming</p>
            <ul class="mobile-nav__sublist" role="list">
                <li class="mobile-nav__item"><a class="mobile-nav__link" href="{{ url('bmi') }}" data-observer="移動-BMI" data-track-section="nav.mobile" data-track-name="nav.mobile.bmi">BMI計算<svg class="mobile-nav__arrow" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-arrowicon"></use></svg></a></li>
                <li class="mobile-nav__item"><a class="mobile-nav__link" href="{{ url('bmr') }}" data-observer="移動-BMR" data-track-section="nav.mobile" data-track-name="nav.mobile.bmr">BMR計算<svg class="mobile-nav__arrow" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-arrowicon"></use></svg></a></li>
                <li class="mobile-nav__item"><a class="mobile-nav__link" href="{{ url('body-fat') }}" data-observer="移動-體脂肪" data-track-section="nav.mobile" data-track-name="nav.mobile.body_fat">體脂肪率計算<svg class="mobile-nav__arrow" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-arrowicon"></use></svg></a></li>
                <li class="mobile-nav__item"><a class="mobile-nav__link" href="{{ url('news') }}" data-observer="移動-專欄" data-track-section="nav.mobile" data-track-name="nav.mobile.news">減肥瘦身專欄<svg class="mobile-nav__arrow" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-arrowicon"></use></svg></a></li>
            </ul>
            <p class="en-title">Service</p>
            <ul class="mobile-nav__sublist" role="list">
                <li class="mobile-nav__item"><a class="mobile-nav__link" href="{{ url('guide') }}" data-observer="移動-購前須知" data-track-section="nav.mobile" data-track-name="nav.mobile.guide">購前須知<svg class="mobile-nav__arrow" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-arrowicon"></use></svg></a></li>
                <li class="mobile-nav__item"><a class="mobile-nav__link" href="{{ url('payment-delivery') }}" data-observer="移動-付款配送" data-track-section="nav.mobile" data-track-name="nav.mobile.payment">付款與配送<svg class="mobile-nav__arrow" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-arrowicon"></use></svg></a></li>
                <li class="mobile-nav__item"><a class="mobile-nav__link" href="{{ url('after-sales') }}" data-observer="移動-售後" data-track-section="nav.mobile" data-track-name="nav.mobile.after_sales">售後服務<svg class="mobile-nav__arrow" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-arrowicon"></use></svg></a></li>
                <li class="mobile-nav__item"><a class="mobile-nav__link" href="{{ url('check') }}" data-observer="移動-訂單追蹤" data-track-section="nav.mobile" data-track-name="nav.mobile.check">訂單追蹤<svg class="mobile-nav__arrow" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-arrowicon"></use></svg></a></li>
                <li class="mobile-nav__item"><a class="mobile-nav__link" href="{{ url('message') }}" data-observer="移動-取得協助" data-track-section="nav.mobile" data-track-name="nav.mobile.message">取得協助<svg class="mobile-nav__arrow" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-arrowicon"></use></svg></a></li>
                <li class="mobile-nav__item"><a class="mobile-nav__link" href="{{ url('privacy') }}" data-observer="移動-隱私" data-track-section="nav.mobile" data-track-name="nav.mobile.privacy">隱私權政策<svg class="mobile-nav__arrow" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-arrowicon"></use></svg></a></li>
            </ul>
        </div>
    </nav>

</header>
<script>
(function () {
    var src = @json(asset('static/js/logo-inline-svg.js'));
    function injectLogoInlineSvg() {
        if (document.querySelector('script[data-logo-inline-svg]')) {
            return;
        }
        var el = document.createElement('script');
        el.src = src;
        el.async = true;
        el.setAttribute('data-logo-inline-svg', '1');
        document.head.appendChild(el);
    }
    function scheduleInject() {
        if (typeof requestIdleCallback === 'function') {
            requestIdleCallback(injectLogoInlineSvg, { timeout: 3000 });
        } else {
            setTimeout(injectLogoInlineSvg, 1);
        }
    }
    if (document.readyState === 'complete') {
        scheduleInject();
    } else {
        window.addEventListener('load', scheduleInject, { once: true });
    }
})();
</script>
