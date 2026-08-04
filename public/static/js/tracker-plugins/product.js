(function (window, document) {
    'use strict';
    if (!window.XenicalTracker) return;

    document.addEventListener('DOMContentLoaded', function () {
        var sticky = document.querySelector('.checkout-btn, .sticky-footer .checkout-btn, [data-track-sticky-buy]');
        if (!sticky || !window.IntersectionObserver) return;
        var ctx = XenicalTracker.getPageContext();
        var sent = false;
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                if (sent || !en.isIntersecting) return;
                sent = true;
                XenicalTracker.track('sticky_buy_view', 'product.sticky.view', {
                    section: 'sticky_footer',
                    label: 'Sticky購買條曝光',
                    metadata: { product_id: ctx.goods_id || '' }
                });
            });
        }, { threshold: 0.5 });
        io.observe(sticky);
    });

    document.addEventListener('click', function (e) {
        var a = e.target.closest('.checkout-btn[data-track-sticky-buy], [data-track-sticky-buy]');
        if (!a || a.getAttribute('data-track-name')) return;
        var ctx = XenicalTracker.getPageContext();
        XenicalTracker.track('sticky_buy_click', 'product.sticky.click', {
            section: 'sticky_footer',
            label: 'Sticky立即訂購',
            metadata: { product_id: ctx.goods_id || '' }
        });
    }, true);
})(window, document);
