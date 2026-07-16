/**
 * PC 端（與版面 desktop 斷點一致：min-width 1024px）在頁面 load 後由 header 非同步載入本腳本，
 * 並以 requestIdleCallback 將 logo <img> 換成同一路徑 SVG 內聯，以保留 .logocenter / .logoouter 的 CSS 互動。
 */
(function () {
  var PC_MIN_WIDTH = 1024;

  function isDesktopViewport() {
    return window.matchMedia('(min-width: ' + PC_MIN_WIDTH + 'px)').matches;
  }

  function replaceLogoImgWithInlineSvg() {
    if (!isDesktopViewport()) {
      return;
    }

    var link = document.querySelector('a.logo');
    if (!link) {
      return;
    }

    var img = link.querySelector('img.logo-img');
    if (!img) {
      return;
    }

    var src = img.currentSrc || img.src;
    if (!src) {
      return;
    }

    fetch(src, { credentials: 'same-origin' })
      .then(function (res) {
        if (!res.ok) {
          throw new Error('fetch failed');
        }
        return res.text();
      })
      .then(function (markup) {
        var doc = new DOMParser().parseFromString(markup, 'image/svg+xml');
        var parseErr = doc.querySelector('parsererror');
        if (parseErr) {
          throw new Error('svg parse error');
        }
        var svg = doc.querySelector('svg');
        if (!svg) {
          return;
        }

        svg.setAttribute('role', 'img');
        var alt = img.getAttribute('alt');
        if (alt) {
          var ns = 'http://www.w3.org/2000/svg';
          var title = doc.createElementNS(ns, 'title');
          title.textContent = alt;
          svg.insertBefore(title, svg.firstChild);
        } else {
          svg.setAttribute('aria-hidden', 'true');
        }

        img.replaceWith(svg);
      })
      .catch(function () {
        /* 保留 <img> */
      });
  }

  function scheduleReplace() {
    if (typeof requestIdleCallback === 'function') {
      requestIdleCallback(replaceLogoImgWithInlineSvg, { timeout: 3000 });
    } else {
      setTimeout(replaceLogoImgWithInlineSvg, 0);
    }
  }

  scheduleReplace();
})();
