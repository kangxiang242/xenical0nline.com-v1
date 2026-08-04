@extends('mobile.layout')

@section('style')
    @parent
    <link rel="stylesheet" href="{{ asset('static/mobile/less/message.css') }}?ver={{ config('app.asset_version') }}">
@stop

@section('script')
    @parent
    <script src="{{ asset('static/js/sweetalert2.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('static/js/jquery.form.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('static/js/api.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script>

        setInterval(function(){
            if(messageVerify() == true){
                $('.form-btn').addClass('activate-btn');
            }else{
                $('.form-btn').removeClass('activate-btn');
            }

        },1000);
        function messageVerify(){
            var name = $("input[name='name']").val();
            var phone = $("input[name='phone']").val();
            var email = $("input[name='email']").val();
            var content = $("textarea[name='content']").val();
            if(!name){
                return false;
            }
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
            if(!content){
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
            let banner_height = $('.fixed-bg').height()-40;
            let opacity = 1-top/banner_height;
            $('.fixed-bg').css('opacity',opacity);
/*
            if(opacity<=0.6){
                $('.page-title').css('color','rgba(0,0,0,'+top/banner_height+')');
            }else{
                $('.page-title').css('color','#fff');
            }
*/

            if(($(window).scrollTop() + $(window).height()).toFixed(0) == $(document).height()){
                $('.fixed-bg').css('opacity',0);
            }

        }

        $('.fqa li').each(function(){
            var height = $(this).find('.answers').innerHeight();
            $(this).css('--_height',height+'px');

        });
        $('.fqa li').click(function(){
            if($(this).hasClass('show')){
                $(this).removeClass('show');
            }else{
                $(this).addClass('show');
            }
        })
    </script>

    <script>
        submit('#message-form', {});
    </script>
@stop

@section('content')

    <section class="message-container" data-track-section="message" data-track-section-view data-track-section-label="取得協助">
        <div class="fixed-bg" style="background-image: url('{{ asset_upload(app('cache.config')->get('page_message_back_img')) }}')">
            <div class="mask"></div>
            <p class="slogan">{!! app('cache.config')->get('page_message_title') !!}</p>
        </div>
        <div class="page-main">
            <div class="page-head">
                <p class="beat"><i class="iconfont">&#xe784;</i></p>
                <h1 class="page-title">取得協助</h1>

            </div>
            <div class="page-body">
                <div class="quick">
                    <div class="describe">
                        <p class="title">快速協助</p>
                        <p class="text">
                            {!! app('cache.config')->get('page_message_desc') !!}
                        </p>
                    </div>
                    <ul class="fqa">
                        @foreach($faqs as $item)
                            <li>
                                <p class="question"><a href="javascript:;">Q：{{ $item->questions }}</a></p>
                                <p class="answers">{{ $item->answers }}</p>
                            </li>
                        @endforeach
                    </ul>
                    <p class="finis">{!! app('cache.config')->get('page_message_finis') !!}</p>
                </div>

                <div class="liaison">
                    <div class="box">
                        <p class="title">聯絡我們</p>
                        <p class="text">
                            {!! app('cache.config')->get('page_lianluo_desc') !!}
                        </p>
                    </div>
                </div>

                <div class="form-pack">
                    <form action="{{ url('message') }}" method="post" id="message-form">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>你的稱呼：</label>
                            <input class="form-control" data-validate="required:請輸入你的稱呼" type="text" name="name" placeholder="請輸入你的稱呼">
                        </div>
                        <div class="form-group">
                            <label>聯絡電話：</label>
                            <input class="form-control" data-validate="required:請輸入你的聯絡電話|mobile:聯絡電話格式錯誤" type="tel" name="phone" placeholder="請輸入聯絡你的電話號碼" maxlength="10" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                        </div>
                        <div class="form-group">
                            <label>電子信箱：</label>
                            <input class="form-control" data-validate="required:請輸入你的電子信箱|email:電子信箱格式錯誤" type="text" name="email" placeholder="請輸入聯絡你的電子信箱">
                        </div>
                        <div class="form-group">
                            <label>協助類型：</label>
                            <select class="form-control" name="type">
                                <option value="1">療程咨詢</option>
                                <option value="2">退換貨</option>
                                <option value="3">修改訂單資訊</option>
                                <option value="4">修改/新增訂單備注</option>
                                <option value="5">意見或建議</option>
                                <option value="0" selected>其他</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>問題詳述：</label>
                            <textarea class="form-control form-textarea" data-validate="required:請詳述你的問題或建議" name="content"  placeholder="請詳述你的問題或建議&#10;並提供您的訂單編號(如您已取得)"></textarea>
                        </div>

                        <div class="form-group">
                            <button class="form-btn" >確認送出</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </section>

@endsection
