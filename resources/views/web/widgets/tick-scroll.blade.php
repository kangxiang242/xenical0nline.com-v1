{{-- 唯一入口：@include('web.widgets.tick-scroll')（見《滚动标签调用说明》） --}}
@php
    /** 產品頁由 Controller 傳入 $slides；其他頁由 LayoutComposer 的資料或此處補查（見《滚动标签调用说明》） */
    if (! isset($slides)) {
        $slides = isset($layout['slides']) ? $layout['slides'] : \App\Models\Slide::orderBy('id')->get();
    }
@endphp

@push('tick-scroll')
<script>
  (function () {
    "use strict";

    var DEFAULT_GAP = 20;
    var DURATION = 520;
    var INTERVAL = 1100;
    var EASING = "cubic-bezier(0.22, 1, 0.36, 1)";
    var MID_HAS_DONE = false;

    function toNumber(value, fallback) {
      var num = parseFloat(value);
      return isFinite(num) ? num : fallback;
    }

    function readGap(list) {
      var style = window.getComputedStyle(list);
      return toNumber(style.rowGap || style.gap || "", DEFAULT_GAP);
    }

    document.addEventListener("DOMContentLoaded", function () {
      if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

      var wraps = document.querySelectorAll(".tick-wrap");
      var instances = [];

      wraps.forEach(function (wrap) {
        var list = wrap.querySelector(".tick-scroll");
        if (!list) return;

        if (list.dataset.tickInit === "1") return;
        list.dataset.tickInit = "1";

        wrap.style.visibility = "hidden";

        var originals = Array.from(list.children);
        var n = originals.length;
        if (n < 3) {
          wrap.style.visibility = "";
          return;
        }

        var frag = document.createDocumentFragment();
        originals.forEach(function (li) { frag.appendChild(li.cloneNode(true)); });
        originals.forEach(function (li) { frag.appendChild(li.cloneNode(true)); });
        list.appendChild(frag);

        var items = list.children;
        var boxes = Array.from(items, function (li) {
          return li.querySelector(".righticon-box");
        });

        var step = 0;
        var itemHeight = 0;
        var currentIndex = 1; // 1..n+1，只滾動「中間那份」n..2n-1，頭尾兩份當緩衝
        var timer = null;
        var transitionFallbackTimer = null;
        var animating = false;
        var pausedByViewport = false;
        var observer = null;
        var destroyed = false;
        var prev = { top: -1, mid: -1, bot: -1 };

        function getBox(i) {
          return boxes[i] || null;
        }

        for (var i = 0; i < n; i++) {
          var box = getBox(i);
          if (box) box.classList.add("done");
        }

        var initialMid = getBox(n);
        if (initialMid) {
          initialMid.classList.add("mark");
          initialMid.classList.add("done");
        }

        // 不改 list 的 transform，用單條高度 + gap 算 step，避免 reflow/閃爍
        function measureStep() {
          if (items.length === 0) return 0;
          var h = items[0].getBoundingClientRect().height;
          if (!h) return 0;
          itemHeight = h;
          return h + readGap(list);
        }

        function syncLayout() {
          var measured = measureStep();
          if (!measured) return false;
          step = measured;
          wrap.style.height = (itemHeight + step * 2) + "px";
          return true;
        }

        // 只顯示中間那份：可視為 (n+currentIndex-2, n+currentIndex-1, n+currentIndex)，中項在 n+currentIndex-1
        function getTranslateY() {
          var midListIndex = n + currentIndex - 1;
          return step * (1 - midListIndex);
        }

        function applyTransform(animate) {
          list.style.transition = animate ? "transform " + DURATION + "ms " + EASING : "none";
          list.style.transform = "translate3d(0, " + getTranslateY() + "px, 0)";
        }

        function getVisibleIndexes() {
          var top = n + currentIndex - 2;
          var mid = n + currentIndex - 1;
          var bot = n + currentIndex;
          return { top: top, mid: mid, bot: bot };
        }

        function setBoxState(box, state) {
          if (!box) return;
          box.classList.remove("done", "mark");
          if (state === "done") box.classList.add("done");
          if (state === "mark") {
            box.classList.add("mark");
            if (MID_HAS_DONE) box.classList.add("done");
          }
        }

        function retriggerMark(box) {
          if (!box) return;
          box.classList.remove("mark");
          requestAnimationFrame(function () {
            requestAnimationFrame(function () { box.classList.add("mark"); });
          });
        }

        function setStatesOnce() {
          if (!step) return;
          var idx = getVisibleIndexes();
          [prev.top, prev.mid, prev.bot].forEach(function (i) {
            if (i >= n) setBoxState(getBox(i), "none");
          });
          if (idx.top >= n) setBoxState(getBox(idx.top), "done");
          var bMid = getBox(idx.mid);
          if (bMid) {
            setBoxState(bMid, "none");
            if (MID_HAS_DONE) bMid.classList.add("done");
            retriggerMark(bMid);
          }
          prev = idx;
        }

        function clearTickTimer() {
          if (!timer) return;
          clearTimeout(timer);
          timer = null;
        }

        function clearTransitionFallback() {
          if (!transitionFallbackTimer) return;
          clearTimeout(transitionFallbackTimer);
          transitionFallbackTimer = null;
        }

        function isPaused() {
          return document.hidden || pausedByViewport;
        }

        function scheduleTick() {
          clearTickTimer();
          if (isPaused()) return;
          timer = setTimeout(tick, INTERVAL);
        }

        function finishTick() {
          if (destroyed) return;
          clearTransitionFallback();
          if (currentIndex === n + 1) {
            currentIndex = 1;
            applyTransform(false);
          }
          setStatesOnce();
          animating = false;
          scheduleTick();
        }

        function onTransitionEnd(e) {
          if (e.propertyName !== "transform") return;
          list.removeEventListener("transitionend", onTransitionEnd);
          finishTick();
        }

        function tick() {
          if (destroyed || !step || animating || isPaused()) return;
          if (!document.body || !document.body.contains(list)) {
            destroy();
            return;
          }

          animating = true;
          currentIndex = currentIndex + 1;
          if (currentIndex > n + 1) currentIndex = n + 1;
          applyTransform(DURATION > 0);

          if (DURATION > 0) {
            list.addEventListener("transitionend", onTransitionEnd);
            transitionFallbackTimer = setTimeout(function () {
              list.removeEventListener("transitionend", onTransitionEnd);
              finishTick();
            }, DURATION + 120);
          } else {
            finishTick();
          }
        }

        function pause() {
          clearTickTimer();
        }

        function resume() {
          if (destroyed || animating || isPaused()) return;
          scheduleTick();
        }

        requestAnimationFrame(function () {
          if (!syncLayout()) {
            wrap.style.visibility = "";
            return;
          }
          currentIndex = 1;
          applyTransform(false);
          prev = { top: -1, mid: -1, bot: -1 };
          scheduleTick();
          wrap.style.visibility = "";
        });

        function handleResize() {
          if (destroyed || animating) return;
          if (!syncLayout()) return;
          currentIndex = Math.max(1, Math.min(currentIndex, n + 1));
          applyTransform(false);
          prev = { top: -1, mid: -1, bot: -1 };
        }

        if ("IntersectionObserver" in window) {
          observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
              if (entry.target !== wrap) return;
              pausedByViewport = !entry.isIntersecting;
              if (pausedByViewport) pause();
              else resume();
            });
          }, { threshold: 0.01 });
          observer.observe(wrap);
        }

        function destroy() {
          if (destroyed) return;
          destroyed = true;
          clearTickTimer();
          clearTransitionFallback();
          list.removeEventListener("transitionend", onTransitionEnd);
          if (observer) observer.disconnect();
        }

        instances.push({
          destroy: destroy,
          handleResize: handleResize,
          pause: pause,
          resume: resume
        });
      });

      if (instances.length === 0) return;

      var resizeTimer = null;
      function onResize() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
          instances.forEach(function (instance) {
            instance.handleResize();
          });
        }, 150);
      }

      function onVisibilityChange() {
        instances.forEach(function (instance) {
          if (document.hidden) instance.pause();
          else instance.resume();
        });
      }

      window.addEventListener("resize", onResize, { passive: true });
      document.addEventListener("visibilitychange", onVisibilityChange);

      window.addEventListener("pagehide", function () {
        clearTimeout(resizeTimer);
        window.removeEventListener("resize", onResize);
        document.removeEventListener("visibilitychange", onVisibilityChange);
        instances.forEach(function (instance) {
          instance.destroy();
        });
        instances = [];
      }, { once: true });
    });
  })();
</script>
@endpush

@if($slides->isNotEmpty())
<div class="tick-wrap">
    <ul class="tick-scroll">
        @foreach($slides as $slide)
            <li class="tick-item">
                <span class="righticon-box"><svg class="righticon" viewBox="0 0 1024 1024"><use href="#icon-righticon"></use></svg></span>
                <p class="tick-text"><strong class="big-text">{{ $slide->title }}</strong>：{{ $slide->desc }}</p>
            </li>
        @endforeach
    </ul>
</div>
@endif
