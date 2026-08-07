@extends('web::layout.layout')
@php
    $comment_labels = $comment_labels->chunk(ceil(count($comment_labels)/3))
@endphp
@section('style')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('static/less/goods.css') }}?ver={{ config('app.asset_version') }}"/>
@stop

@section('script')
    <script src="{{ asset('static/js/price-animator.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script>
        var _0xf5c2fc=(776869^776870)+(134623^134622);const salesSwiper=document['\u0067\u0065\u0074\u0045\u006C\u0065\u006D\u0065\u006E\u0074\u0042\u0079\u0049\u0064']("\u0073\u0061\u006C\u0065\u0073\u0053\u0077\u0069\u0070\u0065\u0072");_0xf5c2fc=(286273^286279)+(667660^667652);const height=salesSwiper['\u0063\u0068\u0069\u006C\u0064\u0072\u0065\u006E'][552857^552857]['\u006F\u0066\u0066\u0073\u0065\u0074\u0048\u0065\u0069\u0067\u0068\u0074'];var _0x6a953f=(734124^734125)+(799889^799893);let isAnimating=false;_0x6a953f=721810^721819;var _0xc71bff=(797458^797460)+(944557^944553);let counter=791031^791031;_0xc71bff="kmhakc".split("").reverse().join("");var _0x57bad=(138054^138053)+(568551^568545);const interval=276879^274999;_0x57bad='\u0064\u0061\u006E\u006B\u0062\u0065';function getRandomDelay(){return Math['\u0066\u006C\u006F\u006F\u0072'](Math['\u0072\u0061\u006E\u0064\u006F\u006D']()*((932869^946725)-(414572^423036)+(634712^634713)))+(118106^125514);}function generateRandomSalesNumber(){return Math['\u0066\u006C\u006F\u006F\u0072'](Math['\u0072\u0061\u006E\u0064\u006F\u006D']()*((450338^449733)-(160189^160217)+(397317^397316)))+(223309^223273);}function updateRandomNumber(){var _0x2af=(553831^553831)+(117377^117379);const _0x62g=Math['\u0066\u006C\u006F\u006F\u0072'](Math['\u0072\u0061\u006E\u0064\u006F\u006D']()*((545774^545206)-(197221^197621)+(731549^731548)))+(388717^389117);_0x2af=(312825^312826)+(216156^216152);document['\u0067\u0065\u0074\u0045\u006C\u0065\u006D\u0065\u006E\u0074\u0042\u0079\u0049\u0064']("rebmuNmodnar".split("").reverse().join(""))['\u0069\u006E\u006E\u0065\u0072\u0054\u0065\u0078\u0074']=_0x62g;}function updateNextSalesNumber(_0xcb5fb){const _0xccd90b=salesSwiper['\u0063\u0068\u0069\u006C\u0064\u0072\u0065\u006E'][617118^617119];_0xcb5fb=691799^691794;if(_0xccd90b){const _0x392db=_0xccd90b['\u0071\u0075\u0065\u0072\u0079\u0053\u0065\u006C\u0065\u0063\u0074\u006F\u0072']("\u0023\u0073\u0061\u006C\u0065\u0073\u004E\u0075\u006D\u0062\u0065\u0072");if(_0x392db){_0x392db['\u0069\u006E\u006E\u0065\u0072\u0054\u0065\u0078\u0074']=generateRandomSalesNumber();}}}function startSwiper(){setInterval(()=>{if(isAnimating)return;isAnimating=!![];let _0x3e481a;const _0x7d524d=counter%(276148^276150)===(345397^345397)?getRandomDelay():interval;_0x3e481a=(232623^232622)+(813358^813358);setTimeout(()=>{updateNextSalesNumber();updateRandomNumber();salesSwiper['\u0073\u0074\u0079\u006C\u0065']['\u0074\u0072\u0061\u006E\u0073\u0069\u0074\u0069\u006F\u006E']="\u0074\u0072\u0061\u006E\u0073\u0066\u006F\u0072\u006D\u0020\u0031\u0073\u0020\u0063\u0075\u0062\u0069\u0063\u002D\u0062\u0065\u007A\u0069\u0065\u0072\u0028\u0030\u002E\u0035\u002C\u0020\u0030\u002C\u0020\u0030\u002C\u0020\u0031\u0029";salesSwiper['\u0073\u0074\u0079\u006C\u0065']['\u0074\u0072\u0061\u006E\u0073\u0066\u006F\u0072\u006D']=`translateY(-${height}px)`;setTimeout(()=>{salesSwiper['\u0073\u0074\u0079\u006C\u0065']['\u0074\u0072\u0061\u006E\u0073\u0069\u0074\u0069\u006F\u006E']="enon".split("").reverse().join("");salesSwiper['\u0061\u0070\u0070\u0065\u006E\u0064\u0043\u0068\u0069\u006C\u0064'](salesSwiper['\u0063\u0068\u0069\u006C\u0064\u0072\u0065\u006E'][676885^676885]);salesSwiper['\u0073\u0074\u0079\u006C\u0065']['\u0074\u0072\u0061\u006E\u0073\u0066\u006F\u0072\u006D']=`translateY(0)`;setTimeout(()=>{salesSwiper['\u0073\u0074\u0079\u006C\u0065']['\u0074\u0072\u0061\u006E\u0073\u0069\u0074\u0069\u006F\u006E']=")1 ,0 ,0 ,5.0(reizeb-cibuc s1 mrofsnart".split("").reverse().join("");isAnimating=false;},850445^850495);},601523^601691);},_0x7d524d);counter++;},interval);}startSwiper();updateRandomNumber();var randomInterval=Math['\u0066\u006C\u006F\u006F\u0072'](Math['\u0072\u0061\u006E\u0064\u006F\u006D']()*((141859^141868)-(463973^463983)+(507705^507704)))+(728872^728866);setInterval(updateRandomNumber,randomInterval*(402570^403298));
    </script>
    <script>
        function getRandomNumber(){return Math['\u0066\u006C\u006F\u006F\u0072'](Math['\u0072\u0061\u006E\u0064\u006F\u006D']()*((858563^855731)-(782525^786205)+(812258^812259)))+(572746^570090);}function updateRandomNumber(){var _0x55e2d=(171796^171794)+(868471^868464);var _0xcc83d=Date['\u006E\u006F\u0077']();_0x55e2d="ffoagc".split("").reverse().join("");var _0x88f12g=localStorage['\u0067\u0065\u0074\u0049\u0074\u0065\u006D']("\u006C\u0061\u0073\u0074\u0055\u0070\u0064\u0061\u0074\u0065\u0054\u0069\u006D\u0065");var _0x125a6a=localStorage['\u0067\u0065\u0074\u0049\u0074\u0065\u006D']("\u0074\u006F\u0074\u0061\u006C\u0073\u0061\u006C\u0065");var _0x9f3abd=(522396^522384)*(255750^255802)*(187159^187179)*(797165^797189);if(!_0x88f12g||_0xcc83d-_0x88f12g>=_0x9f3abd){var _0x57agea=getRandomNumber();localStorage['\u0073\u0065\u0074\u0049\u0074\u0065\u006D']("elaslatot".split("").reverse().join(""),_0x57agea);localStorage['\u0073\u0065\u0074\u0049\u0074\u0065\u006D']("\u006C\u0061\u0073\u0074\u0055\u0070\u0064\u0061\u0074\u0065\u0054\u0069\u006D\u0065",_0xcc83d);_0x125a6a=_0x57agea;}document['\u0067\u0065\u0074\u0045\u006C\u0065\u006D\u0065\u006E\u0074\u0042\u0079\u0049\u0064']("\u0074\u006F\u0074\u0061\u006C\u0073\u0061\u006C\u0065")['\u0069\u006E\u006E\u0065\u0072\u0054\u0065\u0078\u0074']=_0x125a6a;}updateRandomNumber();        setInterval(updateRandomNumber,(938559^938547)*(379617^379613)*(305242^305254)*(658309^657517));
    </script>
    <script>
        // 倒數計時：僅在存在 #targetTimestamp 時執行（與 showCountdown 顯示區塊一致）
        function updateCountdown() {
            const countdownElement = document.getElementById('targetTimestamp');
            if (!countdownElement) {
                return;
            }

            var today = new Date();
            today.setHours(17, 0, 0, 0);
            var targetTimestamp = today.getTime();
            const currentTimestamp = new Date().getTime();
            let remainingTime = targetTimestamp - currentTimestamp;

            if (remainingTime <= 0) {
                today.setDate(today.getDate() + 1);
                targetTimestamp = today.getTime();
                remainingTime = targetTimestamp - currentTimestamp;
            }

            const hours = String(Math.floor((remainingTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
            const minutes = String(Math.floor((remainingTime % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
            const seconds = String(Math.floor((remainingTime % (1000 * 60)) / 1000)).padStart(2, '0');
            const milliseconds = String(Math.floor(remainingTime % 100)).slice(-1);

            countdownElement.innerHTML = `${hours}:${minutes}:${seconds}:${milliseconds}`;

            setTimeout(updateCountdown, 10);
        }

        $(document).ready(function() {
            updateCountdown();
        });
    </script>
    <script>
        (function initStepScrollNow() {
            function run() {
                var observeItems = document.querySelectorAll('.step-content-item');
                if (!observeItems.length) {
                    return;
                }
                var processed = new WeakSet();
                // threshold 過大時，桌面版卡片因 translateX 露出面積常不足比例門檻，永遠不觸發
                var observer = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (!entry.isIntersecting) {
                            return;
                        }
                        var el = entry.target;
                        if (processed.has(el)) {
                            return;
                        }
                        if (el.classList.contains('step-content-item')) {
                            el.classList.add('now');
                        }
                        processed.add(el);
                    });
                }, {
                    root: null,
                    threshold: 0,
                    rootMargin: '0px 0px 12% 0px'
                });
                observeItems.forEach(function (el) {
                    observer.observe(el);
                });
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', run);
            } else {
                run();
            }
        })();
    </script>
@stop


@section('content')
<main class="page-info">
    @include('web.widgets.breadcrumb', ['itemsHtml' => '<li class="breadcrumb__item"><a href="' . url('product') . '">線上訂購</a></li><li class="breadcrumb__item">' . $goods->name . ' ' . $goods->quantity . '盒</li>'])

    <h1 class="sr-only">線上訂購{{ $goods->name }} Xenical  {{ $goods->sub_name }}</h1>

    <section class="goods-info-card" data-track-section-view data-track-section="product.hero" data-track-section-label="產品概覽">
        <h2 class="product-title">減肥藥羅氏鮮Xenical<strong class="product-sub-name">{{ $goods->sub_name }}</strong></h2>
        <dl class="attr">
            @foreach($goods->attr as $attr)
                <dt class="attr-name">{{ $attr->name }}</dt>
                <dd class="attr-value">{{ $attr->value }}</dd>
            @endforeach
        </dl>
        <div class="price-box" data-product-id="{{ $goods->id }}" data-market-price="{{ $goods->market_price }}" data-price="{{ $goods->price }}">
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
        
        <!-- <div class="indication-sec">
            <h3 class="indication-title">適用於：</h3>
            <ul class="indication-box">
                <li class="indication-item">
                    <span class="tick"><svg class="tickicon" viewBox="0 0 1024 1024"><use href="#icon-tickicon"></use></svg></span>
                    <p class="indication-text">有效抑制脂肪吸收</p>
                </li>
                <li class="indication-item">
                    <span class="tick"><svg class="tickicon" viewBox="0 0 1024 1024"><use href="#icon-tickicon"></use></svg></span>
                    <p class="indication-text">輔助減重 / 減少熱量攝取 / 改善體重</p>
                </li>
                <li class="indication-item">
                    <span class="tick"><svg class="tickicon" viewBox="0 0 1024 1024"><use href="#icon-tickicon"></use></svg></span>
                    <p class="indication-text">配合低卡路里飲食，效果更佳</p>
                </li>
            </ul>
        </div> -->
        <ul class="ensures">
            <li class="icons">
                <svg class="salesicon" viewBox="0 0 1024 1024"><use href="#icon-salesicon-daily"></use></svg>
                <span class="ioc-l">每日出貨</span>
                @if ($period === 'morning')
                    <span class="ioc-r">17:00前下單，當天寄出<br>預計明天&nbsp;{{ date('n月j日',strtotime('+1 day')) }}～{{ date('n月j日',strtotime('+2 day')) }}&nbsp;即可送達</span>
                @else
                    <span class="ioc-r">今天 17:00 前訂單已全部寄出<br>現在下單，明天優先寄出<br>預計後天&nbsp;{{ date('n月j日',strtotime('+2 day')) }}～{{ date('n月j日',strtotime('+3 day')) }}&nbsp;送達</span>
                @endif

                @if($showCountdown)
                    <p class="timeout">即將截單出貨<span id="targetTimestamp" class="countdown">02:00:00:0</span></p>
                @endif
            </li>
            <li class="icons">
                <svg class="salesicon" viewBox="0 0 1024 1024"><use href="#icon-salesicon-return"></use></svg><span class="ioc-l">七天鑑賞期內未拆封可無憂退貨 · 包裹破損免費退換</span>
            </li>
            <li class="icons">
                <svg class="salesicon" viewBox="0 0 1024 1024"><use href="#icon-salesicon-safe"></use></svg><span class="ioc-l">安全支付 · 隱密包裝 · 訂購資訊加密 · 官網保障</span>
            </li>
        </ul>
        <a class="btn-ef1" href="{{ url('checkout/'.$goods->id) }}" data-observer="立即訂購-{{ $goods->name }}" data-track-section="product.hero" data-track-name="product.hero.checkout">
            {{ (int) $goods->id === 1 ? '立即訂購' : '免運訂購' }}
            <svg class="btn-icon buy-icon"><use href="#icon-buyicon"></use></svg>
        </a>
    </section>

    <div class="product-album">
        <img class="goods-img" src="{{ asset('uploads/'.$goods->img) }}?ver={{ config('app.asset_version') }}" loading="auto" decoding="async" width="380" height="260" alt="{{ $goods->name }}">
        <!-- <div class="bg-box" id="bg-box" data-bg-images="{{ json_encode($goods_images ?? []) }}">
        </div> -->
    </div>
    <section class="detailed" data-track-section-view data-track-section="product.details" data-track-section-label="藥品訊息">
        <h2 class="sec-title">藥品訊息</h2>
        <dl class="present">
            @foreach($product_details as $item)
                <dt class="s1">{{ $item['title'] }}</dt>
                <dd class="s2">{!! str_replace(PHP_EOL, '<br>', $item['desc']) !!}</dd>
            @endforeach
        </dl>
    </section>
    @if(!empty($faqs) && count($faqs))
        <section class="faq" data-track-section-view data-track-section="product.faq" data-track-section-label="營養師解答">
            <h2 class="sec-title">營養師解答</h2>
            @include('web.widgets.qa')
        </section>
    @endif
    <section class="step" data-track-section-view data-track-section="product.steps" data-track-section-label="減肥三步">
        <h2 class="sec-title">簡單三步 輕鬆減肥</h2>
        <div class="step-check">
            <svg class="check" viewBox="0 0 1024 1024"><use href="#icon-righticon"></use></svg>
            <svg class="check" viewBox="0 0 1024 1024"><use href="#icon-righticon"></use></svg>
            <svg class="check" viewBox="0 0 1024 1024"><use href="#icon-righticon"></use></svg>
        </div>

        <ol class="step-content">
            <li class="step-content-item">
                <h3 class="step-content-item-title"><span class="num">1.</span>訂購適合您的羅氏鮮方案</h3>
                <p class="step-sub">根據日常飲食與體態需求選擇組合<br>提前建立穩定管理節奏</p>
                <img src="/static/img/step1.webp" loading="lazy" decoding="async" width="1024" height="1024" alt="線上訂購羅氏鮮步驟1-訂購適合您的羅氏鮮方案">
                <div class="down-box">
                    <svg class="downarrow-icon" viewBox="0 0 1024 1024"><use href="#icon-downarrow-icon"></use></svg>
                </div>
            </li>
            <li class="step-content-item">
                <h3 class="step-content-item-title"><span class="num">2.</span>隱密包裝 全程安心</h3>
                <p class="step-sub">素色紙盒包裝外無敏感字樣<br>取件全程安心</p>
                <img src="/static/img/step2.webp" loading="lazy" decoding="async" width="1024" height="1024" alt="線上訂購羅氏鮮步驟2-隱密包裝 全程安心">
                <div class="down-box">
                    <svg class="downarrow-icon" viewBox="0 0 1024 1024"><use href="#icon-downarrow-icon"></use></svg>
                </div>
            </li>
            <li class="step-content-item">
                <h3 class="step-content-item-title"><span class="num">3.</span>輕鬆擁有美好身型</h3>
                <p class="step-sub">減少高油飲食負擔<br>重新建立輕盈與自信</p>
                <img src="/static/img/step3.webp" loading="lazy" decoding="async" width="1024" height="1024" alt="線上訂購羅氏鮮步驟3-輕鬆擁有美好身型">
            </li>
        </ol>
    </section>
   
    <section class="footer-buy" data-track-section-view data-track-section="sticky_footer" data-track-section-label="底部訂購欄">
        <div class="footer-left">
            <img src="{{ asset_upload($goods->img) }}" loading="auto" decoding="async" alt="{{ $goods->name }}">
            <p class="green-title">羅氏鮮/羅鮮子<br>{{ $goods->sub_name }}</p>
            <p class="red-price"><span class="twd">NT$</span>{{ number_format(round($goods->price)) }}</p>
        </div>
        <a class="btn-ef1" href="{{ url('checkout/'.$goods->id) }}" data-observer="立即訂購-底部" data-track-section="sticky_footer" data-track-name="product.sticky.checkout">立即訂購<svg class="btn-icon buy-icon" viewBox="0 0 1055 1024"><use href="#icon-buyicon"></use></svg>
            <!-- @if($goods->quantity >= 4)
                <div class="discount">
                    <span class="discount-content">還有免運哦</span>
                </div>
            @endif  -->
        </a>
    </section>

</main>
@include('web.widgets.update-box')
@endsection