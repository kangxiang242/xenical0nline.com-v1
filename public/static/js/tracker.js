/**
 * Site behavior tracking (P0–P2). POST /observer/store
 */
(function (window, document) {
    'use strict';

    var cfg = window.__TRACKING_CONFIG__ || {};
    var pageCtx = window.__TRACKING_PAGE__ || {};
    var SCROLL_MILESTONES = [25, 50, 75, 100];
    var SECTION_THRESHOLD = 0.35;
    var SESSION_TIMEOUT_MS = 30 * 60 * 1000;
    var METADATA_ALLOWED = {
        field: 1, action: 1, product_id: 1, href: 1, element: 1, error_code: 1,
        depth_percent: 1, milestone: 1, scroll_target: 1, duration_seconds: 1, duration_sec: 1,
        max_scroll_percent: 1, exit_type: 1, next_uri: 1, checkout_outcome: 1,
        last_field: 1, fields_touched: 1, submit_clicked: 1, order_no: 1, amount: 1,
        product_name: 1, price: 1, bmi: 1, recommend_product_id: 1, redirect: 1,
        changed: 1, section_label: 1, title: 1, visibility_ratio_peak: 1,
        fcp_ms: 1, lcp_ms: 1, inp_ms: 1, ttfb_ms: 1, lcp_tag: 1,
        engagement_type: 1, duration_before_click_sec: 1, max_scroll_before_click_percent: 1,
        blocks_seen: 1, last_section_id: 1, checkout_duration_sec: 1, calc_type: 1,
        slide_index: 1, percent: 1, max_read_progress: 1, expanded: 1, faq_id: 1,
        status: 1, step: 1, has_value: 1, filled: 1, article_id: 1, cms_uri: 1,
        page_index: 1, session_path: 1, landing_page: 1, heading_id: 1
    };

    var state = {
        sessionId: null,
        visitorId: null,
        pageViewId: null,
        pageEnteredAt: 0,
        maxScrollPercent: 0,
        scrollMilestonesSent: {},
        scrollTarget: null,
        pageExitSent: false,
        checkout: null,
        vitals: null,
        blocksSeen: [],
        lastSectionId: '',
        sectionTimers: {},
        journey: null,
        productClicked: false,
        readProgress: 0
    };

    function isBot() {
        return /bot|crawler|spider|slurp|googlebot|bingpreview|lighthouse/i.test(navigator.userAgent || '');
    }
    function isEnabled() {
        return !isBot() && cfg.enabled !== false;
    }
    function uuid() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
            var r = Math.random() * 16 | 0;
            return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
        });
    }
    function getPageType() {
        return pageCtx.page_type || 'unknown';
    }
    function getVisitorId() {
        if (state.visitorId) return state.visitorId;
        try {
            state.visitorId = localStorage.getItem('xo_vid') || uuid();
            localStorage.setItem('xo_vid', state.visitorId);
            document.cookie = '_xo_vid=' + encodeURIComponent(state.visitorId) + ';path=/;max-age=31536000;SameSite=Lax';
        } catch (e) {
            state.visitorId = uuid();
        }
        return state.visitorId;
    }
    function touchSession() {
        var now = Date.now();
        var last = parseInt(sessionStorage.getItem('xo_sid_ts') || '0', 10);
        try {
            if (!state.sessionId || !last || now - last > SESSION_TIMEOUT_MS) {
                state.sessionId = uuid();
                sessionStorage.setItem('xo_sid', state.sessionId);
                state.journey = { landing: location.pathname, path: [], pageIndex: 0 };
            } else {
                state.sessionId = sessionStorage.getItem('xo_sid') || uuid();
                try {
                    state.journey = JSON.parse(sessionStorage.getItem('xo_journey') || 'null') || { landing: location.pathname, path: [], pageIndex: 0 };
                } catch (e2) {
                    state.journey = { landing: location.pathname, path: [], pageIndex: 0 };
                }
            }
            state.journey.pageIndex = (state.journey.pageIndex || 0) + 1;
            state.journey.path = state.journey.path || [];
            state.journey.path.push(location.pathname);
            if (state.journey.path.length > 20) state.journey.path.shift();
            sessionStorage.setItem('xo_journey', JSON.stringify(state.journey));
            sessionStorage.setItem('xo_sid_ts', String(now));
        } catch (e) {
            state.sessionId = state.sessionId || uuid();
            state.journey = state.journey || { landing: location.pathname, path: [location.pathname], pageIndex: 1 };
        }
        return state.sessionId;
    }
    function getSessionId() {
        if (!state.sessionId) touchSession();
        return state.sessionId;
    }
    function getDevice() {
        var h = location.hostname;
        if (cfg.mobileHost && h === cfg.mobileHost) return 'mobile';
        if (cfg.webHost && h === cfg.webHost) return 'web';
        return /^m\./i.test(h) ? 'mobile' : 'web';
    }
    function getCookie(n) {
        var m = document.cookie.match(new RegExp('(?:^|; )' + n + '=([^;]*)'));
        return m ? decodeURIComponent(m[1]) : '';
    }
    function captureUtm() {
        var q = new URLSearchParams(location.search);
        var utm = {};
        ['utm_source', 'utm_medium', 'utm_campaign'].forEach(function (k) {
            var v = q.get(k);
            if (v) utm[k.replace('utm_', '')] = v;
        });
        if (Object.keys(utm).length) {
            document.cookie = '_xo_utm=' + encodeURIComponent(JSON.stringify(utm)) + ';path=/;max-age=2592000;SameSite=Lax';
        }
    }
    function readUtm() {
        try { return JSON.parse(getCookie('_xo_utm') || '{}'); } catch (e) { return {}; }
    }
    function filterMetadata(o) {
        if (!o) return {};
        var out = {};
        for (var k in o) {
            if (o.hasOwnProperty(k) && METADATA_ALLOWED[k]) out[k] = o[k];
        }
        return out;
    }
    function getProductId() {
        if (pageCtx.goods_id) return String(pageCtx.goods_id);
        var m = location.pathname.match(/\/checkout\/(\d+)/);
        if (m) return m[1];
        var i = document.querySelector('#order-form input[name="goods_id"]');
        return i ? i.value : '';
    }
    function journeyMeta() {
        var j = state.journey || {};
        return filterMetadata({
            landing_page: j.landing || '',
            page_index: j.pageIndex || 0,
            session_path: JSON.stringify(j.path || [])
        });
    }
    function buildPayload(t, name, opt) {
        opt = opt || {};
        var utm = readUtm();
        var meta = filterMetadata(opt.metadata || {});
        var jm = journeyMeta();
        for (var jk in jm) if (jm.hasOwnProperty(jk)) meta[jk] = jm[jk];
        var label = opt.label || opt.explain || '';
        if (!label && (t === 'page_view' || t === 'page_exit')) {
            label = document.title || t;
        }
        return {
            event_type: t,
            event_name: name || '',
            event: t === 'click' ? 'click' : (opt.event || t),
            explain: label,
            label: label,
            page: location.pathname,
            uri: location.pathname,
            section: opt.section || '',
            device: getDevice(),
            session_id: getSessionId(),
            visitor_id: getVisitorId(),
            page_view_id: state.pageViewId || '',
            page_type: getPageType(),
            referer: document.referrer || '',
            original_referer: getCookie('_xo_ref') || '',
            utm_source: utm.source || utm.utm_source || '',
            utm_medium: utm.medium || utm.utm_medium || '',
            utm_campaign: utm.campaign || utm.utm_campaign || '',
            metadata: JSON.stringify(meta)
        };
    }
    function send(p) {
        if (!isEnabled()) return;
        var url = cfg.endpoint || '/observer/store';
        if (navigator.sendBeacon) {
            try {
                var b = new URLSearchParams();
                for (var k in p) if (p[k] != null && p[k] !== '') b.append(k, p[k]);
                if (navigator.sendBeacon(url, b)) return;
            } catch (e) {}
        }
        if (window.jQuery) {
            jQuery.ajax({ type: 'POST', url: url, data: p, dataType: 'text', async: true });
            return;
        }
        var b2 = new URLSearchParams();
        for (var k2 in p) if (p[k2] != null && p[k2] !== '') b2.append(k2, p[k2]);
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: b2.toString(),
            keepalive: true,
            credentials: 'same-origin'
        }).catch(function () {});
    }
    function track(t, n, o) { send(buildPayload(t, n, o)); }

    function isCheckout() {
        return getPageType() === 'checkout' || /^\/checkout\/\d+/.test(location.pathname);
    }
    function initCheckout() {
        if (!isCheckout()) return;
        state.checkout = {
            enteredAt: Date.now(),
            fieldsTouched: {},
            lastField: null,
            submitClicked: false,
            validationFailed: false,
            productId: getProductId()
        };
        track('conversion', 'begin_checkout', { section: 'checkout', metadata: { product_id: state.checkout.productId } });
    }
    function touchField(f) {
        if (state.checkout && f) {
            state.checkout.fieldsTouched[f] = true;
            state.checkout.lastField = f;
        }
    }
    function formInteraction(field, action, extra) {
        if (!field || !action) return;
        touchField(field);
        var meta = { field: field, action: action, product_id: getProductId() };
        if (extra) {
            for (var k in extra) if (extra.hasOwnProperty(k)) meta[k] = extra[k];
        }
        track('form_interaction', 'checkout.field.' + String(field).replace(/_/g, '.'), { section: 'checkout.form', metadata: meta });
    }
    function fieldFrom(el) {
        return el.getAttribute('data-track-field') || el.name || el.id || '';
    }
    function initCheckoutForm() {
        var form = document.getElementById('order-form');
        if (!form) return;
        form.addEventListener('focusin', function (e) {
            var f = fieldFrom(e.target);
            if (f && e.target.type !== 'hidden') formInteraction(f, 'focus');
        }, true);
        form.addEventListener('change', function (e) {
            var f = fieldFrom(e.target);
            if (!f || e.target.type === 'hidden') return;
            var ex = { changed: 1 };
            if (f === 'order_type') ex.changed = e.target.value;
            if (f === 'city' || f === 'county' || f === 'street') {
                track('cascade_step', 'checkout.cascade.' + f, {
                    section: 'checkout.form',
                    metadata: { step: f, has_value: !!(e.target.value && e.target.value !== '0') ? 1 : 0 }
                });
            }
            formInteraction(f, 'change', ex);
        }, true);
        form.addEventListener('focusout', function (e) {
            var f = fieldFrom(e.target);
            if (!f || e.target.type === 'hidden') return;
            var meta = {};
            if (e.target.type === 'text' || e.target.type === 'tel' || e.target.type === 'email' || e.target.tagName === 'TEXTAREA') {
                meta.filled = (e.target.value || '').trim().length > 0 ? 1 : 0;
            }
            formInteraction(f, 'blur', meta);
        }, true);
        form.querySelectorAll('input[name="order_type"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                track('delivery_type_change', 'checkout.delivery_type', {
                    section: 'checkout.form',
                    metadata: { changed: radio.value, product_id: getProductId() }
                });
            });
        });
    }
    function markSubmit() {
        if (!state.checkout) return;
        state.checkout.submitClicked = true;
        track('click', 'checkout.submit.click', {
            section: 'checkout',
            label: '提交訂單',
            explain: '提交訂單按鈕',
            metadata: { product_id: state.checkout.productId }
        });
    }
    function markValidationFail(code) {
        if (!state.checkout) {
            state.checkout = { validationFailed: true, productId: getProductId(), lastField: null, fieldsTouched: {}, submitClicked: false };
        }
        state.checkout.validationFailed = true;
        track('validation_error', 'checkout.validation', {
            section: 'checkout',
            metadata: { error_code: code || 'validation_failed', product_id: getProductId(), last_field: state.checkout.lastField || '' }
        });
        track('conversion', 'submit_fail', {
            section: 'checkout',
            metadata: { error_code: code || 'validation_failed', product_id: getProductId(), last_field: state.checkout.lastField || '' }
        });
    }
    function checkoutExitMeta() {
        if (!state.checkout) return {};
        var touched = [];
        for (var k in state.checkout.fieldsTouched) {
            if (state.checkout.fieldsTouched.hasOwnProperty(k)) touched.push(k);
        }
        var o = 'abandoned';
        if (state.checkout.submitClicked && state.checkout.validationFailed) o = 'abandoned_validation';
        else if (state.checkout.submitClicked) o = 'submitted';
        var meta = {
            checkout_outcome: o,
            last_field: state.checkout.lastField || '',
            fields_touched: touched,
            submit_clicked: state.checkout.submitClicked ? 1 : 0,
            product_id: state.checkout.productId
        };
        if (state.checkout.enteredAt) {
            meta.checkout_duration_sec = Math.max(0, Math.round((Date.now() - state.checkout.enteredAt) / 1000));
        }
        return meta;
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
        for (var i = 0; i < SCROLL_MILESTONES.length; i++) {
            var m = SCROLL_MILESTONES[i];
            if (p >= m && !state.scrollMilestonesSent[m]) {
                state.scrollMilestonesSent[m] = true;
                track('scroll_depth', 'scroll.' + m, {
                    metadata: { depth_percent: m, milestone: m + '%', scroll_target: state.scrollTarget || 'document' }
                });
            }
        }
    }
    var scrollTick = false;
    function scrollThrottle() {
        if (scrollTick) return;
        scrollTick = true;
        requestAnimationFrame(function () { scrollTick = false; onScroll(); });
    }
    function engagementType(durationSec) {
        var sp = state.maxScrollPercent;
        var pt = getPageType();
        if (durationSec < 3 && sp < 10) return 'bounce';
        if (durationSec < 8 && sp < 15) return 'quick_navigate';
        if ((pt === 'news_detail' || pt === 'cms') && sp >= 90) return 'deep_read';
        if (sp >= 50 || durationSec >= 60) return 'read';
        if (sp >= 10) return 'skim';
        return 'bounce';
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
    }
    function sectionKey(el) {
        return el.getAttribute('data-track-section') || ('section-' + (el.id || ''));
    }
    function flushSectionDwell(el) {
        var key = sectionKey(el);
        var st = state.sectionTimers[key];
        if (!st || !st.visibleSince) return;
        var dur = Math.round((Date.now() - st.visibleSince) / 1000);
        if (dur > 0) {
            track('section_dwell', 'section.' + key + '.dwell', {
                section: key,
                metadata: {
                    section_label: st.label || key,
                    duration_sec: dur,
                    visibility_ratio_peak: Math.round((st.peakRatio || 0) * 100) / 100
                }
            });
        }
        st.visibleSince = null;
        st.peakRatio = 0;
    }
    function flushAllSectionDwells() {
        document.querySelectorAll('[data-track-section-view]').forEach(function (el) {
            flushSectionDwell(el);
        });
    }
    function pageExit(type, next) {
        if (state.pageExitSent) return;
        state.pageExitSent = true;
        flushAllSectionDwells();
        var durationSec = Math.max(0, Math.round((Date.now() - state.pageEnteredAt) / 1000));
        var meta = {
            duration_seconds: durationSec,
            max_scroll_percent: state.maxScrollPercent,
            exit_type: type || 'unknown',
            next_uri: next || '',
            engagement_type: engagementType(durationSec)
        };
        var vm = vitalsMeta();
        for (var vk in vm) if (vm.hasOwnProperty(vk)) meta[vk] = vm[vk];
        if (isCheckout()) {
            var co = checkoutExitMeta();
            for (var k in co) if (co.hasOwnProperty(k)) meta[k] = co[k];
        }
        track('page_exit', 'page.exit', { metadata: meta });
    }
    function pageView() {
        captureUtm();
        touchSession();
        state.pageViewId = uuid();
        state.pageEnteredAt = Date.now();
        state.maxScrollPercent = 0;
        state.scrollMilestonesSent = {};
        state.pageExitSent = false;
        state.blocksSeen = [];
        state.sectionTimers = {};
        track('page_view', 'page.view', { metadata: document.title ? { title: document.title } : {} });
        initCheckout();
    }
    function clickContextMeta() {
        return {
            duration_before_click_sec: Math.max(0, Math.round((Date.now() - state.pageEnteredAt) / 1000)),
            max_scroll_before_click_percent: state.maxScrollPercent,
            blocks_seen: state.blocksSeen.slice(),
            last_section_id: state.lastSectionId || ''
        };
    }
    function trackClick(el) {
        if (!el || el.closest('[data-track-ignore]')) return;
        var name = el.getAttribute('data-track-name') || '';
        var section = el.getAttribute('data-track-section') || '';
        var label = el.getAttribute('data-observer') || el.getAttribute('data-track-label') || (el.textContent || '').trim().slice(0, 80);
        if (!name && !label && !section) return;
        if (!name) name = section ? section + '.click' : 'click';
        var href = el.getAttribute('href') || '';
        var meta = clickContextMeta();
        meta.element = el.tagName.toLowerCase();
        if (href) meta.href = href;
        if (/checkout|product|buy|order|訂購|購買/i.test(label + href)) state.productClicked = true;
        track('click', name, { section: section, label: label, explain: label, metadata: meta });
    }
    function initClicks() {
        document.addEventListener('click', function (e) {
            var el = e.target.closest('[data-track-name], [data-observer], .checkout-btn, .form-btn, button[type="submit"]');
            if (!el) return;
            if (isCheckout() && (el.classList.contains('checkout-btn') || el.classList.contains('form-btn') || el.type === 'submit')) {
                markSubmit();
            }
            if (el.hasAttribute('data-track-name') || el.hasAttribute('data-observer')) {
                trackClick(el);
            }
            var href = el.getAttribute('href');
            if (href && href.indexOf('javascript:') !== 0 && href.indexOf('#') !== 0) {
                try {
                    var d = new URL(href, location.href);
                    if (d.origin === location.origin && d.pathname !== location.pathname) {
                        pageExit('navigate', d.pathname);
                    }
                } catch (err) {}
            }
        }, true);
    }
    function initSections() {
        var nodes = document.querySelectorAll('[data-track-section-view]');
        if (!nodes.length || !window.IntersectionObserver) return;
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                var el = en.target;
                var key = sectionKey(el);
                var ratio = en.intersectionRatio;
                var st = state.sectionTimers[key] || {
                    label: el.getAttribute('data-track-section-label') || key,
                    visibleSince: null,
                    peakRatio: 0
                };
                state.sectionTimers[key] = st;
                if (ratio >= SECTION_THRESHOLD) {
                    if (!el.getAttribute('data-track-section-sent')) {
                        el.setAttribute('data-track-section-sent', '1');
                        if (state.blocksSeen.indexOf(key) === -1) state.blocksSeen.push(key);
                        state.lastSectionId = key;
                        track('section_view', 'section.' + key, {
                            section: key,
                            metadata: { section_label: st.label, milestone: 'first_view' }
                        });
                    }
                    if (!st.visibleSince) st.visibleSince = Date.now();
                    if (ratio > st.peakRatio) st.peakRatio = ratio;
                } else if (st.visibleSince) {
                    flushSectionDwell(el);
                }
            });
        }, { threshold: [0, SECTION_THRESHOLD, 0.5, 0.75] });
        nodes.forEach(function (n) { io.observe(n); });
    }
    function loadPlugin(name) {
        var base = cfg.pluginBase || '/static/js/tracker-plugins/';
        if (base.charAt(base.length - 1) !== '/') base += '/';
        var ver = cfg.assetVersion ? '?ver=' + cfg.assetVersion : '';
        var s = document.createElement('script');
        s.src = base + name + '.js' + ver;
        s.defer = true;
        document.body.appendChild(s);
    }
    function initPagePlugins() {
        var pt = getPageType();
        var map = {
            checkout: 'checkout',
            home: 'home',
            product_detail: 'product',
            bmi: 'calc',
            bmr: 'calc',
            body_fat: 'calc',
            news_detail: 'reading',
            cms: 'reading'
        };
        if (map[pt]) loadPlugin(map[pt]);
    }
    function boot() {
        if (!isEnabled()) return;
        var scrollEl = document.querySelector('[data-track-scroll-target]');
        state.scrollTarget = (document.body && document.body.getAttribute('data-track-scroll')) ||
            (scrollEl ? '[data-track-scroll-target]' : null);
        pageView();
        window.addEventListener('scroll', scrollThrottle, { passive: true });
        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'hidden') {
                flushAllSectionDwells();
                pageExit('hidden');
            }
        });
        window.addEventListener('pagehide', function () { pageExit('pagehide'); });
        initWebVitals();
        initClicks();
        initCheckoutForm();
        initSections();
        initPagePlugins();
    }

    window.XenicalTracker = {
        track: track,
        pageView: pageView,
        pageExit: pageExit,
        click: trackClick,
        formInteraction: formInteraction,
        conversion: function (n, m) {
            track('conversion', n, { section: isCheckout() ? 'checkout' : '', metadata: m || {} });
        },
        markCheckoutSubmit: markSubmit,
        markCheckoutValidationFail: markValidationFail,
        markAreaLoad: function (step, status) {
            track('area_load', 'checkout.area.' + step, { section: 'checkout.form', metadata: { step: step, status: status } });
        },
        trackFormResult: function (formType, ok, code) {
            track(formType + '_submit', formType + '.submit', {
                section: formType,
                metadata: { status: ok ? 'success' : 'fail', error_code: code || '' }
            });
        },
        getSessionId: getSessionId,
        getVisitorId: getVisitorId,
        isEnabled: isEnabled,
        getState: function () { return state; },
        setReadProgress: function (p) { state.readProgress = p; },
        getReadProgress: function () { return state.readProgress || 0; },
        hasProductClicked: function () { return state.productClicked; }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})(window, document);
