(function (window, document) {
    'use strict';
    if (!window.XenicalTracker) return;

    var MILESTONES = [25, 50, 75, 100];
    var sent = {};
    var ctx = XenicalTracker.getPageContext();

    function targetEl() {
        return document.querySelector('[data-track-scroll-target], .news-content, .page-body');
    }

    function readPct() {
        var el = targetEl();
        if (!el) return XenicalTracker.scrollPct ? XenicalTracker.scrollPct() : 0;
        var st = el.scrollTop || 0, ch = el.clientHeight || 0, sh = el.scrollHeight || 0;
        if (sh <= ch) return 100;
        return Math.min(100, Math.max(0, Math.floor(((st + ch) / sh) * 100)));
    }

    function onReadScroll() {
        var p = readPct();
        XenicalTracker.setMaxReadProgress(p);
        for (var i = 0; i < MILESTONES.length; i++) {
            var m = MILESTONES[i];
            if (p >= m && !sent[m]) {
                sent[m] = true;
                var meta = { percent: m, scroll_target: elSelector() };
                if (ctx.article_id) meta.article_id = ctx.article_id;
                if (ctx.cms_uri) meta.cms_uri = ctx.cms_uri;
                XenicalTracker.track('read_progress', 'read.' + m, { section: 'content', label: '閱讀進度', metadata: meta });
            }
        }
    }

    function elSelector() {
        if (document.querySelector('[data-track-scroll-target]')) return '[data-track-scroll-target]';
        if (document.querySelector('.news-content')) return '.news-content';
        return '.page-body';
    }

    document.addEventListener('DOMContentLoaded', function () {
        var el = targetEl();
        if (!el) return;
        var meta = {};
        if (ctx.article_id) meta.article_id = ctx.article_id;
        if (ctx.cms_uri) meta.cms_uri = ctx.cms_uri;
        XenicalTracker.track('content_enter', 'content.enter', { section: 'content', label: '進入正文', metadata: meta });
        window.addEventListener('scroll', onReadScroll, { passive: true });
        el.addEventListener('scroll', onReadScroll, { passive: true });
        onReadScroll();
    });

    window.addEventListener('pagehide', function () {
        var max = XenicalTracker.getMaxReadProgress();
        if (max < 10) return;
        var meta = { max_read_progress: max };
        if (ctx.article_id) meta.article_id = ctx.article_id;
        if (ctx.cms_uri) meta.cms_uri = ctx.cms_uri;
        XenicalTracker.track('content_abandon', 'content.abandon', { section: 'content', label: '內容未完成閱讀', metadata: meta });
    });
})(window, document);
