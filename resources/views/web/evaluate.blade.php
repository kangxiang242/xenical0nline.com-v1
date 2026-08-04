@extends('web.layout')

@section('style')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('static/less/evaluate.css') }}?ver={{ config('app.asset_version') }}"/>
    @if(isset($css) && $css)
    <style type="text/css">
        {!! $css !!}
    </style>
    @endif
    <style>
        blockquote{
            border-left: 5px solid rgba(0,0,0,.05);
            padding: 20px;

            font-style: italic;

            position: relative;
            margin: 1.5em 1em 1.5em 3em;
            font-size: 1.2em;
            line-height: inherit;

        }
        .editor ul {
            list-style: disc;
            margin: 0 0 1.5em 3em;
        }
        .editor li{
            list-style: disc;
            margin-bottom: 20px;
        }
        .editor ul li::marker{

            unicode-bidi: isolate;
            font-variant-numeric: tabular-nums;
            text-transform: none;
            text-indent: 0px !important;
            text-align: start !important;
            text-align-last: start !important;
        }
        .editor p{
            margin-bottom: 1.8em;
            font-size: 18px;
        }

        .editor h2{
            margin-bottom: 20px;
            font-size: 2.4em;
        }
    </style>
@stop

@section('script')
    @parent
    <script src="{{ asset('static/js/xie.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('static/js/jquery.leoTextAnimate.js') }}?ver={{ config('app.asset_version') }}"></script>
    @if(!(strpos(strtolower(request()->userAgent()),'google') !== false))
    <script>

        function BMR(sex,weight,height,age){
            if(sex == 1){
                var bmr = (10*weight + 6.25*height - 5*age) + 5;
            }else{
                var bmr = (10*weight + 6.25*height - 5*age) - 161;
            }
            return Math.round(bmr);
        }

        function TDEE(bmr,activityLevel){
            var base = 0;
            if(activityLevel == 1){
                base = 1.2;
            }else if(activityLevel == 2){
                base = 1.357
            }else if(activityLevel == 3){
                base = 1.55
            }else if(activityLevel == 4){
                base = 1.725
            }else if(activityLevel == 5){
                base = 1.9
            }
            return Math.round(base*bmr);
        }

        function BMI(height,weight){
            return (weight/(height*height)*10000).toFixed(1);
        }

        $('.count-btn a').click(function(){
            var sex = $("input[name='sex']:checked").val();
            var age = $("input[name='age']").val();
            var height = $("input[name='height']").val();
            var weight = $("input[name='weight']").val();
            var activityLevel = $("input[name='activityLevel']:checked").val();
            if(!age){
                $("input[name='age']").focus();
                promptError('計算失敗','請輸入您的年齡');
                return false;
            }

            if(!$.isNumeric(age)){
                $("input[name='age']").focus();
                promptError('計算失敗','請輸入正確的年齡');
                return false;
            }

            if(!height){
                $("input[name='height']").focus();
                promptError('計算失敗','請輸入您的身高');
                return false;
            }

            if(!$.isNumeric(height)){
                $("input[name='height']").focus();
                promptError('計算失敗','請輸入正確的身高');
                return false;
            }

            if(!weight){
                $("input[name='weight']").focus();
                promptError('計算失敗','請輸入您的體重');
                return false;
            }

            if(!$.isNumeric(weight)){
                $("input[name='weight']").focus();
                promptError('計算失敗','請輸入正確的體重');
                return false;
            }
            if($(this).attr('disabled')){
                return false;
            }


            var bmr = BMR(sex,weight,height,age);
            var tdee = TDEE(bmr,activityLevel);
            var bmi = BMI(height,weight);

            addLoadingActionBtn('.count-btn a');
            $.ajax({
                type: "POST",
                url: "{{ url('compute') }}",
                data: {sex:sex,age:age,height:height,weight:weight,activityLevel:activityLevel,bmr:bmr, tdee:tdee,bmi:bmi,_token:"{{ csrf_token() }}"},
                dataType: "html",
                success: function(data){
                    $('#ending').html(data);
                    closeLoadingActionBtn('.count-btn a');
                    leoTextAnimateRun();
                    window.dispatchEvent(new Event('xo:calc_complete'));
                },
                error:function(){
                    closeLoadingActionBtn('.count-btn a');
                    promptError('頻繁操作','請稍後再試');
                    return false;
                }
            });


        })



        function leoTextAnimateRun(){
            $('#bmr-value').leoTextAnimate({delay: 500, autorun: true, fixed: ['', '', ''], start: '-'});
            $('#tdee-value').leoTextAnimate({delay: 500, autorun: true, fixed: ['', '', ''], start: '-'});
            $('#bmi-value').leoTextAnimate({delay: 500, autorun: true, fixed: ['', '', '.'], start: '-'});

        }


    </script>
    @endif
@stop

@section('breadcrumb')
    <ul class="breadcrumb">
        <li><a href="{{ url('/') }}">首頁</a></li>

        <li class="active">瘦身計算機</li>
    </ul>
@stop

@section('embed-banner')
    <div class="embed-banner align-left wrapper">
        <h1 class="embed-title">{!! app('cache.config')->get('page_evaluate_title') !!}</h1>
        <div class="embed-desc">{!! str_replace(PHP_EOL,'<br>',app('cache.config')->get('page_evaluate_desc')) !!}</div>
    </div>
@stop

@section('content')


    <section class="evaluate-container" data-track-section="calc" data-track-section-view data-track-section-label="瘦身計算機">
        <div class="evaluate-wrapper">

            @if(!(strpos(strtolower(request()->userAgent()),'google') !== false))
                <div class="evaluate-form">
                    <div class="form-group">
                        <p class="title">你的性別？</p>
                        <div class="form-inline">
                            <div class="radio-inline">
                                <input type="radio" name="sex" value="2" checked id="sex1">
                                <label for="sex1">女性</label>
                            </div>
                            <div class="radio-inline">
                                <input type="radio" name="sex" value="1" id="sex2">
                                <label for="sex2">男性</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <p class="title">你的年龄？</p>
                        <input class="form-control" type="text" name="age" placeholder="">
                    </div>
                    <div class="form-group">
                        <p class="title">你的身高（公分）？</p>
                        <input class="form-control" type="text" name="height" placeholder="">
                    </div>
                    <div class="form-group">
                        <p class="title">你的體重（公斤）？</p>
                        <input class="form-control" type="text" name="weight" placeholder="">
                    </div>

                    <div class="form-group">
                        <p class="title">每週的運動強度？</p>
                        <div class="radio-row">
                            <input id="qd1" type="radio" name="activityLevel" value="1" checked>
                            <label for="qd1">久坐/沒在運動</label>
                        </div>
                        <div class="radio-row">
                            <input id="qd2" type="radio" name="activityLevel" value="2">
                            <label for="qd2">每週低強度運動／一週運動 1～3 天</label>
                        </div>
                        <div class="radio-row">
                            <input id="qd3" type="radio" name="activityLevel" value="3">
                            <label for="qd3">每週中等強度運動／一週運動 3～5 天</label>
                        </div>
                        <div class="radio-row">
                            <input id="qd4" type="radio" name="activityLevel" value="4">
                            <label for="qd4">每週高強運動/一個星期運動 5 ~ 7 天</label>
                        </div>
                        <div class="radio-row">
                            <input id="qd5" type="radio" name="activityLevel" value="5">
                            <label for="qd5">每天運動訓練 2 次、勞力工作者</label>
                        </div>
                    </div>
                    <div class="count-btn" data-track-calc-start>
                        <a class="btn-ef1" href="javascript:;">開始計算</a>
                    </div>
                </div>
            @endif


            <div class="ending" id="ending">


            </div>


            <div class="editor">
                {!! app('cache.config')->get('page_evaluate_article') !!}
            </div>


        </div>
    </section>
@endsection
