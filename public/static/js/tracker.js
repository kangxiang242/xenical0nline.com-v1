/**
 * Xenical site tracking — P0–P2 core SDK.
 */
(function (window, document) {
    'use strict';
    var cfg = window.__TRACKING_CONFIG__ || {};
    var pageCtx = window.__TRACKING_PAGE__ || {};
    var SCROLL_MILESTONES = [25, 50, 75, 100];
    var SECTION_THRESHOLD = 0.35;
    var METADATA_ALLOWED = {
        field: 1, action: 1, product_id: 1, href: 1, element: 1, error_code: 1,
        depth_percent: 1, milestone: 1, scroll_target: 1, duration_seconds: 1, duration_sec: 1,
        max_scroll_percent: 1, exit_type: 1, next_uri: 1, checkout_outcome: 1,
        last_field: 1, fields_touched: 1, submit_clicked: 1, order_no: 1, amount: 1,
        product_name: 1, price: 1, bmi: 1, recommend_product_id: 1, redirect: 1,
        changed: 1, section_label: 1, title: 1, filled: 1, value: 1, status: 1, step: 1,
        visibility_ratio_peak: 1, engagement_type: 1, blocks_seen: 1, last_section_id: 1,
        duration_before_click_sec: 1, max_scroll_before_click_percent: 1, session_path: 1,
        page_view_id: 1, calc_type: 1, slide_index: 1, faq_id: 1, expanded: 1,
        percent: 1, max_read_progress: 1, article_id: 1, cms_uri: 1,
        fcp_ms: 1, lcp_ms: 1, inp_ms: 1, ttfb_ms: 1, lcp_tag: 1, checkout_duration_sec: 1
    };
    var state = {
        sessionId: null, visitorId: null, pageViewId: null, pageEnteredAt: 0,
        maxScrollPercent: 0, scrollMilestonesSent: {}, scrollTarget: null, pageExitSent: false,
        checkout: null, vitals: null, sectionsSeen: {}, lastSectionId: '', sectionDwell: {},
        maxReadProgress: 0, pluginsLoaded: {}
    };

    function isBot() { return /bot|crawler|spider|slurp|googlebot|bingpreview|lighthouse/i.test(navigator.userAgent || ''); }
    function isEnabled() { return !isBot() && cfg.enabled !== false; }
    function uuid() { return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) { var r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8); return v.toString(16); }); }

    function getVisitorId() {
        if (state.visitorId) return state.visitorId;
        try {
            state.visitorId = localStorage.getItem('xo_vid') || uuid();
            localStorage.setItem('xo_vid', state.visitorId);
        } catch (e) { state.visitorId = uuid(); }
        return state.visitorId;
    }

    function getSessionId() {
        if (state.sessionId) return state.sessionId;
        try { state.sessionId = sessionStorage.getItem('xo_sid') || uuid(); sessionStorage.setItem('xo_sid', state.sessionId); }
        catch (e) { state.sessionId = uuid(); }
        return state.sessionId;
    }

    function pushSessionPath() {
        var p = location.pathname;
        try {
            var raw = sessionStorage.getItem('xo_spath') || '[]';
            var arr = JSON.parse(raw);
            if (!arr.length || arr[arr.length - 1] !== p) arr.push(p);
            if (arr.length > 50) arr = arr.slice(-50);
            sessionStorage.setItem('xo_spath', JSON.stringify(arr));
            return arr;
        } catch (e) { return [p]; }
    }

    function getSessionPath() {
        try { return JSON.parse(sessionStorage.getItem('xo_spath') || '[]'); }
        catch (e) { return [location.pathname]; }
    }

    function getDevice() {
        var h = location.hostname;
        if (cfg.mobileHost && h === cfg.mobileHost) return 'mobile';
        if (cfg.webHost && h === cfg.webHost) return 'web';
        return /^m\./i.test(h) ? 'mobile' : 'web';
    }

    function getCookie(n) { var m = document.cookie.match(new RegExp('(?:^|; )' + n.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '=([^;]*)')); return m ? decodeURIComponent(m[1]) : ''; }
    function setCookie(n, v, days) {
        var d = new Date(); d.setTime(d.getTime() + (days || 7) * 86400000);
        document.cookie = n + '=' + encodeURIComponent(v) + ';expires=' + d.toUTCString() + ';path=/;SameSite=Lax';
    }

    function captureUtmFromUrl() {
        try {
            var q = new URLSearchParams(location.search);
            var s = q.get('utm_source'), m = q.get('utm_medium'), c = q.get('utm_campaign');
            if (!s && !m && !c) return;
            setCookie('_xo_utm', JSON.stringify({ utm_source: s || '', utm_medium: m || '', utm_campaign: c || '' }), 7);
        } catch (e) {}
    }

    function readUtm() { try { return JSON.parse(getCookie('_xo_utm') || '{}'); } catch (e) { return {}; } }
    function filterMetadata(o) { if (!o) return {}; var out = {}; for (var k in o) if (o.hasOwnProperty(k) && METADATA_ALLOWED[k]) out[k] = o[k]; return out; }
    function getProductId() {
        var m = location.pathname.match(/\/checkout\/(\d+)/);
        if (m) return m[1];
        if (pageCtx.goods_id) return String(pageCtx.goods_id);
        var i = document.querySelector('#order-form input[name="goods_id"]');
        return i ? i.value : '';
    }

    function defaultExplain(t, name) {
        if (t === 'page_view') return '頁面瀏覽';
        if (t === 'page_exit') return '離開頁面';
        if (t === 'scroll_depth') return '滾動深度';
        if (t === 'section_view') return '區塊曝光';
        if (t === 'section_dwell') return '區塊停留';
        return name || t || 'event';
    }

    function buildPayload(t, name, opt) {
        opt = opt || {};
        var utm = readUtm(), meta = filterMetadata(opt.metadata || {}), label = opt.label || opt.explain || defaultExplain(t, name);
        return {
            event_type: t, event_name: name || '', event: t === 'click' ? 'click' : (opt.event || t),
            explain: label, label: label,
            page: location.pathname, uri: location.pathname, section: opt.section || '',
            device: getDevice(), session_id: getSessionId(), visitor_id: getVisitorId(),
            page_view_id: state.pageViewId || '', page_type: pageCtx.page_type || '', version: pageCtx.version || '無',
            referer: document.referrer || '', original_referer: getCookie('_xo_ref') || '',
            utm_source: utm.utm_source || utm.source || '', utm_medium: utm.utm_medium || utm.medium || '',
            utm_campaign: utm.utm_campaign || utm.campaign || '',
            metadata: JSON.stringify(meta)
        };
    }

    function send(p) {
        if (!isEnabled()) return;
        if (cfg.debug) try { console.log('[XenicalTracker]', p.event_type, p.event_name, p); } catch (e) {}
        var url = cfg.endpoint || '/observer/store';
        if (navigator.sendBeacon) {
            try {
                var b = new URLSearchParams();
                for (var k in p) if (p[k] != null && p[k] !== '') b.append(k, p[k]);
                if (navigator.sendBeacon(url, b)) return;
            } catch (e) {}
        }
        if (window.jQuery) { jQuery.ajax({ type: 'POST', url: url, data: p, dataType: 'text', async: true }); return; }
        var b2 = new URLSearchParams();
        for (var k2 in p) if (p[k2] != null && p[k2] !== '') b2.append(k2, p[k2]);
        fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: b2.toString(), keepalive: true, credentials: 'same-origin' }).catch(function () {});
    }

    function track(t, n, o) { send(buildPayload(t, n, o)); }

    function isCheckout() { return (pageCtx.page_type === 'checkout') || /^\/checkout\/\d+/.test(location.pathname); }

    function engagementType(durationSec, scrollPct) {
        if (durationSec < 3 && scrollPct < 10) return 'bounce';
        if (durationSec < 8 && scrollPct < 50) return 'quick_navigate';
        if (scrollPct >= 90) return 'deep_read';
        if (scrollPct >= 50 || durationSec >= 60) return 'read';
        if (scrollPct >= 10) return 'skim';
        return 'quick_navigate';
    }

    function blocksSeenList() {
        var out = [];
        for (var k in state.sectionsSeen) if (state.sectionsSeen.hasOwnProperty(k)) out.push(k);
        return out;
    }

    function initCheckout() {
        if (!isCheckout()) return;
        state.checkout = { enteredAt: Date.now(), fieldsTouched: {}, lastField: null, submitClicked: false, validationFailed: false, productId: getProductId() };
        track('conversion', 'begin_checkout', { section: 'checkout', label: '開始結帳', metadata: { product_id: state.checkout.productId } });
    }

    function touchField(f) { if (state.checkout && f) { state.checkout.fieldsTouched[f] = true; state.checkout.lastField = f; } }

    function formInteraction(field, action, extra) {
        if (!field || !action) return;
        touchField(field);
        var meta = { field: field, action: action, product_id: getProductId() };
        if (extra) for (var k in extra) if (extra.hasOwnProperty(k)) meta[k] = extra[k];
        track('form_interaction', 'checkout.field.' + String(field).replace(/_/g, '.'), { section: 'checkout.form', metadata: meta });
    }

    function fieldFrom(el) { return el.getAttribute('data-track-field') || el.name || el.id || ''; }

    function initCheckoutForm() {
        var form = document.getElementById('order-form');
        if (!form) return;
        form.addEventListener('focusin', function (e) {
            var f = fieldFrom(e.target);
            if (f && e.target.type !== 'hidden') formInteraction(f, 'focus');
        }, true);
        form.addEventListener('click', function (e) {
            var t = e.target;
            if (t.name === 'order_type') { formInteraction('order_type', 'click', { value: t.value }); return; }
            if (t.name === 'store_id') { formInteraction('store', 'click'); return; }
            var f = fieldFrom(t);
            if (f && f !== 'order_type' && f !== 'store_id') formInteraction(f, 'click');
        }, true);
        form.addEventListener('change', function (e) {
            var f = fieldFrom(e.target);
            if (!f || e.target.type === 'hidden') return;
            var ex = { changed: 1 };
            if (f === 'goods_ids' && e.target.value) ex.product_id = e.target.value;
            if (f === 'order_type') ex.value = e.target.value;
            formInteraction(f, 'change', ex);
        }, true);
        form.addEventListener('focusout', function (e) {
            var f = fieldFrom(e.target);
            if (!f || e.target.type === 'hidden') return;
            var filled = !!(e.target.value && String(e.target.value).trim());
            formInteraction(f, 'blur', { filled: filled });
        }, true);
    }

    function markSubmit() {
        if (!state.checkout) return;
        state.checkout.submitClicked = true;
        track('click', 'checkout.submit.click', { section: 'checkout', label: '提交訂單', explain: '提交訂單', metadata: { product_id: state.checkout.productId } });
    }

    function markValidationFail(code) {
        if (!state.checkout) return;
        state.checkout.validationFailed = true;
        track('conversion', 'submit_fail', { section: 'checkout', label: '提交失敗', metadata: { error_code: code || 'validation_failed', product_id: state.checkout.productId, last_field: state.checkout.lastField } });
    }

    function checkoutExitMeta() {
        if (!state.checkout) return {};
        var touched = [];
        for (var k in state.checkout.fieldsTouched) if (state.checkout.fieldsTouched.hasOwnProperty(k)) touched.push(k);
        var o = 'abandoned';
        if (state.checkout.submitClicked && state.checkout.validationFailed) o = 'abandoned_validation';
        else if (state.checkout.submitClicked) o = 'submitted';
        return {
            checkout_outcome: o, last_field: state.checkout.lastField || '', fields_touched: touched,
            submit_clicked: state.checkout.submitClicked ? 1 : 0, product_id: state.checkout.productId,
            checkout_duration_sec: Math.max(0, Math.round((Date.now() - state.checkout.enteredAt) / 1000))
        };
    }

    function scrollPct() {
        var st = window.pageYOffset || document.documentElement.scrollTop || 0;
        var ch = window.innerHeight || document.documentElement.clientHeight || 0;
        var sh = Math.max(document.documentElement.scrollHeight || 0, document.body ? document.body.scrollHeight : 0);
        if (state.scrollTarget) {
            var el = document.querySelector(state.scrollTarget);
            if (el) { st = el.scrollTop; ch = el.clientHeight; sh = el.scrollHeight; }
        }
        return sh <= ch ? 100 : Math.min(100, Math.max(0, Math.floor(((st + ch) / sh) * 100)));
    }

    function onScroll() {
        var p = scrollPct();
        if (p > state.maxScrollPercent) state.maxScrollPercent = p;
        if (p > state.maxReadProgress) state.maxReadProgress = p;
        for (var i = 0; i < SCROLL_MILESTONES.length; i++) {
            var m = SCROLL_MILESTONES[i];
            if (p >= m && !state.scrollMilestonesSent[m]) {
                state.scrollMilestonesSent[m] = true;
                track('scroll_depth', 'scroll.' + m, { label: '滾動' + m + '%', metadata: { depth_percent: m, milestone: m + '%', scroll_target: state.scrollTarget || 'document' } });
            }
        }
    }

    var scrollTick = false;
    function scrollThrottle() {
        if (scrollTick) return;
        scrollTick = true;
        requestAnimationFrame(function () { scrollTick = false; onScroll(); });
    }

    function flushSectionDwell(el, reason) {
        if (!el || !el._xoDwell) return;
        var d = el._xoDwell;
        if (!d.visibleSince) return;
        var sec = Math.max(0, Math.round((Date.now() - d.visibleSince) / 1000));
        if (sec < 1) { d.visibleSince = null; return; }
        var s = el.getAttribute('data-track-section') || '', lb = el.getAttribute('data-track-section-label') || s;
        track('section_dwell', 'section.dwell.' + s, {
            section: s, label: '區塊停留',
            metadata: { section_label: lb, duration_sec: sec, visibility_ratio_peak: d.peakRatio || SECTION_THRESHOLD }
        });
        d.visibleSince = null;
    }

    function flushAllSectionDwell() {
        document.querySelectorAll('[data-track-section-view]').forEach(function (el) { flushSectionDwell(el, 'pagehide'); });
    }

    function vitalsMeta() {
        var v = state.vitals || {}, out = {};
        if (v.fcp_ms != null) out.fcp_ms = v.fcp_ms;
        if (v.lcp_ms != null) out.lcp_ms = v.lcp_ms;
        if (v.inp_ms != null) out.inp_ms = v.inp_ms;
        if (v.ttfb_ms != null) out.ttfb_ms = v.ttfb_ms;
        if (v.lcp_tag) out.lcp_tag = v.lcp_tag;
        return out;
    }

    function initWebVitals() {
        state.vitals = { fcp_ms: null, lcp_ms: null, inp_ms: null, ttfb_ms: null, lcp_tag: '' };
        if (!window.performance) return;
        try {
            var nav = performance.getEntriesByType && performance.getEntriesByType('navigation')[0];
            if (nav && nav.responseStart) state.vitals.ttfb_ms = Math.round(nav.responseStart);
        } catch (e) {}
        if (!window.PerformanceObserver) return;
        try {
            new PerformanceObserver(function (list) {
                list.getEntries().forEach(function (entry) {
                    if (entry.name === 'first-contentful-paint') state.vitals.fcp_ms = Math.round(entry.startTime);
                });
            }).observe({ type: 'paint', buffered: true });
        } catch (e) {}
        try {
            new PerformanceObserver(function (list) {
                var entries = list.getEntries(), entry = entries[entries.length - 1];
                if (!entry) return;
                state.vitals.lcp_ms = Math.round(entry.renderTime || entry.loadTime || entry.startTime);
                state.vitals.lcp_tag = entry.element && entry.element.tagName ? entry.element.tagName.toLowerCase() : '';
            }).observe({ type: 'largest-contentful-paint', buffered: true });
        } catch (e) {}
        try {
            var poInp = new PerformanceObserver(function (list) {
                list.getEntries().forEach(function (entry) {
                    if (!entry.interactionId) return;
                    var d = Math.round(entry.duration || 0);
                    if (d > (state.vitals.inp_ms || 0)) state.vitals.inp_ms = d;
                });
            });
            try { poInp.observe({ type: 'event', buffered: true, durationThreshold: 0 }); }
            catch (e2) { poInp.observe({ type: 'event', buffered: true }); }
        } catch (e) {}
    }

    function pageExit(type, next) {
        if (state.pageExitSent) return;
        state.pageExitSent = true;
        flushAllSectionDwell();
        var dur = Math.max(0, Math.round((Date.now() - state.pageEnteredAt) / 1000));
        var meta = {
            duration_seconds: dur, max_scroll_percent: state.maxScrollPercent,
            exit_type: type || 'unknown', next_uri: next || '',
            engagement_type: engagementType(dur, state.maxScrollPercent),
            blocks_seen: blocksSeenList(), last_section_id: state.lastSectionId
        };
        var vm = vitalsMeta();
        for (var vk in vm) if (vm.hasOwnProperty(vk)) meta[vk] = vm[vk];
        if (isCheckout()) {
            var co = checkoutExitMeta();
            for (var k in co) if (co.hasOwnProperty(k)) meta[k] = co[k];
        }
        if (state.maxReadProgress > 0) meta.max_read_progress = state.maxReadProgress;
        track('page_exit', 'page.exit', { label: '離開頁面', metadata: meta });
    }

    function pageView() {
        state.pageViewId = uuid();
        state.pageEnteredAt = Date.now();
        state.maxScrollPercent = 0;
        state.maxReadProgress = 0;
        state.scrollMilestonesSent = {};
        state.pageExitSent = false;
        state.sectionsSeen = {};
        var spath = pushSessionPath();
        var meta = { page_view_id: state.pageViewId, session_path: spath };
        if (document.title) meta.title = document.title;
        if (pageCtx.goods_id) meta.product_id = pageCtx.goods_id;
        if (pageCtx.article_id) meta.article_id = pageCtx.article_id;
        if (pageCtx.cms_uri) meta.cms_uri = pageCtx.cms_uri;
        track('page_view', 'page.view', { label: '頁面瀏覽', metadata: meta });
        initCheckout();
    }

    function trackClick(el) {
        if (!el || el.closest('[data-track-ignore]')) return;
        var name = el.getAttribute('data-track-name') || '';
        var section = el.getAttribute('data-track-section') || '';
        var label = el.getAttribute('data-observer') || el.getAttribute('data-track-label') || (el.textContent || '').trim().slice(0, 80);
        if (!name && !label && !section) return;
        if (!name) name = section ? section + '.click' : 'click';
        var href = el.getAttribute('href') || '';
        var dur = Math.max(0, Math.round((Date.now() - state.pageEnteredAt) / 1000));
        var meta = {
            element: el.tagName.toLowerCase(),
            duration_before_click_sec: dur,
            max_scroll_before_click_percent: state.maxScrollPercent,
            blocks_seen: blocksSeenList(),
            last_section_id: state.lastSectionId
        };
        if (href) meta.href = href;
        track('click', name, { section: section, label: label, explain: label, metadata: meta });
    }

    function initClicks() {
        document.addEventListener('click', function (e) {
            var el = e.target.closest('[data-track-name], [data-observer], a[href], button, .submit-btn');
            if (!el) return;
            if (isCheckout() && (el.classList.contains('submit-btn') || el.type === 'submit' || el.closest('.top-fixed'))) markSubmit();
            if (el.hasAttribute('data-track-name') || el.hasAttribute('data-observer')) trackClick(el);
            var href = el.getAttribute('href');
            if (href && href.indexOf('javascript:') !== 0 && href.indexOf('#') !== 0) {
                try {
                    var d = new URL(href, location.href);
                    if (d.origin === location.origin && d.pathname !== location.pathname) pageExit('navigate', d.pathname);
                } catch (err) {}
            }
        }, true);
    }

    function initSections() {
        var nodes = document.querySelectorAll('[data-track-section-view]');
        if (!nodes.length || !window.IntersectionObserver) return;
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                var el = en.target, ratio = en.intersectionRatio || 0;
                if (!el._xoDwell) el._xoDwell = { visibleSince: null, peakRatio: 0 };
                var d = el._xoDwell;
                if (ratio > d.peakRatio) d.peakRatio = ratio;
                if (ratio >= SECTION_THRESHOLD) {
                    var s = el.getAttribute('data-track-section') || '';
                    if (s && !state.sectionsSeen[s]) state.sectionsSeen[s] = true;
                    if (s) state.lastSectionId = s;
                    if (!el.getAttribute('data-track-section-sent')) {
                        el.setAttribute('data-track-section-sent', '1');
                        var lb = el.getAttribute('data-track-section-label') || s;
                        track('section_view', 'section.' + s, { section: s, label: '區塊曝光', metadata: { section_label: lb } });
                    }
                    if (!d.visibleSince) d.visibleSince = Date.now();
                } else if (d.visibleSince) {
                    flushSectionDwell(el, 'intersect');
                }
            });
        }, { threshold: [0, 0.35, 0.5, 0.75, 1] });
        nodes.forEach(function (n) { io.observe(n); });
    }

    var PLUGIN_MAP = {
        checkout: 'checkout', bmi: 'calc', home: 'home', faq: 'home',
        product_detail: 'product', news_detail: 'reading', cms: 'reading'
    };

    function loadPlugin(name) {
        if (state.pluginsLoaded[name]) return;
        state.pluginsLoaded[name] = true;
        var ver = cfg.assetVersion || '';
        var base = (cfg.pluginBase || '/static/js/tracker-plugins/');
        if (base.slice(-1) !== '/') base += '/';
        var src = base + name + '.js' + (ver ? '?ver=' + ver : '');
        var s = document.createElement('script');
        s.src = src;
        s.defer = true;
        document.head.appendChild(s);
    }

    function loadPlugins() {
        var pt = pageCtx.page_type || '';
        if (PLUGIN_MAP[pt]) loadPlugin(PLUGIN_MAP[pt]);
    }

    function boot() {
        if (!isEnabled()) return;
        captureUtmFromUrl();
        getVisitorId();
        var scrollEl = document.querySelector('[data-track-scroll-target]');
        state.scrollTarget = (document.body && document.body.getAttribute('data-track-scroll')) || (scrollEl ? '[data-track-scroll-target]' : null);
        pageView();
        window.addEventListener('scroll', scrollThrottle, { passive: true });
        if (state.scrollTarget) {
            var stEl = document.querySelector(state.scrollTarget);
            if (stEl) stEl.addEventListener('scroll', scrollThrottle, { passive: true });
        }
        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'hidden') { flushAllSectionDwell(); pageExit('hidden'); }
        });
        window.addEventListener('pagehide', function () { flushAllSectionDwell(); pageExit('pagehide'); });
        initWebVitals();
        initClicks();
        initCheckoutForm();
        initSections();
        loadPlugins();
    }

    window.XenicalTracker = {
        track: track, pageView: pageView, pageExit: pageExit, click: trackClick,
        formInteraction: formInteraction,
        conversion: function (n, m, sec) { track('conversion', n, { section: sec || (isCheckout() ? 'checkout' : ''), label: n, metadata: m || {} }); },
        markCheckoutSubmit: markSubmit, markCheckoutValidationFail: markValidationFail,
        getSessionId: getSessionId, getVisitorId: getVisitorId, isEnabled: isEnabled,
        scrollPct: scrollPct, getPageContext: function () { return pageCtx; },
        setMaxReadProgress: function (p) { if (p > state.maxReadProgress) state.maxReadProgress = p; },
        getMaxReadProgress: function () { return state.maxReadProgress; },
        trackAreaLoad: function (step, status) {
            track('area_load', 'checkout.area.' + step, { section: 'checkout.form', metadata: { step: step, status: status || 'ok', product_id: getProductId() } });
        }
    };

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot);
    else boot();
})(window, document);
