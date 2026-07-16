@php
    $openFirst = $openFirst ?? false;
@endphp
@if(!empty($faqs) && count($faqs))
    <ul class="faq-list">
        @foreach($faqs as $faq)
            @php
                $rawAnswerHtml = (string) ($faq->content ?? '');
                $hasParagraphTag = preg_match('/<p\b[^>]*>/i', $rawAnswerHtml) === 1;

                $answerHtml = preg_replace_callback('/<p\b([^>]*)>/i', function ($matches) {
                    $attrs = $matches[1] ?? '';

                    if (preg_match('/\bclass\s*=\s*([\'"])(.*?)\1/i', $attrs, $classMatch)) {
                        $quote = $classMatch[1];
                        $classes = trim($classMatch[2]);
                        $classList = preg_split('/\s+/', $classes, -1, PREG_SPLIT_NO_EMPTY) ?: [];
                        if (!in_array('faq-answer__inner', $classList, true)) {
                            $classList[] = 'faq-answer__inner';
                        }
                        $mergedClasses = implode(' ', $classList);
                        $updatedAttrs = preg_replace(
                            '/\bclass\s*=\s*([\'"])(.*?)\1/i',
                            'class=' . $quote . e($mergedClasses) . $quote,
                            $attrs,
                            1
                        );

                        return '<p' . $updatedAttrs . '>';
                    }

                    return '<p class="faq-answer__inner"' . $attrs . '>';
                }, $rawAnswerHtml);

                if (!$hasParagraphTag && trim(strip_tags($rawAnswerHtml)) !== '') {
                    $answerHtml = '<p class="faq-answer__inner">' . $rawAnswerHtml . '</p>';
                }

                $isOpen = $openFirst && $loop->index === 0;
                $faqId = $loop->iteration;
            @endphp
            <li class="faq-item{{ $isOpen ? ' is-open' : '' }}">
                <h3 class="faq-item__title">
                    <button
                        type="button"
                        class="faq-question"
                        aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
                        aria-controls="faq-answer-{{ $faqId }}"
                        id="faq-question-{{ $faqId }}"
                    >
                        <span class="faq-question__text">{{ $faq->title }}</span>
                        <span class="faq-arrow" aria-hidden="true"></span>
                    </button>
                </h3>
                <div
                    class="faq-answer"
                    id="faq-answer-{{ $faqId }}"
                    role="region"
                    aria-labelledby="faq-question-{{ $faqId }}"
                    @unless($isOpen) hidden @endunless
                >
                    {!! $answerHtml !!}
                </div>
            </li>
        @endforeach
    </ul>

@php
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "FAQPage",
        "mainEntity" => collect($faqs)->map(function($faq){
            return [
                "@type" => "Question",
                "name" => strip_tags($faq->title),
                "acceptedAnswer" => [
                    "@type" => "Answer",
                    "text" => strip_tags($faq->content)
                ]
            ];
        })->values()->toArray()
    ];
@endphp

@push('qa-js')
<script>
    (function () {
        var WRAP_SELECTOR = '.faq-list';
        var ITEM_SELECTOR = '.faq-item';
        var BUTTON_SELECTOR = '.faq-question';
        var PANEL_SELECTOR = '.faq-answer';
        var ENHANCED_CLASS = 'is-qa-enhanced';
        var READY_CLASS = 'is-qa-ready';
        var ANIMATING_CLASS = 'is-animating';
        var COLLAPSING_CLASS = 'is-collapsing';

        function getButton(item) {
            return item.querySelector(BUTTON_SELECTOR);
        }

        function isItemOpen(item) {
            var btn = getButton(item);
            return !!(btn && btn.getAttribute('aria-expanded') === 'true');
        }

        function setItemOpen(item, open) {
            var btn = getButton(item);
            if (btn) {
                btn.setAttribute('aria-expanded', open ? 'true' : 'false');
            }
            if (open) {
                item.classList.add('is-open');
            } else {
                item.classList.remove('is-open');
            }
        }

        function setHeight(panel, value) {
            panel.style.height = Math.max(0, Math.round(value)) + 'px';
        }

        function transitionHeight(panel, toHeight) {
            var fromHeight = panel.getBoundingClientRect().height;

            if (Math.abs(fromHeight - toHeight) < 1) {
                setHeight(panel, toHeight);
                return;
            }

            panel.dataset.animating = '1';
            panel.classList.add(ANIMATING_CLASS);
            setHeight(panel, fromHeight);
            panel.offsetHeight;

            requestAnimationFrame(function () {
                setHeight(panel, toHeight);
            });

            var onEnd = function (event) {
                if (event.propertyName !== 'height') {
                    return;
                }
                panel.dataset.animating = '';
                panel.classList.remove(ANIMATING_CLASS);
                panel.removeEventListener('transitionend', onEnd);
            };

            panel.addEventListener('transitionend', onEnd);
        }

        function openItem(item, animate) {
            var panel = item.querySelector(PANEL_SELECTOR);
            if (!panel) {
                return;
            }

            item.classList.remove(COLLAPSING_CLASS);
            panel.removeAttribute('hidden');
            setItemOpen(item, true);
            var targetHeight = panel.scrollHeight;

            if (animate) {
                transitionHeight(panel, targetHeight);
            } else {
                setHeight(panel, targetHeight);
            }
        }

        function closeItem(item, animate) {
            var panel = item.querySelector(PANEL_SELECTOR);
            if (!panel) {
                return;
            }

            var currentHeight = panel.getBoundingClientRect().height || panel.scrollHeight;

            if (!animate) {
                setHeight(panel, 0);
                setItemOpen(item, false);
                panel.setAttribute('hidden', '');
                item.classList.remove(COLLAPSING_CLASS);
                return;
            }

            item.classList.add(COLLAPSING_CLASS);
            panel.dataset.animating = '1';
            panel.classList.add(ANIMATING_CLASS);
            setHeight(panel, currentHeight);
            panel.offsetHeight;

            requestAnimationFrame(function () {
                setHeight(panel, 0);
            });

            var onEnd = function (event) {
                if (event.propertyName !== 'height') {
                    return;
                }
                setItemOpen(item, false);
                panel.setAttribute('hidden', '');
                item.classList.remove(COLLAPSING_CLASS);
                panel.dataset.animating = '';
                panel.classList.remove(ANIMATING_CLASS);
                panel.removeEventListener('transitionend', onEnd);
            };

            panel.addEventListener('transitionend', onEnd);
        }

        function closeSiblings(wrap, currentItem) {
            wrap.querySelectorAll(ITEM_SELECTOR).forEach(function (item) {
                if (item !== currentItem && isItemOpen(item)) {
                    closeItem(item, true);
                }
            });
        }

        function recalcOpenPanels(items, animate) {
            items.forEach(function (item) {
                if (!isItemOpen(item)) {
                    return;
                }
                var panel = item.querySelector(PANEL_SELECTOR);
                if (!panel) {
                    return;
                }
                var nextHeight = panel.scrollHeight;
                if (animate) {
                    transitionHeight(panel, nextHeight);
                } else {
                    setHeight(panel, nextHeight);
                }
            });
        }

        function initAccordion(wrap) {
            var items = Array.prototype.slice.call(wrap.querySelectorAll(ITEM_SELECTOR));
            if (!items.length) {
                return;
            }

            wrap.classList.add(ENHANCED_CLASS);

            items.forEach(function (item) {
                var panel = item.querySelector(PANEL_SELECTOR);
                if (!panel) {
                    return;
                }
                item.classList.remove(COLLAPSING_CLASS);
                panel.removeAttribute('hidden');
                setHeight(panel, isItemOpen(item) ? panel.scrollHeight : 0);
                if (!isItemOpen(item)) {
                    panel.setAttribute('hidden', '');
                }
            });

            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    wrap.classList.add(READY_CLASS);
                });
            });

            wrap.addEventListener('click', function (event) {
                var btn = event.target.closest(BUTTON_SELECTOR);
                if (!btn || !wrap.contains(btn)) {
                    return;
                }

                var item = btn.closest(ITEM_SELECTOR);
                if (!item) {
                    return;
                }

                event.preventDefault();

                if (isItemOpen(item)) {
                    closeItem(item, true);
                    return;
                }

                closeSiblings(wrap, item);
                openItem(item, true);
            });

            if ('ResizeObserver' in window) {
                var observer = new ResizeObserver(function (entries) {
                    var targets = new Set();

                    entries.forEach(function (entry) {
                        var item = entry.target.closest(ITEM_SELECTOR);
                        if (item && isItemOpen(item)) {
                            targets.add(item);
                        }
                    });

                    targets.forEach(function (item) {
                        var panel = item.querySelector(PANEL_SELECTOR);
                        if (!panel || panel.dataset.animating === '1') {
                            return;
                        }
                        transitionHeight(panel, panel.scrollHeight);
                    });
                });

                items.forEach(function (item) {
                    var panel = item.querySelector(PANEL_SELECTOR);
                    if (!panel) {
                        return;
                    }
                    var observedNode = panel.querySelector('.faq-answer__inner') || panel;
                    observer.observe(observedNode);
                });
            }

            if (document.fonts && document.fonts.ready) {
                document.fonts.ready
                    .then(function () {
                        recalcOpenPanels(items, true);
                    })
                    .catch(function () {});
            }

            var resizeRaf = null;
            window.addEventListener(
                'resize',
                function () {
                    if (resizeRaf) {
                        cancelAnimationFrame(resizeRaf);
                    }
                    resizeRaf = requestAnimationFrame(function () {
                        recalcOpenPanels(items, false);
                        resizeRaf = null;
                    });
                },
                { passive: true }
            );
        }

        function bootstrap() {
            var wraps = document.querySelectorAll(WRAP_SELECTOR);
            wraps.forEach(initAccordion);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', bootstrap, { once: true });
        } else {
            bootstrap();
        }
    })();
</script>
@endpush

@push('schema')
<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush

@endif

