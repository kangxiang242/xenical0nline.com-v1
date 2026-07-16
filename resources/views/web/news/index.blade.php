@extends('web::layout.layout')

@section('style')
    @parent
@stop

@section('script')
    <script>
        const cards = document.querySelectorAll('.news-card');

            function updateActiveCard() {
                let viewportCenter = window.innerHeight / 2;
                let closestCard = null;
                let closestDistance = Infinity;

                cards.forEach(card => {
                    const rect = card.getBoundingClientRect();
                    const cardCenter = rect.top + rect.height / 2;
                    const distance = Math.abs(cardCenter - viewportCenter);

                    if (distance < closestDistance) {
                        closestDistance = distance;
                        closestCard = card;
                    }
                });

                // 清除所有 active
                cards.forEach(card => card.classList.remove('active'));

                // 設置視窗中心最近的那張
                if (closestCard) {
                    closestCard.classList.add('active');
                }
            }

            // 建議用 throttle / requestAnimationFrame 以防過度觸發
            let ticking = false;
            window.addEventListener('scroll', () => {
                if (!ticking) {
                    requestAnimationFrame(() => {
                        updateActiveCard();
                        ticking = false;
                    });
                    ticking = true;
                }
            });

            // 進入頁面也先跑一次
            updateActiveCard();

    </script>
@stop




@section('content')
<main>
    @include('web.widgets.head-banner')

    @include('web.widgets.breadcrumb', ['itemsHtml' => '<li class="breadcrumb__item">'.($cate->name ?? '文章列表').'</li>'])

    @if(!empty($topicsTags) && count($topicsTags))
        <section class="topics">
            @foreach($topicsTags as $tag)
                <div class="topic" style="border-left: 4px solid {{ $tag['color'] }}">
                    <h3 class="topic-title" style="color: {{ $tag['color'] }}">{{ $tag['name'] }}</h3>
                    @if(!empty($tag['description']))
                        <p class="topic-desc">{{ $tag['description'] }}</p>
                    @endif
                    <ul class="topic-articles">
                        @foreach($tag['articles'] as $article)
                            <li>
                                <a href="{{ ($article->cate && $article->cate->uri)
                                    ? route('news.show', [$article->cate->uri, $article->id])
                                    : url('news/'.$article->id) }}">
                                    {{ $article->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </section>
    @endif

    <section class="news-list" data-track-section-view data-track-section="news.list" data-track-section-label="文章列表">
        <h2 class="sec-title">文章列表</h2>
        <ul class="news-wrap">
            @forelse($news as $item)
                @include('web.widgets.news-card', [
                    'item' => $item,
                    'rootTag' => 'li',
                ])
            @empty
                <li>暫無文章</li>
            @endforelse
        </ul>

        <div class="list-pagination">
            {!! $news->links() !!}
        </div>
    </section>
</main>
@include('web.widgets.update-box')
@endsection
