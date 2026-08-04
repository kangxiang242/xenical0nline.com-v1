(function (window, document) {
    'use strict';
    if (!window.XenicalTracker) return;

    function trackHeroSlide(index) {
        XenicalTracker.track('hero_slide_view', 'home.hero.slide', {
            section: 'hero',
            label: 'Banner輪播',
            metadata: { slide_index: index }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (window.Swiper) {
            document.querySelectorAll('.banner-section .swiper-container, .swiper-container').forEach(function (el, i) {
                if (i > 0) return;
                try {
                    var inst = el.swiper;
                    if (inst) {
                        trackHeroSlide(inst.activeIndex || 0);
                        inst.on('slideChange', function () { trackHeroSlide(inst.activeIndex || 0); });
                    }
                } catch (e) {}
            });
        }
        trackHeroSlide(0);
    });

    document.addEventListener('click', function (e) {
        var q = e.target.closest('.question-show, .faq-section .shrink, [data-faq-toggle]');
        if (!q) return;
        var item = q.closest('.item, .question-show');
        var id = (item && item.getAttribute('data-faq-id')) || '';
        var expanded = !(item && item.getAttribute('data-show'));
        if (q.classList.contains('shrink')) expanded = !item.querySelector('.q-desc') || item.querySelector('.q-desc').style.display === 'none';
        XenicalTracker.track('faq_toggle', 'home.faq.toggle', {
            section: 'fqa',
            label: 'FAQ展開',
            metadata: { faq_id: id, expanded: expanded ? 1 : 0 }
        });
    }, true);
})(window, document);
