@extends('web::layout.layout')

@if($news->seo_title)
    @section('title', $news->seo_title ?? $news->title)
@else
    @section('title', $news->title)
@endif

@if($news->seo_description)
    @section('description', $news->seo_description)
@else
    @section('description', \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/u', ' ', strip_tags($news->content ?? ''))), 120, '...'))
@endif

@section('style')
    @parent
    @php
        $customCss = $customCss ?? '';
    @endphp
    <style>
    .article-content table { background-color: transparent !important; }
    .article-content .bloc-button.btn-d.scrollToTop {
        width: 36px !important;
        height: 36px !important;
        padding: 6px !important;
        font-size: 14px !important;
        min-width: unset !important;
        min-height: unset !important;
        line-height: 1 !important;
    }
    .article-content .bloc-button.btn-d.scrollToTop svg {
        width: 16px !important;
        height: 16px !important;
    }
    </style>
    @if(!empty($customCss))
    <style>
    {!! $customCss !!}
    </style>
    @endif
@stop

@section('script')
    <script>
        (() => {
        const content = document.getElementById('articleContent');
        const list = document.querySelector('.summary-list');
        if (!content || !list) return;
        const articleSummary = document.getElementById('articleSummary');
        const summaryWrap = document.querySelector('.summary-fixed');
        const summaryMask = document.querySelector('.summary-mask');
        const summarySwitch = document.querySelector('.summary-switch');
        const summaryTitle = document.querySelector('.summary-title');
        const mainHeader = document.querySelector('.main-header');
        const defaultSummaryTitle = '閱讀導覽';
        let summaryTitleFadeTimer = 0;

        const leadEl = content.querySelector('#lead');
        const h2h3List = Array.from(content.querySelectorAll('h2, h3'));
        const headings = (leadEl ? [leadEl] : []).concat(h2h3List);
        if (!headings.length) return;

        const links = list.querySelectorAll('a');
        const headerOffset = 100;

        function getActiveTitleText() {
            const activeLink = list.querySelector('a.active');
            if (!activeLink) return '';
            return activeLink.textContent.trim();
        }

        function isMobileSummaryMode() {
            return !!summarySwitch && window.getComputedStyle(summarySwitch).display !== 'none';
        }

        function isSummaryStickyActive() {
            if (!summaryWrap || !isMobileSummaryMode()) return false;
            const stickyTop = parseFloat(window.getComputedStyle(summaryWrap).top) || 0;
            const rect = summaryWrap.getBoundingClientRect();
            const tolerance = 1;
            const isSticky = window.pageYOffset > 0 && rect.top <= stickyTop + tolerance;
            const isVisible = rect.bottom > 0 && rect.top < window.innerHeight;
            return isSticky && isVisible;
        }

        function isSummaryExpanded() {
            return !!summaryWrap && summaryWrap.classList.contains('is-expanded');
        }

        function setSummaryTitle(nextText, useStickyStyle) {
            if (!summaryTitle) return;
            const currentText = summaryTitle.textContent || '';
            const currentSticky = summaryTitle.classList.contains('title-sticky');

            if (currentText === nextText && currentSticky === useStickyStyle) return;

            if (summaryTitleFadeTimer) {
                clearTimeout(summaryTitleFadeTimer);
                summaryTitleFadeTimer = 0;
            }

            summaryTitle.classList.add('is-fade-in');
            summaryTitle.textContent = nextText;
            summaryTitle.classList.toggle('title-sticky', useStickyStyle);
            summaryTitleFadeTimer = window.setTimeout(() => {
                summaryTitle.classList.remove('is-fade-in');
                summaryTitleFadeTimer = 0;
            }, 180);
        }

        function updateSummaryStickyState() {
            if (!articleSummary) return;

            if (!isMobileSummaryMode()) {
                articleSummary.classList.remove('is-top');
                document.body.classList.remove('summary-open');
                return;
            }

            const stickyActive = isSummaryStickyActive();
            articleSummary.classList.toggle('is-top', stickyActive);
            document.body.classList.toggle('summary-open', isSummaryExpanded() && stickyActive);
            if (mainHeader && stickyActive) {
                mainHeader.classList.remove('shadow');
            }
        }

        function updateReadingProgress() {
            if (!mainHeader || !content) return;

            const contentRect = content.getBoundingClientRect();
            const contentTop = contentRect.top + window.pageYOffset;
            const contentBottom = contentTop + contentRect.height;
            const headerHeight = mainHeader ? mainHeader.offsetHeight : 60;
            const readPosition = window.pageYOffset + headerHeight;
            const total = contentBottom - contentTop;

            let progress = 0;
            if (total > 0) {
                progress = ((readPosition - contentTop) / total) * 100;
            } else {
                progress = 100;
            }

            progress = Math.min(100, Math.max(0, progress));
            mainHeader.style.setProperty('--news-read-progress', String(progress / 100));
        }

        function updateSummaryTitle() {
            if (!summaryWrap || !summaryTitle || !isMobileSummaryMode()) {
                if (summaryTitle) {
                    setSummaryTitle(defaultSummaryTitle, false);
                }
                return;
            }

            if (summaryWrap.classList.contains('is-collapsed')) {
                if (!isSummaryStickyActive()) {
                    setSummaryTitle(defaultSummaryTitle, false);
                    return;
                }
                setSummaryTitle(getActiveTitleText() || defaultSummaryTitle, true);
                return;
            }

            setSummaryTitle(defaultSummaryTitle, false);
        }

        function setSummaryCollapsed(collapsed) {
            if (!summaryWrap) return;
            const expanded = !collapsed;

            summaryWrap.classList.toggle('is-collapsed', collapsed);
            summaryWrap.classList.toggle('is-expanded', expanded);

            if (summarySwitch) {
                summarySwitch.classList.toggle('is-on', expanded);
                summarySwitch.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
            }

            updateSummaryTitle();
            updateSummaryStickyState();
        }

        let lastMobileMode = isMobileSummaryMode();
        function syncSummaryByViewport() {
            if (!summaryWrap) return;
            const currentMobileMode = isMobileSummaryMode();

            if (currentMobileMode !== lastMobileMode) {
                setSummaryCollapsed(currentMobileMode);
                lastMobileMode = currentMobileMode;
                updateSummaryStickyState();
                return;
            }

            if (!currentMobileMode) {
                setSummaryCollapsed(false);
                updateSummaryStickyState();
                return;
            }

            updateSummaryTitle();
            updateSummaryStickyState();
        }

        function updateVisibleBySection() {
            const viewTop = window.pageYOffset + headerOffset;
            const viewBottom = window.pageYOffset + window.innerHeight;

            headings.forEach((heading, i) => {
                const id = heading.id;
                if (!id) return;

                const rect = heading.getBoundingClientRect();
                const sectionTop = rect.top + window.pageYOffset;
                const sectionBottom = i + 1 < headings.length
                    ? headings[i + 1].getBoundingClientRect().top + window.pageYOffset
                    : content.getBoundingClientRect().bottom + window.pageYOffset;

                const visible = sectionTop < viewBottom && sectionBottom > viewTop;
                links.forEach(link => {
                    const href = link.getAttribute('href');
                    const linkId = href && href.startsWith('#') ? href.slice(1) : '';
                    if (linkId === id) link.classList.toggle('active', visible);
                });
            });

            updateSummaryTitle();
        }

        let tick = 0;
        function onScrollOrResize() {
            tick = requestAnimationFrame(() => {
                updateVisibleBySection();
                updateSummaryStickyState();
                updateReadingProgress();
                tick = 0;
            });
        }
        window.addEventListener('scroll', onScrollOrResize, { passive: true });
        window.addEventListener('resize', onScrollOrResize);
        updateVisibleBySection();
        syncSummaryByViewport();
        updateReadingProgress();

        if (summarySwitch) {
            summarySwitch.addEventListener('click', () => {
                if (!isMobileSummaryMode()) return;
                const willCollapse = !summaryWrap?.classList.contains('is-collapsed');
                setSummaryCollapsed(willCollapse);
            });
        }

        if (summaryMask) {
            summaryMask.addEventListener('click', () => {
                if (!isMobileSummaryMode()) return;
                if (summaryWrap?.classList.contains('is-collapsed')) return;
                setSummaryCollapsed(true);
            });
        }

        window.addEventListener('keydown', e => {
            if (e.key !== 'Escape') return;
            if (!isMobileSummaryMode() || !isSummaryExpanded()) return;
            setSummaryCollapsed(true);
        });

        window.addEventListener('popstate', () => {
            if (!isMobileSummaryMode() || !isSummaryExpanded()) return;
            setSummaryCollapsed(true);
        });

        const offset = 100;
        list.addEventListener('click', e => {
            const link = e.target.closest('a');
            if (!link) return;

            const href = link.getAttribute('href');
            const target = href && href.startsWith('#') ? document.querySelector(href) : null;
            if (!target) return;

            e.preventDefault();

            const targetTop = target.getBoundingClientRect().top + window.pageYOffset - offset;
            window.scrollTo({ top: targetTop, behavior: 'smooth' });

            if (summaryWrap?.classList.contains('is-expanded') && isMobileSummaryMode()) {
                setSummaryCollapsed(true);
            }
        });
        })();
    </script>
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
    <script>
        (function() {
            function getDateKey() {
                var d = new Date();
                return d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
            }
            var pathKey = (window.location.pathname || '/').replace(/\//g, '_');
            var dateKey = 'news_like_date_' + pathKey;
            var baseKey = 'news_like_base_' + pathKey;
            var clickedKey = 'news_like_clicked_' + pathKey;

            function getBaseNumber() {
                var today = getDateKey();
                var storedDate = localStorage.getItem(dateKey);
                var storedBase = localStorage.getItem(baseKey);
                if (storedDate !== today || !storedBase) {
                    var base = 10000 + Math.floor(Math.random() * 10001);
                    localStorage.setItem(dateKey, today);
                    localStorage.setItem(baseKey, String(base));
                    return base;
                }
                return parseInt(storedBase, 10);
            }

            function isClicked() {
                return localStorage.getItem(clickedKey) === '1';
            }

            function setClicked() {
                localStorage.setItem(clickedKey, '1');
            }

            function updateLikeDisplay() {
                var likeEl = document.querySelector('.article .view-box .like');
                if (!likeEl) return;
                var span = likeEl.querySelector('span');
                if (!span) return;
                var base = getBaseNumber();
                var num = base + (isClicked() ? 1 : 0);
                span.textContent = String(num);
                if (isClicked()) likeEl.classList.add('active');
            }

            function init() {
                updateLikeDisplay();
                var likeEl = document.querySelector('.article .view-box .like');
                if (!likeEl) return;
                likeEl.addEventListener('click', function() {
                    if (likeEl.classList.contains('active')) return;
                    likeEl.classList.add('active');
                    setClicked();
                    var span = likeEl.querySelector('span');
                    if (span) span.textContent = String(parseInt(span.textContent, 10) + 1);
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();
    </script>
@stop

@section('content')
<main class="page-news-info">
    @include('web.widgets.breadcrumb', ['itemsHtml' => '<li class="breadcrumb__item"><a href="'.url('news').'">文章列表</a></li><li class="breadcrumb__item">'.Str::limit($news->title, 25).'</li>'])

    <article class="article">
        
        <header class="article-header">
            <h1 class="master-title">{{ $news->title }}</h1>
            <div class="editor-box">
                <div class="edit">
                    <p class="time">最新編輯時間 {{ $news->updated_at->format('Y-m-d') }}</p>
                </div>
                <div class="view-box">
                    <p class="views"><svg class="viewicon" viewBox="0 0 1024 1024"><use href="#icon-viewicon"></use></svg>
                    {{ $news->read_num ?? 0 }}</p>
                    <p class="like">
                        <svg class="icon" viewBox="0 0 1024 1024"><use href="#icon-like"></use></svg><span>31026</span>
                    </p>
                </div>
            </div>
        </header>
        @if($news->img)
            <div class="article-cover">
                <img src="{{ asset_upload($news->img) }}" sizes="(max-width: 768px) 100%, 800px" width="800" height="400" decoding="async" loading="auto" fetchpriority="high" alt="{{ $news->img_alt ?? $news->title }}">
            </div>
        @endif
        <div class="summary-fixed is-collapsed">
            <nav class="article-summary" id="articleSummary">
                <div class="summary-header">
                    <p class="summary-title">閱讀導覽</p>
                    <button class="summary-switch" type="button" aria-label="切換閱讀導覽" aria-expanded="false">
                        <span class="summary-switch__face summary-switch__face--on"><svg class="summary-switch__icon summary-switch__icon--collapse" viewBox="0 0 1024 1024"><use href="#icon-arrowicon"></use></svg>收起</span>
                        <span class="summary-switch__face summary-switch__face--off"><svg class="summary-switch__icon" viewBox="0 0 1024 1024"><use href="#icon-summary-expand"></use></svg>展開</span>
                    </button>
                </div>
                <ol class="summary-list">
                    @foreach ($toc as $item)
                        <li class="summary-item summary-h2">
                            <a href="#{{ $item['id'] }}">
                                {{ $item['title'] }}<svg class="arrowicon" viewBox="0 0 1024 1024"><use href="#icon-arrowicon"></use></svg>
                            </a>
                            @if (!empty($item['children']))
                                <ol class="summary-sublist">
                                    @foreach ($item['children'] as $child)
                                        <li class="summary-item summary-h3">
                                            <a href="#{{ $child['id'] }}">
                                                {{ $child['title'] }}<svg class="arrowicon" viewBox="0 0 1024 1024"><use href="#icon-arrowicon"></use></svg>
                                            </a>
                                        </li>
                                    @endforeach
                                </ol>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
            <button type="button" class="summary-mask" aria-label="收起閱讀導覽"></button>
        </div>

        <section class="article-content article-container" id="articleContent" data-track-section-view data-track-section="news.content" data-track-section-label="文章正文">
            {!! $content !!}
        </section>

        @if(!empty($articleTags) && count($articleTags))
            <div class="article-tags">
                @foreach($articleTags as $tag)
                    <span class="article-tag" style="color: {{ $tag->color }}; border-color: {{ $tag->color }}">{{ $tag->name }}</span>
                @endforeach
            </div>
        @endif
    </article>

    <nav class="page">
        @if($prev)
            <a href="{{ url('news/'.$prev->id) }}" class="prev">
                <span>上一篇</span>
                <p class="title">{{ $prev->title ?? '沒有上一篇' }}</p>
            </a>
        @else
            <a href="#" class="prev">
                <span>上一篇</span>
                <p class="title">沒有上一篇</p>
            </a>
        @endif
        @if($next)
            <a href="{{ url('news/'.$next->id) }}" class="next">
                <span>下一篇</span>
                <p class="title">{{ $next->title ?? '沒有下一篇' }}</p>
            </a>
        @else
            <a href="#" class="next">
                <span>下一篇</span>
                <p class="title">沒有下一篇</p>
            </a>
        @endif
    </nav>

    <section class="related-articles" data-track-section-view data-track-section="news.related" data-track-section-label="更多閱讀">
        <h2 class="sec-title">更多閱讀</h2>
        <ul class="news-wrap">
            @forelse($top as $item)
                @include('web.widgets.news-card', [
                    'item' => $item,
                    'rootTag' => 'li',
                ])
            @empty
                <li>暫無相關文章</li>
            @endforelse
        </ul>
    </section>
</main>
@include('web.widgets.update-box')
@endsection
