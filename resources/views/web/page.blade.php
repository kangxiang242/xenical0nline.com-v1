@extends('web::layout.layout')

@section('style')
    @parent
    @if(isset($css) && $css)
    <style type="text/css">
        {!! $css !!}
    </style>
    @endif
@stop

@section('script')
    <script>
        if (location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
            document.domain = location.hostname;
        }
        function getSafeFrameHeight(iframe) {
            var frameDoc = iframe && iframe.contentWindow ? iframe.contentWindow.document : null;
            if (!frameDoc) {
                return 0;
            }
            return Math.max(
                frameDoc.documentElement ? frameDoc.documentElement.scrollHeight : 0,
                frameDoc.body ? frameDoc.body.scrollHeight : 0
            );
        }
        function setIframeHeight(iframe) {
            if (iframe) {
                var frameHeight = getSafeFrameHeight(iframe);
                if (frameHeight > 0) {
                    iframe.style.height = frameHeight + 'px';
                }
            }
        }
        window.addEventListener('load', function () {
            var iframe = document.getElementById('external-frame');
            if (!iframe) {
                return;
            }
            setIframeHeight(iframe);
            var retryTimer = setInterval(function () {
                setIframeHeight(iframe);
            }, 400);
            setTimeout(function () {
                clearInterval(retryTimer);
            }, 5000);
        });
    </script>
@stop

@section('content')
<main>
    @include('web.widgets.head-banner')
    @include('web.widgets.breadcrumb', ['itemsHtml' => '<li class="breadcrumb__item">'.$title.'</li>'])
    <section class="editor article-content" id="spageContent" data-track-section-view data-track-section="cms.content" data-track-section-label="頁面內容">
        {!! $content !!}
    </section>
</main>
@include('web.widgets.update-box')
@endsection

