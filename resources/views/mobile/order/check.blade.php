@extends('mobile.layout')

@section('style')
    @parent
    <link rel="stylesheet" href="{{ asset('static/mobile/less/check.css') }}?ver={{ config('app.asset_version') }}">
@stop

@section('script')
    @parent
    <script src="{{ asset('static/js/sweetalert2.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('static/js/jquery.form.js') }}?ver={{ config('app.asset_version') }}"></script>
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
    </script>
    <script>
        $(window).scroll(function() {
            bgEffect()

        });

        function bgEffect(){
            let top = document.scrollingElement.scrollTop; //触发滚动条，记录数值
            let banner_height = $('.fixed-bg').height()-130;
            let opacity = 1-top/banner_height;
            $('.fixed-bg').css('opacity',opacity);
            if(opacity<=0.6){
                $('.page-title').css('color','rgba(0,0,0,'+top/banner_height+')');
            }else{
                $('.page-title').css('color','#fff');
            }

            if(($(window).scrollTop() + $(window).height()).toFixed(0) == $(document).height()){
                $('.fixed-bg').css('opacity',0);
            }

        }
    </script>
    <script>
        submit('#check-form')
    </script>
@stop

@section('content')

    <section class="check-container" data-track-section="order_check" data-track-section-view data-track-section-label="訂單追蹤">
        <div class="fixed-bg" style="background-image: url('{{ asset_upload(app('cache.config')->get('page_check_back_img')) }}')">
            <div class="mask"></div>
            <p class="slogan">{!! app('cache.config')->get('page_check_title') !!}</p>
        </div>
        <div class="page-main">
            <div class="page-head">
                <p class="beat"><i class="iconfont">&#xe784;</i></p>
                <h1 class="page-title">訂單追蹤</h1>
            </div>
            <div class="page-body">
                <p class="describe">
                    {!! app('cache.config')->get('page_check_desc') !!}
                </p>
                <div class="form-pack">
                    <form action="" id="check-form" method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>訂單行動電話：</label>
                            <input class="form-control" type="tel" data-validate="required:請輸入你的訂單電話|mobile:電話格式錯誤" name="phone" placeholder="請輸入訂購時留下的電話號碼">
                        </div>
                        <div class="form-group">
                            <label>訂單電子信箱：</label>
                            <input class="form-control" type="email" data-validate="required:請輸入訂單電子信箱|email:電子信箱格式錯誤" name="email" placeholder="請輸入訂購時留下的電子信箱">
                        </div>
                        <div class="form-group">
                            <label>我不是機器人：</label>
                            <div class="inline">
                                <input class="form-control captcha_code" data-validate="required:請輸入驗證碼" type="text" name="captcha_code" placeholder="請輸入驗證碼">
                                <div class="code"><img class="thumbnail captcha mt-3 mb-2" src="{{ captcha_src('flat') }}" onclick="this.src='/captcha/flat?'+Math.random()" title="點擊圖片重新獲取驗證碼" loading="lazy" decoding="async"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button class="form-btn" type="submit" data-track-section="order_check" data-track-name="order_check.submit" data-observer="查單提交">確認送出</button>
                        </div>
                    </form>
                </div>
            </div>
            {{--
            <div class="foot">
                {!! app('cache.config')->get('page_check_foot') !!}
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
    </section>

@endsection
