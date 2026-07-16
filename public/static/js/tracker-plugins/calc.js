(function () {
    'use strict';
    if (!window.XenicalTracker) return;
    var T = XenicalTracker;
    var page = window.__TRACKING_PAGE__ || {};
    var calcType = page.page_type || 'calc';

    document.querySelectorAll('.btn.count, button.count').forEach(function (btn) {
        btn.addEventListener('click', function () {
            T.track('calc_start', 'calc.start', { section: 'calculator', metadata: { calc_type: calcType } });
        });
    });

    var resultEl = document.querySelector('.result-num, .result .value, #bmi-value');
    if (resultEl) {
        var obs = new MutationObserver(function () {
            var text = (resultEl.textContent || '').trim();
            if (text && text !== '?' && !resultEl.getAttribute('data-calc-tracked')) {
                resultEl.setAttribute('data-calc-tracked', '1');
                T.track('calc_complete', 'calc.complete', { section: 'calculator', metadata: { calc_type: calcType } });
            }
        });
        obs.observe(resultEl, { childList: true, subtree: true, characterData: true });
    }

    document.querySelectorAll('a.go-btn, a[data-track-name="calc.recommend.buy"]').forEach(function (a) {
        a.addEventListener('click', function () {
            var m = location.pathname.match(/checkout\/(\d+)/);
            T.track('calc_recommend_click', 'calc.recommend.click', {
                section: 'calculator',
                metadata: { calc_type: calcType, recommend_product_id: m ? m[1] : '' }
            });
        });
    });
})();
