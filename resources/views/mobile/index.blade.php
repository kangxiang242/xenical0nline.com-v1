@extends('mobile.layout')

@section('style')
    @parent
    <link rel="stylesheet" href="{{ asset('static/mobile/less/index.css') }}?ver={{ config('app.asset_version') }}">
    <link rel="stylesheet" href="{{ asset('static/swiper4/swiper.min.css') }}?ver={{ config('app.asset_version') }}">
    <style>
        /*.swiper-container {
            height: 100vh;
        }
*/
        .swiper-slide {
            overflow: hidden;
        }

        .slide-inner {
            position: absolute;
            width: 100%;
            height: 100%;
            left: 0;
            top: 0;
            background-size: cover;
            background-position: center;
        }

        .splitting.-aos-active .char {
            -webkit-animation: splitting 1.2s cubic-bezier(.245,.495,0,.99) forwards;
            animation: splitting 1.2s cubic-bezier(.245,.495,0,.99) forwards;
            -webkit-animation-delay: calc(30ms*var(--char-index));
            animation-delay: calc(30ms*var(--char-index))
        }

        .splitting .word {
            display: inline-block;
            overflow: hidden;
            width: 100%;

        }

        .splitting .char {
            display: inline-block;
            -webkit-transform: translate3d(0,100%,0);
            transform: translate3d(0,100%,0);
            opacity: 0
        }

        @-webkit-keyframes splitting {
            to {
                opacity: 1;
                -webkit-transform: translate3d(0,0,0);
                transform: translate3d(0,0,0)
            }
        }

        @keyframes splitting {
            to {
                opacity: 1;
                -webkit-transform: translate3d(0,0,0);
                transform: translate3d(0,0,0)
            }
        }

        @-webkit-keyframes splitting-in {
            0% {
                opacity: 1;
                -webkit-transform: translate3d(0,100%,0);
                transform: translate3d(0,100%,0)
            }

            to {
                opacity: 1;
                -webkit-transform: translate3d(0,0,0);
                transform: translate3d(0,0,0)
            }
        }

        @keyframes splitting-in {
            0% {
                opacity: 1;
                -webkit-transform: translate3d(0,100%,0);
                transform: translate3d(0,100%,0)
            }

            to {
                opacity: 1;
                -webkit-transform: translate3d(0,0,0);
                transform: translate3d(0,0,0)
            }
        }

        @-webkit-keyframes splitting-out {
            0% {
                opacity: 1;
                -webkit-transform: translate3d(0,0,0);
                transform: translate3d(0,0,0)
            }

            to {
                opacity: 0;
                -webkit-transform: translate3d(0,-100%,0);
                transform: translate3d(0,-100%,0)
            }
        }

        @keyframes splitting-out {
            0% {
                opacity: 1;
                -webkit-transform: translate3d(0,0,0);
                transform: translate3d(0,0,0)
            }

            to {
                opacity: 0;
                -webkit-transform: translate3d(0,-100%,0);
                transform: translate3d(0,-100%,0)
            }
        }



        .text-animation-main {
            width: 100%;
            height: 100%;

            top: 0;
            left: 0;
            display: block;
            opacity: 0;
            -webkit-transition: opacity 3s;
            transition: opacity 3s
        }

        .text-animation-main .splitting.-aos-active .char {
            -webkit-transform: translate(0) scaleY(1) rotateX(0) rotate(0);
            transform: translate(0) scaleY(1) rotateX(0) rotate(0);
            -webkit-animation: none;
            animation: none;
            -webkit-animation-delay: calc(30ms*var(--char-index));
            animation-delay: calc(30ms*var(--char-index))
        }

        .text-animation-main.-show {
            opacity: 1;
            z-index: 2;
            -webkit-transition: opacity 2s;
            transition: opacity 2s;
            pointer-events: all
        }

        .text-animation-main.-show .splitting.-aos-active .char {
            opacity: 0;
            -webkit-animation: splitting-in 1.2s cubic-bezier(.99,0,.755,.505) forwards;
            animation: splitting-in 1.2s cubic-bezier(.99,0,.755,.505) forwards;
            -webkit-animation-delay: calc(30ms*var(--char-index));
            animation-delay: calc(30ms*var(--char-index))
        }

        .text-animation-main:not(.-show) {
            pointer-events: none;
            z-index: 1
        }

        .text-animation-main:not(.-show) .splitting.-aos-active .char {
            opacity: 1;
            -webkit-animation: splitting-out .8s cubic-bezier(.99,0,.755,.505) forwards;
            animation: splitting-out .8s cubic-bezier(.99,0,.755,.505) forwards;
            -webkit-animation-delay: calc(30ms*var(--char-index));
            animation-delay: calc(30ms*var(--char-index))
        }
        .text-effect .p2{
            text-indent: -0.2rem;
        }
        .text-effect .p2 .splitting .word .char{
            padding: 0 0.4rem;
        }

    </style>
@stop

@section('script')
    @parent
    <script src="{{ asset('static/swiper4/swiper.min.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('static/js/jquery.textAnimation.min.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('static/js/jquery.waypoints.min.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('static/js/jquery.marquee.min.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('static/js/jquery.parallax.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('static/js/countUp.min.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script>
        textAnimation("#text-banner-0 #banner-p1");
        textAnimation("#text-banner-0 #banner-p2");
        textAnimation("#text-banner-0 #banner-p3");
        textAnimation("#text-banner-0 #banner-p4");

        textAnimation("#text-banner-1 #banner-p1");
        textAnimation("#text-banner-1 #banner-p2");
        textAnimation("#text-banner-1 #banner-p3");
        textAnimation("#text-banner-1 #banner-p4");

        textAnimation("#text-banner-2 #banner-p1");
        textAnimation("#text-banner-2 #banner-p2");
        textAnimation("#text-banner-2 #banner-p3");


        function textAnimation(elem){
            var text = $(elem).text();
            var textArr = text.split('');
            var html = '<span class="text-animation-main"><span class="splitting -aos-active"><span class="word">';
            for(j = 0; j < textArr.length; j++) {
                html += '<span class="char" style="--char-index:'+j+';">'+textArr[j]+'</span>';
            }
            html += '</span></span></span>';
            $(elem).html(html);
        }
    </script>
    <script>
        var interleaveOffset = 0.5;
        var bannerImageScale=1.1;
        var swiperOptions = {
            allowTouchMove: true,
            autoplay: {
                delay: 5500,
                disableOnInteraction: false
            },
            grabCursor: true,
            watchSlidesProgress: true,
            mousewheelControl: true,
            speed: 1000,
            loop: true,
            pagination: {
                el: '.progress',

                renderBullet: function (index, className) {
                    return '<div class="bar ' + className + '"></div>';
                },
            },
            on: {

                slideChange: function(){

                    var eq = this.activeIndex;

                    var elem = $(this.slides[eq]).find(".slide-inner").attr('data-bind-text');
                    $(this.slides[eq]).find(".slide-inner video")[0].play()
                    $('#'+elem).find('.text-animation-main').addClass('-show');
                    $('#'+elem).siblings().find('.text-animation-main').removeClass('-show');
                },
                progress: function() {
                    var swiper = this;
                    for (var i = 0; i < swiper.slides.length; i++) {
                        var slideProgress = swiper.slides[i].progress;
                        var innerOffset = swiper.width * interleaveOffset;
                        var innerTranslate = slideProgress * innerOffset;

                        var innerScaleOffset = Math.abs(1 - bannerImageScale);
                        var innerScale = Math.abs(slideProgress * innerScaleOffset) + 1;
                        //swiper.slides[i].querySelector(".slide-inner").style.transform = "translate3d(".concat(innerTranslate, "px, 0, 0) scale(").concat(innerScale, ")");
                        swiper.slides[i].querySelector(".slide-inner").style.transform =
                            "translate3d(" + innerTranslate + "px, 0, 0)";
                    }
                },
                touchStart: function() {
                    var swiper = this;
                    for (var i = 0; i < swiper.slides.length; i++) {
                        swiper.slides[i].style.transition = "";
                    }
                },
                setTransition: function(speed) {
                    var swiper = this;
                    for (var i = 0; i < swiper.slides.length; i++) {
                        swiper.slides[i].style.transition = speed + "ms";
                        swiper.slides[i].querySelector(".slide-inner").style.transition = speed + "ms";
                    }
                }
            }
        };

        var swiper = new Swiper("#swiper-video3", swiperOptions);



        var Swiper5 = new Swiper('#swiper5', {
            effect: 'coverflow',
            loop: true,
            slidesPerView: "auto",
            centeredSlides: true, //设置slide居中

            coverflow: {
                rotate: 50, //slide做3d旋转时Y轴的旋转角度。默认50。
                stretch: 0, //每个slide之间的拉伸值（距离），越大slide靠得越紧。 默认0。
                depth: 80, //slide的位置深度。值越大z轴距离越远，看起来越小。 默认100。
                modifier: 1, //depth和rotate和stretch的倍率，相当于            depth*modifier、rotate*modifier、stretch*modifier，值越大这三个参数的效果越明显。默认1。
                slideShadows: true //开启slide阴影。默认 true。
            },

        })
/*        var mySwiper = new Swiper('#swiper5',{
            effect : 'coverflow',
            loop : true,
            slidesPerView: 3,
            centeredSlides: true,
            coverflowEffect: {
                rotate: 50,
                stretch: 0,
                depth: 80,
                modifier: 1,
                slideShadows : true
            },
        })*/

    </script>
    <script>
        var is_epilogue_waypoints = false;
        $('.epilogue-section').waypoint(function(direction) {

            if(is_epilogue_waypoints === false){
                is_epilogue_waypoints = true;
                $('.epilogue-section .p1').textAnimation({
                    speed: 600,
                    delay: 100,
                    left: 50,
                    top: 50,
                    scale: 1,
                    rotateY: 0,
                    rotateX: 0,
                    translateZ: 1000,
                    letterSpacing: '10px',
                    easing: "cubic-bezier(0.290, 0.350, 0.460, 1.200)",
                    backgroundColor: "transparent",
                    isRandomScale: false,
                    isRandomPosition: false,
                    isRandomRotateY: false,
                    isRandomRotateX: false,
                    isRandomTranslateZ: false,
                    isRandomSpeed: false,
                    isRandomDelay: false});
                $('.epilogue-section .p2').textAnimation({
                    speed: 600,
                    delay: 100,
                    left: 50,
                    top: 50,
                    scale: 1,
                    rotateY: 0,
                    rotateX: 0,
                    translateZ: 1000,
                    letterSpacing: '10px',
                    easing: "cubic-bezier(0.290, 0.350, 0.460, 1.200)",
                    backgroundColor: "transparent",
                    isRandomScale: false,
                    isRandomPosition: false,
                    isRandomRotateY: false,
                    isRandomRotateX: false,
                    isRandomTranslateZ: false,
                    isRandomSpeed: false,
                    isRandomDelay: false});

            }


        }, {
            offset: '70%'
        })

        setTimeout(function(){

            $('#slogan-text').textAnimation({
                speed: 600,
                delay: 100,
                left: 50,
                top: 50,
                scale: 1,
                rotateY: 0,
                rotateX: 0,
                translateZ: 1000,
                letterSpacing: '10px',
                easing: "cubic-bezier(0.290, 0.350, 0.460, 1.200)",
                backgroundColor: "transparent",
                isRandomScale: false,
                isRandomPosition: false,
                isRandomRotateY: false,
                isRandomRotateX: false,
                isRandomTranslateZ: false,
                isRandomSpeed: false,
                isRandomDelay: false});
        },1000)
    </script>

    <script>
        $('.question .q-desc').each(function(){
            var height = $(this).outerHeight();
            $(this).css({
                'height':0,
                '--height':height+"px",
            });
        });
        $('.question .q-title').click(function(){
            if($(this).next('.q-desc').hasClass('-show')){
                $(this).next('.q-desc').removeClass('-show');
                $(this).find('.shrink i').html('&#xe775;');

            }else{
                $(this).next('.q-desc').addClass('-show');
                $(this).find('.shrink i').html('&#xeca2;');
            }
        });
    </script>

    <script>

        $(document).ready(function(){
            const $ScrollWrap = $(window)
            // 监听滚动停止
            let t1 = 0;
            let t2 = 0;
            let timer = null; // 定时器
            $ScrollWrap.on("touchstart", function(){

                // 触摸开始 ≈ 滚动开始
            })
            $ScrollWrap.on("scroll", function(){
                //$('.right-online-buy').addClass('slipOut')

                // 滚动
                clearTimeout(timer)
                timer = setTimeout(isScrollEnd, 300)
                t1 = $ScrollWrap.scrollTop()
                if(t1<=0){

                }else{

                }
            })
            function isScrollEnd() {
                t2 = $ScrollWrap.scrollTop();
                if(t2<=0){
                    //$('.right-online-buy').addClass('slipOut')
                    //$('.right-online-buy a').css('opacity',0);
                }else{
                    if(t2 == t1){
                        //$('.right-online-buy a').css('opacity',1);
                        //$('.right-online-buy').removeClass('slipOut')

                        clearTimeout(timer)
                    }
                }

            }


            var roll_status = 0;
            headerEffect()
            initialize();
            $(window).scroll(function() {
                headerEffect()
            });
            function headerEffect(){
                let top = document.scrollingElement.scrollTop; //触发滚动条，记录数值
                let banner_height = $('.video-wrap').height()-40;
                let opacity = 1-(1-top/banner_height);
                if(opacity >= 1){
                    opacity = 1;
                }

                if(top>10){
                    $('header').addClass('-show')
                }else{
                    $('header').removeClass('-show')
                }


                if(top<=0){
                    //$('.online-buy a').css('opacity',1)
                    //$('.online-buy').removeClass('slipOut')

                    logo_compose(1,1);

                }else{
                    //$('.online-buy').addClass('slipOut')

                    logo_compose(2,opacity);
                }
            }

            function initialize(){
                let top = document.scrollingElement.scrollTop; //触发滚动条，记录数值
                /* if(top<=0){
                    $('.online-buy').removeClass('slipOut')
                    $('.right-online-buy a').css('opacity',0)

                    $('.online-buy a').css('opacity',1)
                }else{
                    $('.online-buy').addClass('slipOut')
                    $('.right-online-buy a').css('opacity',1)
                    $('.online-buy a').css('opacity',0)

                } */
            }

        })

        function logo_compose(type,opacity){
            if(type == 1){
                $('.logo-img').attr('src','/static/img/mlogo1.webp?ver=1')
            }else{
                $('.logo-img').attr('src','/static/img/mlogo2.webp?ver=1')
            }
        }

        $('#use-num').waypoint(function(direction) {
            let demo = new CountUp('use-num',0, 100000,0,2,{
                useEasing: true,
                useGrouping: true,
            });
            demo.start();

        }, {
            offset: '100%'
        })
    </script>
    <script>
        $('#loopWrap').marquee({
            //duration in milliseconds of the marquee

            speed:30,
            //gap in pixels between the tickers
            gap: 0,
            //time in milliseconds before the marquee will start animating
            delayBeforeStart: 0,
            //'left' or 'right'
            direction: 'left',
            //true or false - should the marquee be duplicated to show an effect of continues flow
            duplicated: true,
            pauseOnHover:true,
            startVisible:true,

        });
    </script>
    <script>
        $(".epilogue-img").parallax({
            speed:20,
            delay: 1000,
            deviation:200,
        });
    </script>

@stop

@section('content')

    {{--<div class="right-online-buy">
        <a href="{{ url('product') }}"><i class="iconfont">&#xe811;</i>線上訂購</a>
    </div>--}}
    <section class="adv-section" data-track-section="hero" data-track-section-view data-track-section-label="首屏">
        <div class="act-main">
            <div class="video-wrap" style="height: 100vh">
                <div class="swiper-container" id="swiper-video3">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="slide-inner" data-bind-text="text-banner-0">
                                <video style="object-fit:cover" loop="" muted="" width="100%" height="100%" playsinline="" preload="none" poster="/static/video/poster1-m.webp">
                                    <source src="{{ asset('static/video/m1.mp4') }}" type="video/mp4">
                                </video>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="slide-inner" data-bind-text="text-banner-1">
                                <video style="object-fit:cover" loop="" muted="" width="100%" height="100%" playsinline="" preload="none" poster="/static/video/poster2-m.webp">
                                    <source src="{{ asset('static/video/m2.mp4') }}" type="video/mp4">
                                </video>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="slide-inner" data-bind-text="text-banner-2">
                                <video style="object-fit:cover"  loop="" muted="" width="100%" height="100%" playsinline="" preload="none" poster="/static/video/poster3-m.webp">
                                    <source src="{{ asset('static/video/m3.mp4') }}" type="video/mp4">
                                </video>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="shade-mask"></div>

                <div class="video-slogan">
                    <div class="text-effect" id="text-banner-0">
                        <p class="p1" id="banner-p1">{{ app('cache.config')->get('home_banner_0_title', '安全減肥') }}</p>
                        <p class="p2" id="banner-p2">{{ app('cache.config')->get('home_banner_0_title_en', 'Safety') }}&nbsp;</p>
                        <p class="p3" id="banner-p3">美國FDA、歐盟EMA等權威認證</p>
                        <p class="p3" id="banner-p4">僅作用於腸道、對人體安全</p>
                    </div>

                    <div class="text-effect" id="text-banner-1" >
                        <p class="p1" id="banner-p1">{{ app('cache.config')->get('home_banner_1_title', '有效減肥') }}</p>
                        <p class="p2" id="banner-p2">{{ app('cache.config')->get('home_banner_1_title_en', 'Effective') }}&nbsp;</p>
                        <p class="p3" id="banner-p3">歐盟核准上市28年</p>
                        <p class="p3" id="banner-p4">醫師首選口服合法減肥藥</p>
                    </div>

                    <div class="text-effect" id="text-banner-2" >
                        <p class="p1" id="banner-p1">{{ app('cache.config')->get('home_banner_2_title', '健康減肥') }}</p>
                        <p class="p2" id="banner-p2">{{ app('cache.config')->get('home_banner_2_title_en', 'Healthy') }}&nbsp;</p>
                        <p class="p3" id="banner-p3">{{ app('cache.config')->get('home_banner_2_desc', '無須斷食動刀，健康排出油脂') }}</p>
                    </div>
                </div>

                <div class="pill1"></div>
                <div class="pill2"></div>


                <div class="progress"></div>
            </div>
            <div class="slogan-wrap">
                <div class="back-img"><img src="{{ asset('static/img/ellipse.webp') }}" alt="" loading="lazy" decoding="async"></div>
                <p class="text" id="slogan-text"> 妳滿意現在的身材嗎？</p>
            </div>
        </div>

        <div class="about-main">
            <div class="head wow animate__animated animate__fadeInUp">
                <h1 class="title">{!! app('cache.config')->get('home_about_title') !!}</h1>
                <p class="sub">WHAT IS XENICAL</p>
            </div>
            <div class="text wow animate__animated animate__fadeInUp">
                {!! app('cache.config')->get('home_about') !!}
            </div>
            <div class="cumulative wow animate__animated animate__fadeInUp">
                <p class="p1">銷量超過</p>
                <p class="p2"><span class="num" id="use-num">100,000</span><span class="em">萬億顆<br>以上</span></p>
            </div>
        </div>
    </section>

    <section class="section suit-section" data-track-section="suit" data-track-section-view data-track-section-label="适用对象">
        <div class="head wow animate__animated animate__fadeInUp">
            <h2 class="title">適用族群</h2>
            <p class="sub">FOR WHO</p>
        </div>
        <div class="main wow animate__animated animate__fadeInUp">
            @foreach($for_people as $item)
                <div class="item">
                    <div class="box">
                        <img src="{{ asset('uploads/'.($item->img ?? '')) }}" alt="{{ $item->text }}" loading="lazy" decoding="async">
                    </div>
                    <p class="text">{{ $item->text }}</p>
                </div>
            @endforeach
        </div>
    </section>


    <section class="section affect-section" data-track-section="how" data-track-section-view data-track-section-label="作用原理">
        <div class="head wow animate__animated animate__fadeInUp">
            <h2 class="title">羅氏鮮作用機轉</h2>
            <p class="sub">HOW TO WORK</p>
        </div>
        <div class="main ">
            <div class="row ">
                <div class="picker wow animate__animated animate__fadeInUp key-1">
                    <span class="min-fat left-more" style="left: 12.3rem;top: 7.7rem;"></span>
                    <span class="min-fat left-more" style="left: 13.2rem;top: 11.6rem;"></span>
                    <span class="min-fat right-more" style="right: 12.1rem;top: 8.6rem;"></span>
                    <span class="min-fat right-more" style="right: 12.3rem;top: 6.2rem;"></span>
                </div>
                <div class="introduce wow animate__animated animate__fadeInUp">
                    {!! app('cache.config')->get('how_to_work_1') !!}
                </div>
            </div>

            <div class="row">
                <div class="picker wow animate__animated animate__fadeInUp key-2">
                    <span class="max-fat bottom-more" style="bottom: 9.5rem;left: 16rem"></span>
                </div>
                <div class="introduce int-2 wow animate__animated animate__fadeInUp">
                    {!! app('cache.config')->get('how_to_work_2') !!}
                </div>
            </div>
        </div>
        <div class="decorate de-1"></div>
        <div class="decorate de-2"></div>
    </section>

    <section class="section buy-section orange-bg" data-track-section="product" data-track-section-view data-track-section-label="商品方案">
        <div class="head wow animate__animated animate__fadeInUp">
            <h2 class="title">如何訂購羅氏鮮</h2>
            <p class="sub">HOW TO BUY</p>
        </div>
        <div class="main">
            {{--<div class="officina wow animate__animated animate__fadeInUp">
                <div class="card">
                    <p class="title">線下通路</p>
                    <p class="sub">
                        羅氏鮮已上市全台各大藥局<br>
                        統一定價NT$1,800<br>
                        訂購者必須出示醫師處方箋
                    </p>
                </div>
                <div class="drug">藥</div>
            </div>--}}

            <div class="officina wow animate__animated animate__fadeInUp">
                <div class="card">
                    <p class="title">線上通路</p>
                    <p class="sub">
                        台灣羅氏鮮官方線上訂購<br>
                        無須醫師處方箋，歐洲原廠進口<br>
                        訂購組合懶人包可享受超值優惠
                    </p>
                </div>
                <div class="shop-back"><img src="{{ asset('static/img/shop2.webp') }}" alt="線上訂購" loading="lazy" decoding="async"></div>
            </div>

            <div class="product wow animate__animated animate__fadeInUp">
                <div class="card">
                    @foreach($products as $item)
                    <div class="goods">
                        <div class="col">
                            <p class="title">羅氏鮮{{ $item->sub_name }}</p>
                            <p class="price">
                                <span class="now">NT${{ number_format(round($item->price)) }}</span>

                                <span class="jetso">
                                    @if($item->market_price > $item->price)
                                        優惠${{ $item->market_price - $item->price }}
                                    @else
                                        官方標準售價
                                    @endif
                                </span>
                            </p>
                        </div>
                        <div class="col">
                            <a class="checkout-btn" href="{{ url('checkout/'.$item->id) }}" data-track-section="product" data-track-name="home.product.checkout" data-observer="立即訂購-{{ $item->name }}">立即訂購</a>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{--<a class="more" href="{{ url('product') }}">查看剩下10種優惠組合方案</a>--}}
            </div>

        </div>
    </section>

    <section class="section word-section news-section" data-track-section="lunbo" data-track-section-view data-track-section-label="用户见证">
        <div class="head wow animate__animated animate__fadeInUp">
            <h2 class="title">健康減肥瘦身<br>看看他們怎麼說</h2>
            <p class="sub">HOW TO WORK</p>
        </div>
        <div class="main wow animate__animated animate__fadeInUp">
            @if($trade_show)
                <div class="swiper-container" id="swiper5">
                    <div class="swiper-wrapper">
                        @foreach(array_values($trade_show) as $key=>$item)
                            @if($key>5)
                                @break
                            @endif
                                <div class="swiper-slide word-item">
                                    <div class="box"><img src="{{ asset_upload($item['img']) }}" alt="{{ $item['text'] }}" loading="lazy" decoding="async"></div>
                                </div>
                        @endforeach


                    </div>
                </div>
            @endif

        </div>
    </section>

    <section class="section eval-section orange-bg" data-track-section="tdee" data-track-section-view data-track-section-label="计算器">
        <div class="head wow animate__animated animate__fadeInUp">
            <h2 class="title">你瞭解你的身體嗎？</h2>
        </div>
        <div class="main wow animate__animated animate__fadeInUp">
            <div class="text ">
                {!! str_replace(PHP_EOL,'<br>',app('cache.config')->get('slim_about')) !!}
            </div>
            <a class="btn" href="{{ url('compute') }}" data-track-section="tdee" data-track-name="home.tdee.btn" data-observer="測試你的數據按鈕">測試一下你的數據</a>
        </div>
    </section>

    <section class="section solve-section" data-track-section="timeline" data-track-section-view data-track-section-label="时间轴">
        <div class="head wow animate__animated animate__fadeInUp">
            <h2 class="title">我們致力於解決你的困擾</h2>

        </div>
        <div class="main wow animate__animated animate__fadeInUp" id="loopWrap">
            <div class="group">
                @foreach($trouble as $item)
                    <div class="item">
                        <p class="p1">{{ $item->text }}</p>
                        <p class="p2"><span class="num">{{ $item->number }}</span><span class="unit">{{ $item->unit }}</span></p>
                    </div>
                @endforeach

            </div>
        </div>
    </section>

    <section class="section faq-section orange-bg" data-track-section="fqa" data-track-section-view data-track-section-label="FAQ">
        <div class="head wow animate__animated animate__fadeInUp">
            <h2 class="title">營養師解答</h2>
            <p class="sub">Q&A</p>
        </div>
        <div class="main">
            <div class="question wow animate__animated animate__fadeInUp">
                @foreach($faqs as $faq)
                    <div class="item" data-faq-id="{{ $loop->iteration }}">
                        <p class="q-title">Q：{{ $faq->questions }}<a class="shrink" href="javascript:;"><i class="iconfont">&#xe775;</i></a></p>
                        <p class="q-desc">{!! $faq->answers !!}</p>

                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section epilogue-section" data-track-section="epilogue" data-track-section-view data-track-section-label="底部CTA">
        <div class="main">
            <div class="img-wrap wow animate__animated animate__fadeInUp">
                <div class="box epilogue-img" style="background-image: url({{ asset('uploads/'.app('cache.config')->get('promote_image')) }})"></div>
            </div>

            <div class="text">
                <p class="p1">這個夏天</p>
                <p class="p2">你準備好了嗎？</p>
            </div>
        </div>
    </section>

@endsection
