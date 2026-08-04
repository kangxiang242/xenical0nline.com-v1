@extends('web.layout')

@section('style')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('static/less/news.css') }}?ver={{ config('app.asset_version') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('static/less/pagination.css') }}?ver={{ config('app.asset_version') }}"/>
@stop

@section('script')
    @parent
    <script src="{{ asset('static/js/jquery.waypoints.min.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script>
        $(function(){
            $('.cardList > li').waypoint(function(){
                this.element.classList.add('show');
            },{
                offset: '70%'
            });
        });
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
    @php($newsBg = app('cache.config')->get('page_news_back_img_pc'))
    @if($newsBg)
    <div class="container-bg" style="background-image: url('{{ asset_upload($newsBg) }}')">
    @else
    <div class="container-bg">
    @endif
        <p class="bg-text">{!! app('cache.config')->get('page_news_title') !!}</p>
        <p class="beat"><i class="iconfont">&#xe784;</i></p>
    </div>
    <h1 class="page-title">瘦身專欄</h1>
    <div class="news-wrap" data-track-section="news.list" data-track-section-view data-track-section-label="文章列表">
        <ul class="breadcrumb">
            <li><a href="{{ url('/') }}">首頁</a></li>
            <li class="active">瘦身專欄</li>
        </ul>
        <ul class="cardList vertical">

            @foreach($news as $item)
                <li class="">
                    <div class="item ">
                        @if($item->img)
                        <div class="Img"><a href="{{ url('news/'.$item->id) }}" data-track-section="news.list" data-track-name="news.list.item" data-observer="文章-{{ $item->title }}"><img src="{{ asset('uploads/'.$item->img) }}" alt="{{ $item->title }}" loading="lazy" decoding="async"></a></div>
                        @endif
                        <div class="Txt">
                            <div class="newsInfoIdxBox">
                                <div class="newsDateBox">
                                    <span class="day">{{ $item->release_at->format('d') }}</span>
                                    <span class="ym">{{ substr($item->release_at->format('Y'),-2) }} {{ $item->release_at->format('M') }}</span>
                                </div>
                                <div class="newsTitle">
                                    <h3><a href="{{ url('news/'.$item->id) }}" data-track-section="news.list" data-track-name="news.list.title" data-observer="文章標題-{{ $item->title }}">{{ $item->title }}</a></h3>
                                </div>
                            </div>
                            <p class="ellipsis" style="overflow-wrap: break-word;">
                                {{ \Illuminate\Support\Str::limit($item->brief?$item->brief:strip_tags($item->content),680) }}
                            </p>
                        </div>
                    </div>
                </li>
            @endforeach


        </ul>

        {{ $news->links() }}
    </div>

@endsection
