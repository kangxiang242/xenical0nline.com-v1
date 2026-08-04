@extends('mobile.layout')
@if($news->seo_title)
    @section('title', $news->seo_title)
@else
    @section('title', $news->title)
@endif

@if($news->seo_keyword)
    @section('keywords', $news->seo_keyword)
@endif

@if($news->seo_description)
    @section('description', $news->seo_description)
@endif
@section('style')
    @parent
    <base href="{{ $articleAssetBase ?? '/' }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('static/mobile/less/news-desc.css') }}?ver={{ config('app.asset_version') }}"/>
    @if($news->custom_css)
    <style>{!! $news->custom_css !!}</style>
    @endif
@stop

@section('script')
    @parent
    <script>
        
        
        function setIframeHeight(iframe) {
            if (iframe) {
                var iframeWin = iframe.contentWindow || iframe.contentDocument.parentWindow;
                if (iframeWin.document.body) {
                    iframe.height = iframeWin.document.documentElement.scrollHeight || iframeWin.document.body.scrollHeight;
                }
            }
        };
        
        window.onload = function() {
            setIframeHeight(document.getElementById('external-frame'));
        };
    </script>
@stop
@section('breadcrumb')
    <ul class="breadcrumb">
        <li><a href="{{ url('/') }}">首頁</a></li>
        <li><a href="{{ url('news') }}">瘦身專欄</a></li>
        <li class="active">{{ $news->title }}</li>
    </ul>
@stop

@section('content')
@push('body-attr')
@endpush

    <div class="news-show-wrap">
        <div class="middle">
            <div class="line"></div>
            <div class="time">
                <p class="p1">發佈日期</p>
                <p class="p2">{{ $news->release_at->format('d') }}.{{ $news->release_at->format('m') }}.{{ $news->release_at->format('Y') }}</p>
            </div>
            <div class="fluid">
                <h1 class="news-title">{{ $news->title }}</h1>
                <div class="news-content" data-track-scroll-target>
                    @if($news->html_file)
                        @php
                            // 移除 .zip 后缀以获取正确的目录名
                            $dirName = preg_replace('/\.zip$/', '', $news->html_file);
                            $htmlPath = 'uploads/' . $dirName . '/index.html';
                        @endphp
                        <iframe id="external-frame" width="100%" style="min-height: 100vh" src="{{ asset($htmlPath) }}" frameborder="0" scrolling="no" onload="setIframeHeight(this)"></iframe>
                    @else
                        {!! preg_replace(['/<script\b[^>]*>.*?<\/script>/is', '/<title\b[^>]*>.*?<\/title>/is', '/<meta\b[^>]*\/?>/i', '/<base\b[^>]*\/?>/i', '/<link\b[^>]*\/?>/i', '/<iframe\b[^>]*>.*?<\/iframe>/is', '/<\/?(?:html|head|body)\b[^>]*>/i'], '', $news->content) !!}
                    @endif
                </div>
            </div>
        </div>

        <div class="article-footer">


            <nav class="relatednav">
                @if($prev)
                <a class="relatednav-prev" data-track-section="news.detail" data-track-name="news.detail.prev" data-observer="上一篇" href="{{ url('news/'.$prev->id) }}">
                    <span class="relatednav-arrow"></span>
                    <span class="relatednav-title  h4 h4-mb fw-bolder">{{ $prev->title }}</span>
                </a>
                @endif

     {{--           <a class="relatednav-back  fw-bold" href="{{ url('news') }}">
                    <i class="ico-dots"><b></b></i>返回
                </a>--}}
                @if($next)
                <a class="relatednav-next" data-track-section="news.detail" data-track-name="news.detail.next" data-observer="下一篇" href="{{ url('news/'.$next->id) }}">
                    <span class="relatednav-arrow"></span>
                    <span class="relatednav-title  h4 h4-mb fw-bolder">{{ $next->title }}</span>
                </a>
                @endif
            </nav>
        </div>
    </div>

@endsection
