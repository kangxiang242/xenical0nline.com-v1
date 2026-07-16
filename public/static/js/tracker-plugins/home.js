(function () {
    'use strict';
    if (!window.XenicalTracker) return;
    var T = XenicalTracker;

    var carousel = document.getElementById('hero-video-carousel');
    if (carousel) {
        var slides = carousel.querySelectorAll('.hero-slide');
        var lastIdx = -1;
        function trackSlide() {
            var active = carousel.querySelector('.hero-slide.is-active, .hero-slide.active');
            if (!active) return;
            var idx = Array.prototype.indexOf.call(slides, active);
            if (idx === lastIdx) return;
            lastIdx = idx;
            T.track('hero_slide_view', 'home.hero.slide', {
                section: 'home.hero',
                metadata: { slide_index: idx }
            });
        }
        trackSlide();
        var mo = new MutationObserver(trackSlide);
        mo.observe(carousel, { attributes: true, subtree: true, attributeFilter: ['class'] });
        setInterval(trackSlide, 3000);
    }

    document.querySelectorAll('.faq-question').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.id || btn.getAttribute('aria-controls') || '';
            var willExpand = btn.getAttribute('aria-expanded') !== 'true';
            T.track('faq_toggle', 'home.faq.toggle', {
                section: 'home.faq',
                metadata: { faq_id: id, expanded: willExpand ? 1 : 0 }
            });
        });
    });
})();
