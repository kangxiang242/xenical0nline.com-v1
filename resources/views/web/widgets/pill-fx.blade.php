@php
    $pillHomeHero = !empty($pillHomeHero);
@endphp
<div id="pill3d-root" class="pill-fx-root pill-fx-root--deferred @if($pillHomeHero) pill-fx-root--home-hero @endif" @if($pillHomeHero) data-pill-home-hero="1" @endif>
    <canvas id="pill3d-canvas" class="capsule-fx-canvas" role="presentation" aria-hidden="true"></canvas>
    <a
        id="pill3d-link"
        class="pill-link"
        href="{{ url('product') }}"
        aria-label="前往產品訂購頁"
    ></a>
</div>

@push('pill-fx')
{{-- three r182：官方 ESM 會再請求 three.core；此處改為 esbuild 打成的單檔 ESM，僅需部署 three-0.182.0.bundle.esm.min.js --}}
{{-- 頁面 load 後再以動態 import 載入，避免與首屏搶頻寬/主線程；就緒後自螢幕下方滑入 pill --}}
<script type="module">
  var THREE_MODULE_URL = "{{ asset('static/js/vendor/three-0.182.0.bundle.esm.min.js') }}";

  var cfg = {
    topColor: '#66c5ff',
    bottomColor: '#7acdff',
  };

  function revealPillFx(root) {
    if (!root) return;
    root.classList.remove("pill-fx-root--deferred");
  }

  /**
   * 與 `resources/js/pages/home-gsap.js` 內 `HOME_BANNER_HERO_SCROLL`（預設 start top top、end bottom top）語意對齊：
   * 非頁面頂端載入時不要首屏大藥丸，改與內頁相同的小藥丸自下方滑入。
   * 可調：`SCROLL_TOP_GAP`、`BANNER_PROGRESS_GAP`（與 GSAP end 無法共用字串，此處用幾何近似）。
   */
  function homePillShouldRevealAsCornerOnly(root) {
    if (root.getAttribute("data-pill-home-hero") !== "1") {
      return false;
    }
    var scrollTop = window.scrollY || document.documentElement.scrollTop || 0;
    var SCROLL_TOP_GAP = 40;
    if (scrollTop > SCROLL_TOP_GAP) {
      return true;
    }
    var ban = document.querySelector(".index-banner");
    if (!ban) {
      return false;
    }
    var br = ban.getBoundingClientRect();
    var h = br.height;
    if (h < 1) {
      return false;
    }
    if (br.bottom <= 0) {
      return true;
    }
    if (br.top < 0) {
      var prog = (-br.top) / h;
      var BANNER_PROGRESS_GAP = 0.06;
      if (prog >= BANNER_PROGRESS_GAP) {
        return true;
      }
    }
    return false;
  }

  function applyHomeCornerOnlyReveal(root) {
    root.classList.remove("pill-fx-root--home-hero");
    root.setAttribute("data-pill-corner-only", "1");
  }

  function revealPillAndDispatchHome(root, cornerOnly) {
    revealPillFx(root);
    if (cornerOnly) {
      window.dispatchEvent(new CustomEvent("pill-fx-ready", { bubbles: true, detail: { root: root, cornerOnly: true } }));
    } else {
      dispatchPillFxReadyIfHome(root);
    }
  }

  /** 首頁大藥丸：等 CSS 由下往上進場後再通知 GSAP，避免 clearProps 打斷 transition */
  function dispatchPillFxReadyIfHome(root) {
    if (root.getAttribute("data-pill-home-hero") !== "1") {
      return;
    }
    var enterMs = 720;
    window.setTimeout(function() {
      window.dispatchEvent(new CustomEvent("pill-fx-ready", { bubbles: true, detail: { root: root } }));
    }, enterMs);
  }

  function schedulePill3dBoot() {
    function run() {
      import(THREE_MODULE_URL)
        .then(function(THREE) {
          bootCapsule(THREE);
        })
        .catch(function(err) {
          console.warn("Capsule 3D: Three.js 載入失敗", err);
        });
    }

    if (typeof window.requestIdleCallback === "function") {
      window.requestIdleCallback(run, { timeout: 4000 });
    } else {
      window.setTimeout(run, 0);
    }
  }

  window.addEventListener("load", schedulePill3dBoot);

  function bootCapsule(THREE) {
    var root = document.getElementById("pill3d-root");
    var canvas = document.getElementById("pill3d-canvas");
    var link = document.getElementById("pill3d-link");
    if (!root || !canvas || !link) {
      return;
    }

    document.body.appendChild(root);

    function createCapsuleGeometry(radius, halfBody, profileSegments, radialSegments) {
      var points = [];
      var i;
      for (i = 0; i <= profileSegments; i++) {
        var vTop = i / profileSegments;
        var thetaTop = vTop * Math.PI * 0.5;
        points.push(new THREE.Vector2(Math.sin(thetaTop) * radius, halfBody + Math.cos(thetaTop) * radius));
      }
      points.push(new THREE.Vector2(radius, -halfBody));
      for (i = profileSegments; i >= 0; i--) {
        var vBottom = i / profileSegments;
        var thetaBottom = vBottom * Math.PI * 0.5;
        points.push(new THREE.Vector2(Math.sin(thetaBottom) * radius, -halfBody - Math.cos(thetaBottom) * radius));
      }
      return new THREE.LatheGeometry(points, radialSegments);
    }

    var renderer = new THREE.WebGLRenderer({
      canvas: canvas,
      alpha: true,
      antialias: true,
    });

    if (!renderer.getContext()) {
      canvas.style.display = "none";
      if (!link.querySelector(".pill-fallback-dot")) {
        var fallbackWebgl = document.createElement("span");
        fallbackWebgl.className = "pill-fallback-dot";
        fallbackWebgl.setAttribute("aria-hidden", "true");
        link.appendChild(fallbackWebgl);
      }
      console.warn("WebGL unavailable for capsule.");
      var cornerOnlyFail = homePillShouldRevealAsCornerOnly(root);
      if (cornerOnlyFail) {
        applyHomeCornerOnlyReveal(root);
      }
      revealPillAndDispatchHome(root, cornerOnlyFail);
      return;
    }

    var maxDpr = 1;
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.28;

    var scene = new THREE.Scene();
    scene.add(new THREE.AmbientLight(0xffffff, 0.82));
    var keyLight = new THREE.DirectionalLight(0xffffff, 1.05);
    keyLight.position.set(4, 6, 8);
    scene.add(keyLight);
    var fillLight = new THREE.DirectionalLight(0xd0dcff, 0.52);
    fillLight.position.set(-5, 1, -4);
    scene.add(fillLight);

    var camera = new THREE.PerspectiveCamera(30, 1, 1, 500);
    camera.position.set(0, 0, 2.6);
    camera.lookAt(0, 0, 0);

    var capsuleGeo = createCapsuleGeometry(0.35, 0.62, 16, 44);
    var capsuleMat = new THREE.MeshStandardMaterial({
      color: new THREE.Color(cfg.topColor),
      roughness: 0.28,
      metalness: 0.18,
      side: THREE.DoubleSide,
      fog: false,
    });

    capsuleMat.onBeforeCompile = function(shader) {
      shader.uniforms.u_colorB = { value: new THREE.Color(cfg.bottomColor) };
      shader.vertexShader = "varying float vLocalY;\n" + shader.vertexShader.replace(
        "#include <begin_vertex>",
        "#include <begin_vertex>\nvLocalY = position.y;"
      );
      shader.fragmentShader = "uniform vec3 u_colorB;\nvarying float vLocalY;\n" + shader.fragmentShader.replace(
        "#include <color_fragment>",
        "#include <color_fragment>\nfloat splitMask = smoothstep(-0.04, 0.04, vLocalY);\ndiffuseColor.rgb = mix(u_colorB, diffuseColor.rgb, splitMask);"
      );
    };

    var capsule = new THREE.Mesh(capsuleGeo, capsuleMat);
    capsule.frustumCulled = false;
    scene.add(capsule);

    var capsuleBaseScale = 0.7;
    var hoverScale = 1;
    var hoverTarget = 0.7;
    var lastTime = performance.now();

    function resize() {
      var rect = root.getBoundingClientRect();
      var w = Math.max(1, rect.width);
      var h = Math.max(1, rect.height);
      renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, maxDpr));
      renderer.setSize(w, h, false);
      camera.aspect = w / h;
      camera.clearViewOffset();
      camera.updateProjectionMatrix();
    }

    function animate(now) {
      var dt = Math.min((now - lastTime) * 0.001, 0.1);
      lastTime = now;
      var t = now * 0.001;

      hoverScale += (hoverTarget - hoverScale) * Math.min(dt * 8, 1);

      capsule.rotation.set(
        0.42 + Math.sin(t * 0.42) * 0.05,
        t * 0.55,
        0.5 + t * 0.45
      );
      var scale = capsuleBaseScale * (1 + (hoverScale - 1) * 0.95);
      capsule.scale.setScalar(scale);

      renderer.render(scene, camera);
      requestAnimationFrame(animate);
    }

    link.addEventListener("mouseenter", function() {
      hoverTarget = 1;
    });
    link.addEventListener("mouseleave", function() {
      hoverTarget = 0.7;
    });

    window.addEventListener("resize", resize);
    root.__pillThreeResize = resize;
    resize();
    renderer.render(scene, camera);
    var cornerOnlyBoot = homePillShouldRevealAsCornerOnly(root);
    if (cornerOnlyBoot) {
      applyHomeCornerOnlyReveal(root);
    }
    revealPillAndDispatchHome(root, cornerOnlyBoot);
    requestAnimationFrame(animate);
  }
</script>
@endpush
