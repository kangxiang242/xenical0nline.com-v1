(function (window, document) {
    'use strict';
    if (!window.XenicalTracker) return;

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.count-btn a, .count-btn button, [data-track-calc-start]');
        if (!btn) return;
        XenicalTracker.track('calc_start', 'calc.start', {
            section: 'calc.form',
            label: '開始計算',
            metadata: { calc_type: 'bmi' }
        });
    }, true);

    window.addEventListener('xo:calc_complete', function () {
        XenicalTracker.track('calc_complete', 'calc.complete', {
            section: 'calc.result',
            label: '計算完成',
            metadata: { calc_type: 'bmi' }
        });
    });

    document.addEventListener('click', function (e) {
        var a = e.target.closest('.go-btn, [data-track-calc-recommend]');
        if (!a) return;
        var m = (a.getAttribute('href') || '').match(/checkout\/(\d+)/);
        XenicalTracker.track('calc_recommend_click', 'calc.recommend.click', {
            section: 'calc.result',
            label: '推薦商品訂購',
            metadata: { calc_type: 'bmi', product_id: m ? m[1] : '', recommend_product_id: m ? m[1] : '' }
        });
    }, true);
})(window, document);
