@extends('mobile.layout')

@section('style')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('static/mobile/less/faq.css') }}?ver={{ config('app.asset_version') }}"/>

@stop

@section('script')
    @parent
    <script>
        $('.question-show').click(function(){
            var is_show = $(this).attr('data-show');
            var height = $(this).find('.q-desc').height()+10+$(this).find('.q-title').height()
            if(!is_show){
                $(this).css('height',height);
                $(this).attr('data-show',1);
                $(this).find('.q-icon').html('&#xeca2;');
            }else{
                $(this).css('height',$(this).find('.q-title').height());
                $(this).removeAttr('data-show');
                $(this).find('.q-icon').html('&#xe775;');
            }

        });
    </script>


@stop
@section('breadcrumb')
    <ul class="breadcrumb">
        <li><a href="{{ url('/') }}">首頁</a></li>
        <li class="active">營養師解答</li>
    </ul>
@stop

@section('content')
    <section class="fqa" data-track-section="faq" data-track-section-view data-track-section-label="營養師解答">
        <div class="wrapper" style="">
            <div class="modal">
                <h1 class="title">營養師解答</h1>
            </div>
            <div class="fqa-body">
                <div class="question">
                    @foreach($faq as $item)
                    <div class="item question-show" data-faq-id="{{ $loop->iteration }}">
                        <p class="q-title">Q：{{ $item->questions }}</p>
                        <p class="q-desc">{{ $item->answers }}</p>
                        <i class="q-icon iconfont">&#xe775;</i>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

@endsection
