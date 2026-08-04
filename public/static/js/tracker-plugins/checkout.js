(function (window) {
    'use strict';
    if (!window.XenicalTracker) return;

    document.addEventListener('change', function (e) {
        var t = e.target;
        if (!t || t.name !== 'order_type') return;
        XenicalTracker.track('delivery_type_change', 'checkout.delivery_type', {
            section: 'checkout.form',
            label: '配送方式',
            metadata: { value: t.value, product_id: XenicalTracker.getPageContext().goods_id || '' }
        });
    }, true);

    var lastOrderType = null;
    document.addEventListener('DOMContentLoaded', function () {
        var r = document.querySelector('input[name="order_type"]:checked');
        if (r) lastOrderType = r.value;
    });
    document.addEventListener('change', function (e) {
        if (!e.target || e.target.name !== 'order_type') return;
        if (lastOrderType !== null && lastOrderType !== e.target.value) {
            XenicalTracker.track('cascade_step', 'checkout.delivery_switch', {
                section: 'checkout.form',
                metadata: { step: 'order_type', changed: 1, value: e.target.value }
            });
        }
        lastOrderType = e.target.value;
    }, true);
})(window);
