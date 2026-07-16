(function () {
    'use strict';
    if (!window.XenicalTracker) return;
    var T = XenicalTracker;
    var page = window.__TRACKING_PAGE__ || {};
    var sent = {};

    T.track('content_enter', 'content.enter', {
        section: 'content',
        metadata: filterMeta()
    });

    function filterMeta() {
        var m = {};
        if (page.article_id) m.article_id = page.article_id;
        if (page.cms_uri) m.cms_uri = page.cms_uri;
        return m;
    }

    var content = document.querySelector('.article-content, #articleContent, #spageContent, .news-main .article-content');
    if (content && window.IntersectionObserver) {
        var milestones = [25, 50, 75, 100];
        var io = new IntersectionObserver(function () {
            var rect = content.getBoundingClientRect();
            var ch = window.innerHeight;
            var visible = Math.max(0, Math.min(rect.bottom, ch) - Math.max(rect.top, 0));
            var total = rect.height || 1;
            var pct = Math.min(100, Math.floor((visible / total) * 100));
            var scrolled = Math.min(100, Math.floor(((ch - rect.top) / (rect.height + ch)) * 100));
            var progress = Math.max(pct, scrolled);
            if (progress > T.getReadProgress()) T.setReadProgress(progress);
            milestones.forEach(function (m) {
                if (progress >= m && !sent[m]) {
                    sent[m] = true;
                    T.track('read_progress', 'content.read.' + m, {
                        section: 'content.body',
                        metadata: { percent: m, scroll_target: 'article', article_id: page.article_id || '' }
                    });
                }
            });
        }, { threshold: [0, 0.25, 0.5, 0.75, 1] });
        io.observe(content);
        window.addEventListener('scroll', function () { io.takeRecords(); }, { passive: true });
    }

    document.querySelectorAll('.faq-question, .article-summary button').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var expanded = btn.getAttribute('aria-expanded') === 'true';
            T.track(expanded ? 'toc_collapse' : 'toc_expand', 'content.toc.toggle', {
                section: 'content.toc',
                metadata: { expanded: !expanded, heading_id: btn.id || '' }
            });
        });
    });

    window.addEventListener('pagehide', function () {
        if (!T.hasProductClicked()) {
            T.track('content_abandon', 'content.abandon', {
                section: 'content',
                metadata: { max_read_progress: T.getReadProgress(), article_id: page.article_id || '' }
            });
        }
    });
})();
