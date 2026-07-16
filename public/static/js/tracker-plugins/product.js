(function () {
    'use strict';
    if (!window.XenicalTracker) return;
    var T = XenicalTracker;
    var page = window.__TRACKING_PAGE__ || {};
    var pid = page.goods_id || '';

    var sticky = document.querySelector('.footer-buy');
    if (sticky && window.IntersectionObserver) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                if (!en.isIntersecting || sticky.getAttribute('data-sticky-tracked')) return;
                sticky.setAttribute('data-sticky-tracked', '1');
                T.track('sticky_buy_view', 'product.sticky.view', {
                    section: 'sticky_footer',
                    metadata: { product_id: pid }
                });
            });
        }, { threshold: 0.35 });
        io.observe(sticky);
    }

    document.querySelectorAll('.footer-buy a.btn-ef1').forEach(function (a) {
        if (a.getAttribute('data-track-name')) return;
        a.setAttribute('data-observer', '立即訂購-底部');
        a.setAttribute('data-track-section', 'sticky_footer');
        a.setAttribute('data-track-name', 'product.sticky.checkout');
    });
})();
