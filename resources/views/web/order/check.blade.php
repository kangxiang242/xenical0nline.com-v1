@extends('web.layout')

@section('style')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('static/less/check.css') }}?ver={{ config('app.asset_version') }}"/>
@stop

@section('script')
    @parent
    <script src="{{ asset('static/js/xie.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('static/js/api.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script>

        setInterval(function(){
            if(checkVerify() == true){
                $('.form-btn').addClass('activate-btn');
            }else{
                $('.form-btn').removeClass('activate-btn');
            }

        },1000);
        function checkVerify(){
            var phone = $("input[name='phone']").val();
            var email = $("input[name='email']").val();
            var captcha_code = $("input[name='captcha_code']").val();

            if(!phone){
                return false;
            }
            if(!(/^09\d{8}$/.test(phone))){
                return false;
            }
            if(!email){
                return false;
            }
            if(email.search(/^([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+\.(?:com|cn|tw|info|net)$/) == -1){
                return false;
            }
            if(!captcha_code){
                return false;
            }
            return true;
        }

        bgHeight()
        function bgHeight(){
            $('.container-bg').css('height',$(window).height()-80);

        }
        window.onresize = function(){
            bgHeight()
        }

        $(window).scroll(function() {
            bgEffect()

        });

        function bgEffect(){
            let top = document.scrollingElement.scrollTop; //触发滚动条，记录数值
            let banner_height = $('.container-bg').height()-60;
            let opacity = 1-top/banner_height;
            $('.container-bg').css('opacity',opacity);
            if(opacity<=0.6){
                $('.page-title').css('color','rgba(0,0,0,'+top/banner_height+')');
            }else{
                $('.page-title').css('color','#fff');
            }

            if(($(window).scrollTop() + $(window).height()).toFixed(0) == $(document).height()){
                $('.container-bg').css('opacity',0);
            }



        }
    </script>

@stop


@section('content')
    <section class="check-container" data-track-section="order_check" data-track-section-view data-track-section-label="訂單追蹤">
        <div class="container-bg" style="background-image: url('{{ asset_upload(app('cache.config')->get('page_check_back_img_pc')) }}')">
            <p class="bg-text">{!! app('cache.config')->get('page_check_title') !!}</p>
            <p class="beat"><i class="iconfont">&#xe784;</i></p>
        </div>

        <div class="check-wrap">
            <div class="page-main">
                <h1 class="page-title">訂單追蹤</h1>
            </div>
            <div class="check-main">

                <p class="desc">
                    {!! app('cache.config')->get('page_check_desc') !!}
                </p>

                <div class="form-main">
                    <form action="" id="check-form" method="post" onsubmit="return orderCheck()">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>訂單行動電話：</label>
                            <input class="form-control" type="tel" name="phone" placeholder="請輸入訂購時留下的電話號碼" maxlength="10">
                        </div>
                        <div class="form-group">
                            <label>訂單電子信箱：</label>
                            <input class="form-control" type="email" name="email" placeholder="請輸入訂購時留下的電子信箱">
                        </div>
                        <div class="form-group">
                            <label>我不是機器人：</label>
                            <input class="form-control" type="text" name="captcha_code" placeholder="請輸入驗證碼">
                            <div class="code"><img class="thumbnail captcha mt-3 mb-2" src="{{ captcha_src('flat') }}" onclick="this.src='/captcha/flat?'+Math.random()" title="點擊圖片重新獲取驗證碼" loading="lazy" decoding="async"></div>
                        </div>

                        <div class="form-group">
                            <button class="form-btn" type="submit" data-track-section="order_check" data-track-name="order_check.submit" data-observer="查單提交">確認送出</button>
                        </div>
                    </form>
                </div>
                {{--
                <div class="foot">
                    {!! app('cache.config')->get('page_check_foot') !!}
                </div>
                --}}
                {{--
                <div class="foot">

                    <article class="foot-card">
                        <h3 class="foot-title">如何查詢訂單</h3>

                        <ul class="foot-list">
                            <li>可透過下單時填寫的手機號碼與電子信箱查詢訂單狀態。</li>
                            <li>請確認輸入資訊與下單資料一致，以避免查詢失敗。</li>
                        </ul>
                    </article>

                    <article class="foot-card">
                        <h3 class="foot-title">查不到訂單怎麼辦？</h3>

                        <ul class="foot-list">
                            <li>手機號碼或電子信箱輸入錯誤。</li>
                            <li>訂單尚未同步至系統，請稍後再試。</li>
                            <li>網路異常或付款未完成，可能導致訂單未成功建立。</li>
                        </ul>
                    </article>

                    <article class="foot-card">
                        <h3 class="foot-title">物流狀態更新說明</h3>

                        <ul class="foot-list">
                            <li>訂單成立後，系統會依序進行確認、備貨、出貨與物流配送。</li>
                            <li>因物流系統同步需要時間，配送狀態可能會有些許延遲。</li>
                        </ul>
                    </article>

                    <article class="foot-card">
                        <h3 class="foot-title">配送時間說明</h3>

                        <ul class="foot-list">
                            <li>訂單將於週一至週六 09:00－18:00 依序處理與安排出貨。</li>
                            <li>配送方式包含黑貓宅急便與 7-ELEVEN 超商取貨。</li>
                            <li>實際配送時間仍依物流與配送地區安排為準。</li>
                        </ul>
                    </article>

                </div>
                --}}
                @php
                    $qaList = json_decode(app('cache.config')->get('page_check_qa', '[]'), true);
                @endphp
                @if ($qaList)
                <div class="order-help">
                    @foreach ($qaList as $item)
                    <article class="order-help-card">
                        <div class="order-help-icon">
                            <svg viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                        </div>
                        <h3>{{ $item['title'] ?? '' }}</h3>
                        @php $lines = explode("\n", $item['content'] ?? ''); @endphp
                        @if ($lines)
                        <ul>
                            @foreach ($lines as $line)
                            @if (trim($line))
                            <li>{{ trim($line) }}</li>
                            @endif
                            @endforeach
                        </ul>
                        @endif
                    </article>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </section>
@endsection
