@extends('web::layout.layout')

@section('body_attributes', 'data-page="home"')

@section('style')
    @parent
@stop

@section('script')
    <script defer src="{{ asset('static/js/price-animator.js') }}?ver={{ config('app.asset_version') }}"></script>

    <script>
        /** 用 DOM 建立 .word，避免 innerHTML 拼接 SVG 時產生異常字元／舊環境解析問題 */
        function textAnimation(elem) {
            if (!elem) {
                return;
            }
            var wordStaggerMs = 30;
            var charIndex = 0;
            var frag = document.createDocumentFragment();

            function appendTextSegment(text) {
                var t = text || '';
                for (var j = 0; j < t.length; j++) {
                    var span = document.createElement('span');
                    span.className = 'word';
                    span.style.animationDelay = charIndex * wordStaggerMs + 'ms';
                    span.textContent = t.charAt(j);
                    frag.appendChild(span);
                    charIndex++;
                }
            }

            function walk(node) {
                if (!node) {
                    return;
                }
                if (node.nodeType === Node.TEXT_NODE) {
                    appendTextSegment(node.nodeValue);
                    return;
                }
                if (node.nodeType === Node.ELEMENT_NODE) {
                    var ns = node.namespaceURI || '';
                    var tag = node.tagName ? node.tagName.toLowerCase() : '';
                    if (tag === 'svg' || ns.indexOf('svg') !== -1) {
                        var wrap = document.createElement('span');
                        wrap.className = 'word';
                        wrap.style.animationDelay = charIndex * wordStaggerMs + 'ms';
                        wrap.appendChild(node.cloneNode(true));
                        frag.appendChild(wrap);
                        charIndex++;
                        return;
                    }
                    var kids = node.childNodes;
                    for (var i = 0; i < kids.length; i++) {
                        walk(kids[i]);
                    }
                }
            }

            var children = elem.childNodes;
            for (var k = 0; k < children.length; k++) {
                walk(children[k]);
            }

            while (elem.firstChild) {
                elem.removeChild(elem.firstChild);
            }
            elem.appendChild(frag);
        }

        function initBannerTextAnimations() {
            var textNodes = document.querySelectorAll(".text-effect .text-effect-p1, .text-effect .text-effect-p2, .text-effect .text-effect-p3");
            Array.prototype.forEach.call(textNodes, function(node) {
                textAnimation(node);
            });
        }

        var bannerTextExitMs = 800;
        var bannerExitTimer = null;
        var bannerTextInitialShown = false;

        function showActiveBannerText(textId) {
            if (!textId) {
                return;
            }
            var incoming = document.getElementById(textId);
            if (!incoming) {
                return;
            }

            if (bannerExitTimer) {
                window.clearTimeout(bannerExitTimer);
                bannerExitTimer = null;
            }

            var blocks = document.querySelectorAll('.text-effect');
            var outgoing = null;
            var i;
            for (i = 0; i < blocks.length; i++) {
                var b = blocks[i];
                if (b.classList.contains('splitting') && !b.classList.contains('is-exiting') && b !== incoming) {
                    outgoing = b;
                    break;
                }
            }

            for (i = 0; i < blocks.length; i++) {
                var block = blocks[i];
                if (block === incoming) {
                    continue;
                }
                if (outgoing && block === outgoing) {
                    block.classList.add('is-exiting');
                } else {
                    block.classList.remove('splitting', 'is-exiting');
                }
            }

            incoming.classList.remove('is-exiting');

            if (outgoing) {
                bannerExitTimer = window.setTimeout(function() {
                    outgoing.classList.remove('splitting', 'is-exiting');
                    if (outgoing.id === 'text-banner-0') {
                        outgoing.classList.remove('text-effect--static');
                    }
                    incoming.classList.add('splitting');
                    bannerExitTimer = null;
                }, bannerTextExitMs);
            } else if (
                incoming.id === 'text-banner-0' &&
                incoming.classList.contains('splitting') &&
                bannerTextInitialShown
            ) {
                return;
            } else {
                incoming.classList.add('splitting');
            }
        }

        /* hero 輪播與下方共用 script；須掛在 window 供計時器／輪播呼叫 */
        window.showActiveBannerText = showActiveBannerText;

        function bootHomeBannerText() {
            initBannerTextAnimations();
            requestAnimationFrame(function() {
                showActiveBannerText('text-banner-0');
                bannerTextInitialShown = true;
            });
        }

        window.bootHomeBannerText = bootHomeBannerText;

        /** 首屏背景輪播（圖片淡入淡出，與文案 data-bind-text 同步） */
        function initHeroVideoCarousel() {
            var autoplayDelayMs = 8000;
            var transitionMs = 1000;
            var currentIndex = 0;
            var autoTimer = null;
            var isTransitioning = false;
            var suspended = false;

            var carousel = document.getElementById('hero-video-carousel');
            if (!carousel) {
                return;
            }

            var slides = Array.prototype.slice.call(carousel.querySelectorAll('.hero-slide'));
            var textBlocks = document.querySelectorAll('.text-effect-wrap .text-effect');
            if (!slides.length) {
                return;
            }

            function syncBannerSlideAria(activeIndex) {
                Array.prototype.forEach.call(slides, function(slide, idx) {
                    slide.setAttribute('aria-hidden', idx === activeIndex ? 'false' : 'true');
                });
                Array.prototype.forEach.call(textBlocks, function(block, idx) {
                    block.setAttribute('aria-hidden', idx === activeIndex ? 'false' : 'true');
                });
            }

            function pauseHeroVideoCarousel() {
                if (suspended) {
                    return;
                }
                suspended = true;
                window.clearTimeout(autoTimer);
                autoTimer = null;
            }

            function resumeHeroVideoCarousel() {
                if (!suspended) {
                    return;
                }
                suspended = false;
                scheduleNext();
            }

            window.pauseHeroVideoCarousel = pauseHeroVideoCarousel;
            window.resumeHeroVideoCarousel = resumeHeroVideoCarousel;

            function activate(index) {
                Array.prototype.forEach.call(slides, function(slide, idx) {
                    slide.classList.toggle('is-active', idx === index);
                });
                syncBannerSlideAria(index);
                var textId = slides[index].getAttribute('data-bind-text');
                if (typeof window.showActiveBannerText === 'function') {
                    window.showActiveBannerText(textId);
                }
            }

            function goTo(nextIndex) {
                if (suspended || isTransitioning || nextIndex === currentIndex) {
                    return;
                }
                var outgoing = slides[currentIndex];
                var incoming = slides[nextIndex];

                isTransitioning = true;
                outgoing.classList.add('is-outgoing');
                incoming.classList.add('is-active');
                syncBannerSlideAria(nextIndex);

                if (typeof window.showActiveBannerText === 'function') {
                    window.showActiveBannerText(incoming.getAttribute('data-bind-text'));
                }

                window.setTimeout(function() {
                    outgoing.classList.remove('is-active', 'is-outgoing');
                    currentIndex = nextIndex;
                    isTransitioning = false;
                }, transitionMs);
            }

            function scheduleNext() {
                window.clearTimeout(autoTimer);
                autoTimer = null;
                if (suspended) {
                    return;
                }
                autoTimer = window.setTimeout(function tick() {
                    if (suspended) {
                        return;
                    }
                    goTo((currentIndex + 1) % slides.length);
                    if (!suspended) {
                        autoTimer = window.setTimeout(tick, autoplayDelayMs);
                    }
                }, autoplayDelayMs);
            }

            activate(0);
            scheduleNext();
        }

        function startHomeBannerTextAndHero() {
            bootHomeBannerText();
            initHeroVideoCarousel();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', startHomeBannerTextAndHero);
        } else {
            startHomeBannerTextAndHero();
        }

        window.addEventListener('load', function onBannerLoadFallback() {
            var first = document.getElementById('text-banner-0');
            if (first && !first.classList.contains('splitting')) {
                bootHomeBannerText();
            }
        });
    </script>

    <script>
        (function() {
            var isMobile = window.matchMedia('(max-width: 768px)').matches;
            var coreItems = $('.index-banner .core-item');
            if (!coreItems.length) {
                return;
            }

            var currentIndex = 0;
            var autoRotateTimer = null;
            var clickTimer = null;
            var isPaused = false;

            function removeAllAnimate() {
                coreItems.removeClass('core-item--animate');
            }

            function addAnimate(index) {
                removeAllAnimate();
                coreItems.eq(index).addClass('core-item--animate');
                currentIndex = index;
            }

            function nextBox() {
                currentIndex = (currentIndex + 1) % coreItems.length;
                addAnimate(currentIndex);
            }

            function startAutoRotate() {
                if (autoRotateTimer) {
                    clearInterval(autoRotateTimer);
                }
                isPaused = false;
                autoRotateTimer = setInterval(function() {
                    if (!isPaused) {
                        nextBox();
                    }
                }, 8000);
            }

            function stopAutoRotate() {
                if (autoRotateTimer) {
                    clearInterval(autoRotateTimer);
                    autoRotateTimer = null;
                }
            }

            if (!isMobile) {
                coreItems.each(function(index) {
                    var $box = $(this);
                    $box.on('mouseenter', function() {
                        isPaused = true;
                        stopAutoRotate();
                        removeAllAnimate();
                        $box.addClass('core-item--animate');
                        currentIndex = index;
                    });
                    $box.on('mouseleave', function() {
                        startAutoRotate();
                    });
                });
            } else {
                coreItems.each(function(index) {
                    var $box = $(this);
                    $box.on('click', function() {
                        if (clickTimer) {
                            clearTimeout(clickTimer);
                        }
                        stopAutoRotate();
                        removeAllAnimate();
                        $box.addClass('core-item--animate');
                        currentIndex = index;
                        clickTimer = setTimeout(function() {
                            startAutoRotate();
                        }, 8000);
                    });
                });
            }

            $(document).ready(function() {
                addAnimate(0);
                setTimeout(function() {
                    startAutoRotate();
                }, 3000);
            });
        })();
    </script>
    <script>
        (function initLunboSlider() {
            var root = document.querySelector('.lunbo .reviews-body');
            if (!root) return;
            var track = root.querySelector('.evaluate');
            if (!track) return;
            var prev = root.querySelector('[data-carousel-prev]');
            var next = root.querySelector('[data-carousel-next]');
            if (!prev || !next) return;

            var slides = Array.prototype.slice.call(track.querySelectorAll('.sef'));
            var total = slides.length;
            if (!total) return;

            var current = 0;
            var isAnimating = false;

            function isMobile() {
                return window.innerWidth <= 1024;
            }

            function setFocusByCurrent() {
                slides.forEach(function(slide, index) {
                    slide.classList.toggle('focus', index === current);
                });
            }

            function getCardWidth() {
                if (isMobile()) return Math.min(500, root.clientWidth - 80);
                return 500;
            }

            function getGap() {
                return isMobile() ? 10 : 30;
            }

            function applyLayout() {
                var cardWidth = getCardWidth();
                var gap = getGap();
                var step = cardWidth + gap;
                var startOffset = isMobile() ? (root.clientWidth - cardWidth) / 2 : gap;

                track.style.gap = gap + 'px';
                slides.forEach(function(slide) {
                    slide.style.flexBasis = cardWidth + 'px';
                    slide.style.width = cardWidth + 'px';
                    slide.style.maxWidth = cardWidth + 'px';
                    slide.style.position = '';
                    slide.style.left = '';
                    slide.style.top = '';
                    slide.style.opacity = '';
                    slide.style.filter = '';
                    slide.style.zIndex = '';
                    slide.style.transform = '';
                });

                track.style.transform = 'translate3d(' + (startOffset - current * step) + 'px,0,0)';
                setFocusByCurrent();
            }

            function move(dir) {
                if (isAnimating) return;
                var nextIndex = current + dir;
                if (nextIndex < 0 || nextIndex > total - 1) return;
                isAnimating = true;
                current = nextIndex;
                applyLayout();
                window.setTimeout(function() {
                    isAnimating = false;
                }, 460);
            }

            prev.addEventListener('click', function() { move(-1); });
            next.addEventListener('click', function() { move(1); });
            window.addEventListener('resize', function() { applyLayout(); });

            setFocusByCurrent();
            applyLayout();
        })();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var videos = document.querySelectorAll('.js-lazy-video');
            if (!videos.length) {
                return;
            }

            function loadAndPlay(video) {
                if (!video.dataset.src) {
                    return;
                }

                if (video.dataset.loaded !== '1') {
                    video.src = video.dataset.src;
                    video.load();
                    video.dataset.loaded = '1';
                }

                if (video.paused) {
                    video.play().catch(function() {});
                }
            }

            if (!('IntersectionObserver' in window)) {
                Array.prototype.forEach.call(videos, loadAndPlay);
                return;
            }

            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    var video = entry.target;
                    if (entry.isIntersecting) {
                        loadAndPlay(video);
                    } else if (!video.paused) {
                        video.pause();
                    }
                });
            }, {
                root: null,
                rootMargin: '200px 0px',
                threshold: 0.15
            });

            Array.prototype.forEach.call(videos, function(video) {
                observer.observe(video);
            });
        });
    </script>

    <script>
        (function() {
            var observeItems = document.querySelectorAll('.mon');
            if (!observeItems.length) {
                return;
            }

            var processed = new WeakSet();

            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (!entry.isIntersecting) {
                        return;
                    }
                    var el = entry.target;
                    if (processed.has(el)) {
                        return;
                    }

                    el.classList.add('now');
                    processed.add(el);
                });
            }, {
                root: null,
                rootMargin: '-20% 0px',
                threshold: 0.5
            });

            Array.prototype.forEach.call(observeItems, function(el) {
                observer.observe(el);
            });
        })();
    </script>

<script>
(function() {
    var weightLine = document.querySelector('.weight-line');

    if (!weightLine) {
        return;
    }

    var lineDraw = weightLine.querySelector('#weightLineDraw');
    var dotMove = weightLine.querySelector('#weightDotMove');
    var fillFade = weightLine.querySelector('#weightFillFade');

    var pulseAnimations = weightLine.querySelectorAll('.weight-line-pulse animate');

    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                lineDraw.beginElement();
                dotMove.beginElement();
                fillFade.beginElement();

                Array.prototype.forEach.call(pulseAnimations, function(animation) {
                    animation.beginElement();
                });

                observer.unobserve(weightLine);
            }
        });
    }, {
        threshold: 0.35
    });

    observer.observe(weightLine);
})();
</script>
@stop


@section('content')
<main class="page-index">
    <section class="index-banner" data-track-section-view data-track-section="home.hero" data-track-section-label="首屏 Banner">

        <h1><span class="hbrand">Orlistat</span>羅氏鮮減肥藥<span>安全有效阻斷脂肪吸收</span></h1>
        <ul class="text-effect-wrap" role="list" aria-label="羅氏鮮產品特點說明">
            <li class="text-effect text-effect--static" id="text-banner-0" aria-hidden="false">
                <strong class="text-effect-p1">安全減肥</strong>
                <p class="text-effect-p2"><svg class="tickicon" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-tickicon"></use></svg>上市近30年，全球累計數億人次使用數據</p>
                <p class="text-effect-p3"><svg class="tickicon" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-tickicon"></use></svg>歐盟EMA、美國FDA等多國權威認證對人體安全</p>
            </li>
            <li class="text-effect" id="text-banner-1" aria-hidden="true">
                <strong class="text-effect-p1">有效減肥</strong>
                <p class="text-effect-p2"><svg class="tickicon" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-tickicon"></use></svg>臨床醫師首選合法減肥輔助用藥</p>
                <p class="text-effect-p3"><svg class="tickicon" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-tickicon"></use></svg>有效阻斷約30%的脂肪吸收</p>
            </li>
            <li class="text-effect" id="text-banner-2" aria-hidden="true">
                <strong class="text-effect-p1">健康減肥</strong>
                <p class="text-effect-p2"><svg class="tickicon" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-tickicon"></use></svg>不脫水，不口渴，不影響食慾</p>
                <p class="text-effect-p3"><svg class="tickicon" viewBox="0 0 1024 1024" aria-hidden="true"><use href="#icon-tickicon"></use></svg>無須斷食動刀，健康排出油脂</p>
            </li>
        </ul>
        <a href="/product" class="btn-ef1" data-observer="首頁-立即訂購" data-track-section="home.hero" data-track-name="home.hero.order_btn">立即訂購<svg class="btn-icon buy-icon" aria-hidden="true"><use href="#icon-buyicon"></use></svg></a>
        <div class="hero-carousel" id="hero-video-carousel" role="region" aria-roledescription="carousel" aria-label="羅氏鮮產品特點輪播">
            <img
                class="hero-slide is-active"
                src="{{ asset('static/video/poster1.webp') }}"
                width="1920"
                height="1080"
                alt="羅氏鮮安全減肥"
                data-bind-text="text-banner-0"
                aria-hidden="false"
                decoding="async"
                fetchpriority="high"
            >
            <img
                class="hero-slide"
                src="{{ asset('static/video/poster2.webp') }}"
                width="1920"
                height="1080"
                alt="羅氏鮮有效減肥"
                data-bind-text="text-banner-1"
                aria-hidden="true"
                decoding="async"
            >
            <img
                class="hero-slide"
                src="{{ asset('static/video/poster3.webp') }}"
                width="1920"
                height="1080"
                alt="羅氏鮮健康減肥"
                data-bind-text="text-banner-2"
                aria-hidden="true"
                decoding="async"
            >
        </div>
        @include('web.widgets.core-sec', ['variant' => 'hero'])
        <div class="slogan-box">
            {{-- 勿在 .slogan 的 flex 子項之間留空白／換行，否則會產生匿名 flex 文字節點，擠高區塊並遮到 hero 影片 --}}
            <p class="slogan">@foreach(preg_split('//u', '妳值得擁有更好的身材', -1, PREG_SPLIT_NO_EMPTY) as $sloganChar)<span class="slogan__char">{{ $sloganChar }}</span>@endforeach</p>
            <div class="scroll-down">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </section>

    <section class="suit" data-track-section-view data-track-section="home.suit" data-track-section-label="減肥困擾">
        <div class="content-wrap">
            <p class="en-title">Oops...</p>
            <h2 class="sec-title">這些減肥困擾<span class="mon">大多數人正在經歷</span></h2>
            <p class="sec-content">當身體脂肪比偏高。多數人會開始嘗試各種減肥方法，例如節食、運動、代餐、減肥藥甚至抽脂，但實際上，很多減肥失敗的原因，並不是不夠努力，而是方法用錯了。</p>
            <p class="sec-content">以下這些常見的減肥卡關狀況，如果你中了其中一項，代表你可能需要重新選擇更適合的減肥方式。</p>
        </div>
        <ul class="suit-list">
            <li class="suit-item mon">
                <div class="suit-item-img">
                    <img loading="lazy" decoding="async" src="/static/img/suit1.webp" alt="已經吃很少 體重卻完全不動">
                </div>
                <h3 class="suit-item-title">已經吃很少<span>體重卻完全不動</span></h3>
                <p class="suit-item-content">少吃、節食都有做，熱量也在控制，但體重就是卡住不下降，一放鬆，反而更快反彈。</p>
            </li>
            <li class="suit-item mon">
                <div class="suit-item-img">
                    <img loading="lazy" decoding="async" src="/static/img/suit2.webp" alt="每天外食 油脂根本控制不了">
                </div>
                <h3 class="suit-item-title">每天外食<span>油脂根本控制不了</span></h3>
                <p class="suit-item-content">便當、外送、聚餐應酬，幾乎每餐都偏油膩。明知道飲食油膩，卻不可能每餐都能控制。</p>
            </li>
            <li class="suit-item mon">
                <div class="suit-item-img">
                    <img loading="lazy" decoding="async" src="/static/img/suit3.webp" alt="有在運動 效果慢到撐不下去">
                </div>
                <h3 class="suit-item-title">有在運動<span>效果慢到撐不下去</span></h3>
                <p class="suit-item-content">跑步、重訓都有做，撐了好幾個月，但體脂卻只降一點點，慢到開始懷疑自己根本瘦不下來。</p>
            </li>
            <li class="suit-item mon">
                <div class="suit-item-img">
                    <img loading="lazy" decoding="async" src="/static/img/suit4.webp" alt="手術抽脂怕傷身 也不敢亂吃減肥藥">
                </div>
                <h3 class="suit-item-title">手術抽脂怕傷身<span>也不敢亂吃減肥藥</span></h3>
                <p class="suit-item-content">吃藥怕等副作用，抽脂手術怕後遺症影響身體，想改善體態，卻又不想拿健康去冒險。</p>
            </li>
            <li class="suit-item mon">
                <div class="suit-item-img">
                    <img loading="lazy" decoding="async" src="/static/img/suit5.webp" alt="瘦下來沒多久 體重又全部胖回來">
                </div>
                <h3 class="suit-item-title">瘦下來沒多久<span>體重又全部胖回來</span></h3>
                <p class="suit-item-content">節食、運動好不容易才瘦一點，只要恢復正常生活，體重很快又反彈回去，反反覆覆很累。</p>
            </li>
            <li class="suit-item mon">
                <div class="suit-item-img">
                    <img loading="lazy" decoding="async" src="/static/img/suit6.webp" alt="產後加年齡增長 體重也越來越難控制">
                </div>
                <h3 class="suit-item-title">產後加年齡增長<span>體重也越來越難控制</span></h3>
                <p class="suit-item-content">以前少吃幾天就能瘦下來，現在明明吃得差不多，體重卻一直往上升，代謝明顯不像以前。</p>
            </li>
        </ul>
    </section>
    <section class="about" data-track-section-view data-track-section="home.about" data-track-section-label="關於羅氏鮮">
        <p class="en-title">About XENICAL</p>
        <h2 class="sec-title">XENICAL® 羅氏鮮/羅鮮子<span class="mon">隨餐一顆 輕鬆減肥</span></h2>
        <div class="sec-content-wrap">
            @php
                $homeAboutHtml = app('cache.config')->get('home_about') ?? '';
                $homeAboutHtml = preg_replace_callback('/<p\b([^>]*)>/i', function ($matches) {
                    $attrs = $matches[1] ?? '';
                    if (preg_match('/\bclass\s*=\s*([\'"])(.*?)\1/i', $attrs, $classMatch)) {
                        $classes = preg_split('/\s+/', trim($classMatch[2])) ?: [];
                        if (!in_array('sec-content', $classes, true)) {
                            $classes[] = 'sec-content';
                        }
                        $newClass = 'class=' . $classMatch[1] . implode(' ', $classes) . $classMatch[1];
                        $newAttrs = preg_replace('/\bclass\s*=\s*([\'"])(.*?)\1/i', $newClass, $attrs, 1);
                        return '<p' . $newAttrs . '>';
                    }
                    return '<p class="sec-content"' . $attrs . '>';
                }, $homeAboutHtml);
            @endphp
            {!! $homeAboutHtml !!}
            <ul class="about-list">
                <li class="about-item"><svg class="righticon" viewBox="0 0 1024 1024"><use href="#icon-righticon"></use></svg>不需要極端節食，羅氏鮮能讓你在正常飲食下也能減少油脂熱量攝入。這不是快速減肥，而是穩定的控制熱量，打破復胖的惡性循環。</li>
                <li class="about-item"><svg class="righticon" viewBox="0 0 1024 1024"><use href="#icon-righticon"></use></svg>羅氏鮮非常適合高油脂飲食生活，在進食中或餐後一小時內服用，直接讓腸胃少吸收進口的油脂。</li>
                <li class="about-item"><svg class="righticon" viewBox="0 0 1024 1024"><use href="#icon-righticon"></use></svg>當減脂過慢，改變吸收熱量是最高效的策略。透過減少脂肪吸收，能幫你從物理層面拉開熱量赤字，效果自然事半功倍。</li>
                <li class="about-item"><svg class="righticon" viewBox="0 0 1024 1024"><use href="#icon-righticon"></use></svg>口服羅氏鮮不進入血液循環，僅在腸道發揮作用，不影響中樞神經，不腹瀉脫水、不口乾口渴，是目前安全性相對極高、且受到全球廣泛臨床驗證的非侵入式減肥方案。</li>

            </ul>
            <p class="user-count"><span><span class="user-count-number" data-count="{{ $userCount }}">{{ number_format($userCount) }}</span>人</span><span class="user-count-text">選擇羅氏鮮成功達到瘦身目標</span></p>
            
            <a class="btn-ef2" href="/about" data-observer="查看羅氏鮮詳細介紹" data-track-section="home.about" data-track-name="home.about.more">查看羅氏鮮詳細介紹<svg class="arrowicon"><use href="#icon-arrowicon"/></svg></a>
            <div class="about-banner">
                <img class="about-banner-img" src="/static/img/about.webp" loading="lazy" decoding="async" width="1024" height="1024" alt="羅氏鮮減肥效果">
                <svg class="weight-line" viewBox="0 0 360 220" preserveAspectRatio="none">
                    <defs>

                        <linearGradient id="weight-gradient" x1="0" y1="0" x2="1" y2="0">
                            <stop offset="0%" class="weight-stop-start"/>
                            <stop offset="100%" class="weight-stop-end"/>
                        </linearGradient>


                        <linearGradient id="weight-fill-gradient" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" class="weight-fill-start"/>
                            <stop offset="100%" class="weight-fill-end"/>
                        </linearGradient>

                        <linearGradient id="weight-fade-gradient" x1="0" y1="0" x2="1" y2="0">
                            <stop offset="0%" stop-color="black" stop-opacity="0"/>
                            <stop offset="10%" stop-color="white"/>
                            <stop offset="90%" stop-color="white"/>
                            <stop offset="100%" stop-color="black" stop-opacity="0"/>
                        </linearGradient>

                        <mask id="weight-fade-mask">
                            <rect
                                width="360"
                                height="220"
                                fill="url(#weight-fade-gradient)"
                            />
                        </mask>

                        <filter id="weight-dot-glow" x="-200%" y="-200%" width="400%" height="400%">
                            <feGaussianBlur stdDeviation="4" result="blur"/>
                            <feMerge>
                                <feMergeNode in="blur"/>
                                <feMergeNode in="SourceGraphic"/>
                            </feMerge>
                        </filter>

                    </defs>
                    <g class="weight-grid">

                        <line x1="0" y1="50" x2="360" y2="50"/>
                        <line x1="0" y1="95" x2="360" y2="95"/>
                        <line x1="0" y1="140" x2="360" y2="140"/>
                        <line x1="0" y1="185" x2="360" y2="185"/>

                    </g>
                    <path
                        class="weight-fill"
                        mask="url(#weight-fade-mask)"
                        d="
                            M0,220
                            L0,58

                            Q110,60 180,88
                            T360,165

                            L360,220
                            Z
                        "
                        fill="url(#weight-fill-gradient)"
                        opacity="0"
                    >
                        <animate
                            id="weightFillFade"
                            attributeName="opacity"
                            from="0"
                            to="1"
                            dur="1.6s"
                            begin="indefinite"
                            fill="freeze"
                        />
                    </path>
                    <path
                        class="weight-inner-line"
                        d="
                            M0,58

                            Q110,60 180,88
                            T360,165
                        "
                        pathLength="1"
                        stroke-dasharray="1"
                        stroke-dashoffset="1"
                    >
                        <animate
                            id="weightLineDraw"
                            attributeName="stroke-dashoffset"
                            from="1"
                            to="0"
                            dur="2s"
                            begin="indefinite"
                            fill="freeze"
                            calcMode="spline"
                            keyTimes="0;1"
                            keySplines="0.4 0 0.2 1"
                        />
                    </path>
                    <g class="weight-dot-group" filter="url(#weight-dot-glow)">

                        <circle class="weight-line-dot" r="5"/>

                        <circle class="weight-line-pulse" r="0">

                            <animate
                                attributeName="r"
                                from="0"
                                to="22"
                                dur="2s"
                                begin="indefinite"
                            />

                            <animate
                                attributeName="opacity"
                                from="0.7"
                                to="0"
                                dur="2s"
                                begin="indefinite"
                            />

                            <animate
                                attributeName="stroke-width"
                                from="4"
                                to="0"
                                dur="2s"
                                begin="indefinite"
                            />

                        </circle>

                        <animateMotion
                            id="weightDotMove"
                            dur="2s"
                            begin="indefinite"
                            fill="freeze"
                            path="
                                M0,58

                                Q110,60 180,88
                                T360,165
                            "
                            calcMode="spline"
                            keyTimes="0;1"
                            keySplines="0.4 0 0.2 1"
                        />

                    </g>
                </svg>
               
            </div>
        </div>
        <div class="rice-wrap">
        @include('web.widgets.rice-scroll')
        </div>
    </section>
    <section class="product-sec" aria-label="訂購羅氏鮮療程組合" data-track-section-view data-track-section="home.products" data-track-section-label="首頁產品列表">

        <p class="en-title">BUY ONLINE</p>
        <h2 class="sec-title">訂購羅氏鮮療程組合<span class="mon">實現您的瘦身目標</span></h2>
        <p class="sec-content">台灣訂購減肥藥羅氏鮮官方線上通路，無須醫師處方箋，歐洲原裝進口，購買組合療程可享受最高51%優惠</p>

        @include('web.widgets.core-sec')

        <ol class="product-list">
        @foreach($products as $goods)
            <li class="product-card" data-product-id="{{ $goods->id }}">
                @if($loop->iteration === 2)
                    <p class="choose-label"><span class="hot">時下熱門</span>83%顧客選擇該組合開始減肥計劃</p>
                @endif
                <div class="img-wrap">
                    <img src="{{ asset('uploads/'.$goods->img) }}" alt="{{ $goods->name }}">
                </div>
                <h3 class="product-title">{{ $goods->name }}<strong class="product-sub-name">{{ $goods->sub_name }}</strong></h3>
                <ul class="tags">
                    @foreach(explode('|',$goods->label) as $label)
                        <li class="tag-item">
                            <span class="tick"><svg class="tickicon" viewBox="0 0 1024 1024"><use href="#icon-tickicon"></use></svg></span>
                            <p class="tag-text">{{ $label }}</p>
                        </li>
                    @endforeach
                </ul>
                <dl class="attr">
                    @foreach($goods->attr->skip(1) as $attr)
                        <dt class="attr-name">{{ $attr->name }}</dt>
                        <dd class="attr-value">{{ $attr->value }}</dd>
                    @endforeach

                </dl>
                <div class="product-bottom">
                    <div class="price-box" data-market-price="{{ $goods->market_price }}" data-price="{{ $goods->price }}">
                        <div class="mk-price-box">
                            <p class="mk-price"><span class="twd">NT$</span>{{ $goods->market_price }}</p>
                            <p class="discount">-<span class="descount-num">{{ $goods->discount_percent }}</span>%</p>
                        </div>
                        <p class="price"><span class="twd">NT$</span><span class="price-number">{{ $goods->price }}</span></p>
                    </div>
                    <a class="btn-ef1" href="{{ url('checkout/'.$goods->id) }}" data-observer="立即訂購-{{ $goods->name }}" data-track-section="home.products" data-track-name="home.product.checkout">立即訂購<svg class="btn-icon buy-icon"><use href="#icon-buyicon"></use></svg>
                        
                    </a>
                    @if($loop->iteration === 2)
                        <span class="free-shipping">限時免運哦</span>
                    @endif 
                </div>
            </li>
        @endforeach
        </ol>
        <a href="/product" class="btn-ef2" data-observer="更多羅氏鮮組合" data-track-section="home.products" data-track-name="home.products.more">更多羅氏鮮瘦身療程組合<svg class="btn-icon"><use href="#icon-arrowicon"></use></svg></a>
        @include('web.widgets.tick-scroll')
    </section>
    <section class="how" aria-label="羅氏鮮作用機轉" data-track-section-view data-track-section="home.how" data-track-section-label="作用機轉">
        <p class="en-title">HOW TO WORK</p>
        <h2 class="sec-title">羅氏鮮安全减肥機轉<span class="mon">阻斷脂肪分解吸收</span></h2>
        <ol class="work-wrap">
            <li class="work-item mon">
                <video data-src="/static/video/work1.mp4" poster="{{ asset('static/video/work1-cover.webp') }}" preload="none" autoplay muted loop playsinline webkit-playsinline class="work-video js-lazy-video" title="威而鋼抑制PDE5酵素過程演示" aria-label="動畫展示威而鋼如何與PDE5酵素結合"></video>
                <div class="work-text">
                    <h3 class="work-title">脂肪正常分解</h3>
                    <p class="work-desc">人體在攝取高油脂食物後，需透過「脂肪酶（Lipase）」將脂肪分解，才能被小腸吸收並轉化為熱量。一旦吸收量長期高於消耗，脂肪便會累積。</p>
                </div>
                <div class="down-box">
                    <svg class="downarrow-icon" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg"><use href="#icon-downarrow-icon"/></svg>
                </div>
            </li>
            <li class="work-item mon">
                <video data-src="/static/video/work2.mp4" poster="{{ asset('static/video/work2-cover.webp') }}" preload="none" autoplay muted loop playsinline webkit-playsinline class="work-video js-lazy-video"></video>
                <div class="work-text">
                    <h3 class="work-title">羅氏鮮抑制脂肪分解</h3>
                    <p class="work-desc">羅氏鮮（Orlistat）能有效抑制腸道脂肪酶活性，使約30%的膳食脂肪無法被分解，直接隨糞便排出體外。脂肪在腸道階段就被「攔截」，還沒進入血液，就已經失去轉化為熱量的機會。由於其作用侷限於腸道，長期使用的安全結構相對明確</p>
                </div>
                <div class="down-box">
                    <svg class="downarrow-icon" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg"><use href="#icon-downarrow-icon"/></svg>
                </div>
            </li>
            <li class="work-item mon">
                <video data-src="/static/video/work3.mp4" poster="{{ asset('static/video/work3-cover.webp') }}" preload="none" autoplay muted loop playsinline webkit-playsinline class="work-video js-lazy-video"></video>
                <div class="work-text">
                    <h3 class="work-title">形成熱量赤字</h3>
                    <p class="work-desc">當每日吸收的脂肪熱量被降低後，即使飲食沒有劇烈改變，整體熱量攝取仍會下降，進而形成穩定的熱量赤字（Caloric Deficit）。透過持續降低脂肪吸收效率，減少脂肪再次堆積的速度，降低因飲食波動帶來的體重反彈風險。</p>
                </div>
            </li>
        </ol>
    </section>
    <section class="lunbo" aria-label="羅氏鮮用戶減肥見證" data-track-section-view data-track-section="home.testimonials" data-track-section-label="用戶見證">
        <p class="en-title">REAL RESULTS</p>
        <h2 class="sec-title">真實減肥效果<span class="mon">看看她們怎麼說</span></h2>
            <div class="reviews-body">
                <div class="evaluate">
                    @forelse($successCases as $case)
                    <article class="sef">
                        <h3 class="sef-title">{{ $case->duration }}{{ $case->result }}</h3>
                        <div class="compare-images">
                            <img src="{{ asset_upload(ltrim($case->before_image, '/')) }}" loading="lazy" decoding="async" alt="{{ $case->name }} 服用羅氏鮮前">
                            <p class="compare-label">服用羅氏鮮前</p>
                            <img src="{{ asset_upload(ltrim($case->after_image, '/')) }}" loading="lazy" decoding="async" alt="{{ $case->name }} 服用羅氏鮮後">
                            <p class="compare-label">服用羅氏鮮{{ $case->duration }}後</p>
                        </div>
                        <p class="note">{{ $case->content }}</p>
                        <p class="identity">{{ $case->name }}<span>/</span>{{ $case->age }}<span>/</span>{{ $case->occupation }}</p>
                        <p class="sef-footnote"><svg class="sef-icon" aria-hidden="true"><use href="#icon-sef"></use></svg>顧客留言見證</p>
                    </article>
                    @empty
                    <article class="sef">
                        <h3 class="sef-title">暫無案例</h3>
                        <p class="note">成功案例資料載入中，敬請稍後再瀏覽。</p>
                    </article>
                    @endforelse
                </div>
                <button class="switch prev-btn" type="button" data-carousel-prev aria-label="上一張" data-observer="首頁-見證-上一張" data-track-section="home.testimonials" data-track-name="home.testimonials.prev">
                    <svg><use href="#icon-arrowicon"/></svg>
                </button>
                <button class="switch next-btn" type="button" data-carousel-next aria-label="下一張" data-observer="首頁-見證-下一張" data-track-section="home.testimonials" data-track-name="home.testimonials.next">
                    <svg><use href="#icon-arrowicon"/></svg>
                </button>
            </div>
    </section>

    <section class="tdee" data-track-section-view data-track-section="home.bmi" data-track-section-label="BMI 計算入口">
        <p class="en-title">BMI CALCULATOR</p>
        <h2 class="sec-title">BMI計算<span class="mon">快速計算您的身體指數</span></h2>
        <p class="tdee-about ">
            {!! str_replace(PHP_EOL,'<br>',app('cache.config')->get('slim_about')) !!}
        </p>
        <a class="btn-ef1" href="{{ url('bmi') }}" data-observer="測試你的數據按鈕" data-track-section="home.bmi" data-track-name="home.tdee.btn">測一測你的BMI<svg class="arrowicon"><use href="#icon-arrowicon"/></svg></a>

    </section>

    {{--<section class="faq">
        <p class="en-title">Q&A</p>
        <h2 class="sec-title">羅氏鮮幫助你<span class="mon">解決所有減肥困擾</span></h2>
        @include('web.widgets.qa')
        @include('web.widgets.tick-scroll')
    </section>--}}


    <section class="news" data-track-section-view data-track-section="home.news" data-track-section-label="減肥專欄">
        <p class="en-title">Slimming Blog</p>
        <h2 class="sec-title">閱讀專欄<span class="mon">分享更多減肥知識</span></h2>
        <div class="news-wrap">
            @foreach($news as $item)
                @include('web.widgets.news-card', ['item' => $item])
            @endforeach
        </div>
        <a class="btn-ef2" href="/news" data-observer="前往專欄閱讀更多" data-track-section="home.news" data-track-name="home.news.more">前往專欄閱讀更多內容<svg class="arrowicon"><use href="#icon-arrowicon"/></svg></a>
    </section>
</main>
@include('web.widgets.update-box')
@endsection
