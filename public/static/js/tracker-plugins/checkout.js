(function () {
    'use strict';
    if (!window.XenicalTracker) return;
    var T = XenicalTracker;

    function patchXarea() {
        if (typeof window.loadCity !== 'function' && typeof window.xareaLoad !== 'function') {
            ['#city', '#county', '#street'].forEach(function (sel) {
                var el = document.querySelector(sel);
                if (el) {
                    el.addEventListener('change', function () {
                        var step = el.id || sel.replace('#', '');
                        T.markAreaLoad(step, el.value && el.value !== '0' ? 'ok' : 'fail');
                    });
                }
            });
            return;
        }
    }

    patchXarea();
})();
