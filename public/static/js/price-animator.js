/**
 * 价格动画模块 - 统一的价格计算和动画处理
 * 支持多种触发时机：scroll（滚动触发）、load（页面加载触发）、manual（手动触发）
 */
const PriceAnimator = {
    DEFAULT_PRICE_DURATION: 4000,
    PRICE_ANIM_START_DELAY_MS: 1500,
    LEADING_ZERO_HIDE_MS: 300,

    /**
     * 数字格式化 - 添加千分位逗号
     * @param {number} number - 要格式化的数字
     * @returns {string} 格式化后的字符串
     */
    number_format(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    },

    /**
     * 固定长度的纯数字串插入千分位逗号（与补位后的位数一致，用于滚轮布局）
     */
    formatDigitsWithCommas(digitStr) {
        const len = digitStr.length;
        if (len === 0) return '0';
        let firstGroupLen = len % 3;
        if (firstGroupLen === 0) firstGroupLen = 3;
        let out = digitStr.slice(0, firstGroupLen);
        let i = firstGroupLen;
        while (i < len) {
            out += ',' + digitStr.slice(i, i + 3);
            i += 3;
        }
        return out;
    },

    /**
     * 取得数字的小数位数（最多 4 位，避免浮点噪声）
     */
    getDecimalPlaces(num) {
        const s = String(num);
        if (s.indexOf('e-') > -1) {
            const parts = s.split('e-');
            const exp = parseInt(parts[1], 10);
            return Number.isFinite(exp) ? Math.min(4, exp) : 0;
        }
        const i = s.indexOf('.');
        if (i < 0) return 0;
        return Math.min(4, s.length - i - 1);
    },

    /**
     * 按小数位缩放成整数，供里程表逐步递减计算
     */
    toScaledInt(num, decimals) {
        const factor = Math.pow(10, decimals);
        return Math.round(num * factor);
    },

    /**
     * 固定长度数字串按小数位插入小数点与千分位
     */
    formatScaledDigitsWithCommas(digitStr, decimals) {
        if (decimals <= 0) return this.formatDigitsWithCommas(digitStr);
        const safe = digitStr.padStart(decimals + 1, '0');
        const splitAt = safe.length - decimals;
        const intPart = safe.slice(0, splitAt);
        const fracPart = safe.slice(splitAt);
        return `${this.formatDigitsWithCommas(intPart)}.${fracPart}`;
    },

    /**
     * 解析 CSS 时间变量
     * @param {string} varName - CSS 变量名
     * @param {HTMLElement} targetEl - 目标元素
     * @returns {number} 解析后的毫秒数
     */
    parseTime(val) {
        if (!val) return 0;
        val = String(val).trim();
        if (val.endsWith('ms')) return parseFloat(val);
        if (val.endsWith('s')) return parseFloat(val) * 1000;
        return parseFloat(val) || 0;
    },

    getCssTime(varName, targetEl) {
        return this.parseTime(
            getComputedStyle(targetEl || document.documentElement)
                .getPropertyValue(varName)
        );
    },

    /**
     * 从 .price-box 自身或祖先元素的 data-product-id 读取商品 id
     * @param {jQuery} $priceBox
     * @returns {number|null}
     */
    getProductIdFromPriceBox($priceBox) {
        const $host = $priceBox.closest('[data-product-id]');
        let raw =
            ($host.length ? $host.attr('data-product-id') : null) ||
            $priceBox.attr('data-product-id');
        if (raw === undefined || raw === null || String(raw).trim() === '') {
            return null;
        }
        const n = parseInt(String(raw).trim(), 10);
        return Number.isFinite(n) ? n : null;
    },

    /**
     * 从价格文本中提取数字（支持千分位与小数）
     */
    parsePriceText(text) {
        if (text == null) return NaN;
        const normalized = String(text).replace(/,/g, '').replace(/[^\d.]/g, '');
        if (!normalized) return NaN;
        return parseFloat(normalized);
    },

    /**
     * 当模板未提供 data-market-price / data-price 时，从新类名文案回填
     */
    ensurePriceData($priceBox) {
        const hasMarket = $priceBox.data('market-price') != null && $priceBox.data('market-price') !== '';
        const hasPrice = $priceBox.data('price') != null && $priceBox.data('price') !== '';
        if (hasMarket && hasPrice) return;

        const finalFromText = this.parsePriceText($priceBox.find('.price-number').first().text());
        const marketFromMk = this.parsePriceText($priceBox.find('.mk-price').first().text());
        const marketFromDiscount = this.parsePriceText(
            $priceBox.find('.discount').filter((_, el) => $(el).find('.descount-num').length === 0).first().text()
        );

        const finalPrice = Number.isFinite(finalFromText) ? finalFromText : NaN;
        const marketPrice = Number.isFinite(marketFromMk)
            ? marketFromMk
            : (Number.isFinite(marketFromDiscount) ? marketFromDiscount : finalPrice);

        if (Number.isFinite(finalPrice)) {
            $priceBox.data('price', finalPrice);
        }
        if (Number.isFinite(marketPrice)) {
            $priceBox.data('market-price', marketPrice);
        }
    },

    /**
     * 解析价格动画时长（毫秒）
     * 优先级：1) showPriceWithAnimation 传入的 duration 2) .price-box[data-price-duration] 数字（毫秒）
     * 3) JS 默认值 DEFAULT_PRICE_DURATION（不再依赖 CSS 变量）
     */
    resolvePriceAnimationDuration($priceBox, customDuration) {
        if (typeof customDuration === 'number' && customDuration > 0 && Number.isFinite(customDuration)) {
            return customDuration;
        }
        const raw = $priceBox.data('price-duration');
        if (raw != null && raw !== '') {
            const n = parseFloat(String(raw).trim(), 10);
            if (Number.isFinite(n) && n > 0) {
                return n;
            }
        }
        return this.DEFAULT_PRICE_DURATION;
    },

    /**
     * 条带起始偏移（格），避免顺滚到底时位移超出条带
     */
    REEL_STRIP_CYCLE_OFFSET: 3,
    MIN_STRIP_CYCLES: 2,
    MAX_STRIP_CYCLES: 40,

    /**
     * 每圈由上到下数字顺序（反向）：0,1,2,3,4,5,6,7,8,9（即 0123456789 循环）
     */
    STRIP_CYCLE_DIGITS: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],

    /**
     * 数字 d 在单圈条带中的格索引（0..9）
     */
    indexForStripDigit(d) {
        return d;
    },

    /**
     * 老虎机滚轮：每圈依 STRIP_CYCLE_DIGITS 重复，与 translateY(-k*1em) 对齐第 k 格
     */
    buildReelStripHTML(cycles) {
        const parts = [];
        const order = this.STRIP_CYCLE_DIGITS;
        for (let c = 0; c < cycles; c++) {
            for (let j = 0; j < order.length; j++) {
                parts.push(`<span class="price-reel__cell">${order[j]}</span>`);
            }
        }
        return parts.join('');
    },

    /**
     * 整数 n 在补位长度 len 下、由右往左第 placeFromRight 位（0=个位）的数字
     */
    digitAt(n, placeFromRight, len) {
        const s = String(Math.abs(Math.floor(n))).padStart(len, '0');
        const i = s.length - 1 - placeFromRight;
        return parseInt(s[i], 10);
    },

    /**
     * 从 market 递减到 final，每位滚轮在条带上需经过的格数（每次 n→n-1 若该位数字变化则累加一格距）。
     * 个位被每次减 1 驱动，高位仅在低位借位时变化，符合「秒针驱动分针」的联动。
     * totals[r]===0 表示该位在整段区间内数字从未变 → 不滚（如万位 15000→12000 始终为 1）；
     * 起讫相同但中间有变（如个位 0→…→0）则 totals[r]>0。
     */
    getStripOffsetsFromPriceRange(market, final, len) {
        const totals = new Array(len).fill(0);
        for (let n = market; n > final; n--) {
            for (let r = 0; r < len; r++) {
                const dBefore = this.digitAt(n, r, len);
                const dAfter = this.digitAt(n - 1, r, len);
                if (dBefore !== dAfter) {
                    const ib = this.indexForStripDigit(dBefore);
                    const ia = this.indexForStripDigit(dAfter);
                    // 条带反向后，递减时用相反方向计算格距，保持每次 n->n-1 仍推进 1 格
                    totals[r] += (ib - ia + 10) % 10;
                }
            }
        }
        return totals;
    },

    /**
     * 价格下降动画（老虎机滚轮式：每位同时开滚，个位先锁停→十位→百位…）
     * @param {jQuery} $priceBox - 价格容器元素（jQuery对象）
     * @param {number} customDuration - 自定义动画时长（毫秒）
     * @param {number} customDelay - 自定义延迟时间（毫秒）
     * @returns {Promise} 动画完成后的 Promise
     */
    animatePrice($priceBox, customDuration, customDelay) {
        return new Promise((resolve) => {
            // 防止重复动画
            if ($priceBox.data('animated')) {
                return resolve();
            }
            this.ensurePriceData($priceBox);

            const $priceNumber = $priceBox.find('.price-number');
            const marketPrice = parseFloat($priceBox.data('market-price'));
            const finalPrice = parseFloat($priceBox.data('price'));


            // 验证数据：须为有效正数且原价高于现价；先勿标记 animated，避免验证失败锁死
            if (
                !Number.isFinite(marketPrice) ||
                !Number.isFinite(finalPrice) ||
                marketPrice <= 0 ||
                finalPrice <= 0 ||
                marketPrice === finalPrice
            ) {
                return resolve();
            }

            $priceBox.data('animated', true);

            // 先显示原价，等待延迟后再滚轮到现价
            $priceNumber.text(this.number_format(marketPrice));

            // 获取动画参数
            const duration = this.resolvePriceAnimationDuration($priceBox, customDuration);
            const delay = typeof customDelay === 'number' && customDelay >= 0 ? customDelay : 0;


            const decimals = Math.max(
                this.getDecimalPlaces(marketPrice),
                this.getDecimalPlaces(finalPrice)
            );
            const marketRounded = this.toScaledInt(marketPrice, decimals);
            const finalRounded = this.toScaledInt(finalPrice, decimals);
            let marketDigits = String(marketRounded);
            let finalDigits = String(finalRounded);
            const len = Math.max(marketDigits.length, finalDigits.length);
            marketDigits = marketDigits.padStart(len, '0');
            finalDigits = finalDigits.padStart(len, '0');
            const rawFinalLen = Math.max(1, String(Math.abs(finalRounded)).length);
            const leadingPadCount = Math.max(0, len - rawFinalLen);

            const stripTotals = this.getStripOffsetsFromPriceRange(
                marketRounded,
                finalRounded,
                len
            );

            const columns = [];
            let maxStripIndex = 0;
            const maxOffsetInStrip =
                (this.MAX_STRIP_CYCLES - this.REEL_STRIP_CYCLE_OFFSET - 2) * 10;
            for (let i = 0; i < len; i++) {
                const startDigit = parseInt(marketDigits[i], 10);
                const endDigit = parseInt(finalDigits[i], 10);
                const placeFromRight = len - 1 - i;

                let totalOffset = stripTotals[placeFromRight];
                const isStatic = totalOffset === 0;

                const ie = this.indexForStripDigit(endDigit);
                const indexFinal = ie + 10 * this.REEL_STRIP_CYCLE_OFFSET;
                let indexMarket = indexFinal;

                if (!isStatic) {
                    // 性能保护：将位移压在可渲染条带范围内，避免生成超长 DOM
                    if (totalOffset > maxOffsetInStrip) {
                        totalOffset = maxOffsetInStrip + (totalOffset % 10);
                    }
                    indexMarket = indexFinal + totalOffset;
                    maxStripIndex = Math.max(maxStripIndex, indexMarket);
                }

                const lockEndMs = (duration * (placeFromRight + 1)) / len;

                columns.push({
                    startDigit,
                    endDigit,
                    isStatic,
                    totalOffset,
                    indexFinal,
                    indexMarket,
                    lockEndMs
                });
            }
            const stripCycles = Math.min(
                this.MAX_STRIP_CYCLES,
                Math.max(this.MIN_STRIP_CYCLES, Math.ceil((maxStripIndex + 2) / 10))
            );

            setTimeout(() => {
                const stripHtml = this.buildReelStripHTML(stripCycles);
                const templateStr = this.formatScaledDigitsWithCommas(finalDigits, decimals);
                $priceNumber.empty().addClass('price-number--slot');
                const leadingZeroEls = [];

                let digitIdx = 0;
                for (let c = 0; c < templateStr.length; c++) {
                    const ch = templateStr[c];
                    if (ch === ',') {
                        $priceNumber.append(
                            '<span class="price-reel-sep" aria-hidden="true">,</span>'
                        );
                    } else if (ch === '.') {
                        $priceNumber.append(
                            '<span class="price-reel-sep" aria-hidden="true">.</span>'
                        );
                    } else {
                        const col = columns[digitIdx];
                        if (col.isStatic) {
                            const $static = $(
                                `<span class="price-reel price-reel--static">${col.endDigit}</span>`
                            );
                            if (digitIdx < leadingPadCount) {
                                $static.addClass('price-reel--leading-zero');
                                leadingZeroEls.push($static);
                            }
                            $priceNumber.append($static);
                            col.$track = null;
                        } else {
                            const $track = $('<span class="price-reel__track"></span>').html(
                                stripHtml
                            );
                            const $reel = $('<span class="price-reel"></span>').append($track);
                            if (digitIdx < leadingPadCount) {
                                $reel.addClass('price-reel--leading-zero');
                                leadingZeroEls.push($reel);
                            }
                            $priceNumber.append($reel);
                            col.$track = $track;
                        }
                        digitIdx += 1;
                    }
                }

                let startTime = null;

                const animate = (currentTime) => {
                    if (startTime === null) startTime = currentTime;
                    const t = currentTime - startTime;

                    columns.forEach((col) => {
                        if (col.isStatic || !col.$track) return;
                        let p = 1;
                        const lockMs = col.lockEndMs;
                        if (lockMs > 0 && t < lockMs) {
                            const linear = t / lockMs;
                            p = 1 - Math.pow(1 - linear, 3);
                        }
                        // p:0→1 时 translateY 从 -indexMarket 增至 -indexFinal，条带向下移
                        const yEm = -col.indexMarket + col.totalOffset * p;
                        col.$track.css('transform', `translateY(${yEm}em)`);
                    });

                    if (t < duration) {
                        requestAnimationFrame(animate);
                    } else {
                        columns.forEach((col) => {
                            if (col.isStatic || !col.$track) return;
                            const yEnd = -col.indexFinal;
                            col.$track.css('transform', `translateY(${yEnd}em)`);
                        });
                        const finish = () => {
                            // 收尾仅替换数字文本；保留 slot 容器布局，避免切换 display/对齐引发跳动
                            $priceNumber.text(this.number_format(finalPrice));
                            resolve();
                        };
                        if (leadingZeroEls.length > 0) {
                            requestAnimationFrame(() => {
                                leadingZeroEls.forEach(($el) => {
                                    $el.addClass('price-reel--leading-zero-hide');
                                });
                                setTimeout(finish, this.LEADING_ZERO_HIDE_MS);
                            });
                        } else {
                            finish();
                        }
                    }
                };

                requestAnimationFrame(animate);
            }, delay);
        });
    },

    /**
     * 添加可见性类并触发动画
     * @param {jQuery} $priceBox - 价格容器
     * @param {jQuery} $discountBox - 折扣容器
     * @param {number} duration - 动画时长
     * @param {number} delay - 动画延迟
     */
    showPriceWithAnimation($priceBox, $discountBox, duration, delay) {
        if ($priceBox.data('animated')) {
            return;
        }
        $priceBox.addClass('price-show');

        const productId = this.getProductIdFromPriceBox($priceBox);

        // 商品 id 为 1：不跑滚轮动画，不加 price-animate，改用 price-origin（原价展示）
        if (productId === 1) {
            $priceBox.data('animated', true);
            requestAnimationFrame(() => {
                $priceBox.addClass('price-origin');
                this.ensurePriceData($priceBox);
                const finalPrice = parseFloat($priceBox.data('price'));
                const $priceNumber = $priceBox.find('.price-number');
                if ($priceNumber.length && Number.isFinite(finalPrice)) {
                    $priceNumber.text(this.number_format(Math.round(finalPrice)));
                    $priceNumber.removeClass('price-number--slot');
                }
                if ($discountBox.length) {
                    $discountBox.addClass('discount-show');
                }
            });
            return;
        }

        requestAnimationFrame(() => {
            // 统一触发类：由 .price-box.price-animate 承载所有视觉效果
            $priceBox.addClass('price-animate');
            const startDelay =
                typeof delay === 'number' && delay >= 0
                    ? delay
                    : this.PRICE_ANIM_START_DELAY_MS;
            this.animatePrice($priceBox, duration, startDelay).then(() => {
                if ($discountBox.length) {
                    $discountBox.addClass('discount-show');
                }
            });
        });
    },

    /**
     * 滚动触发模式（产品列表页）
     * 当卡片滚动到视口中时触发动画
     */
    initScrollTrigger() {
        const self = this;
        let ticking = false;

        const checkVisibility = () => {
            const scrollTop = $(window).scrollTop();
            const viewportHeight = $(window).height();

            $('.product-card').each(function() {
                const $productBox = $(this);
                const elementTop = $productBox.offset().top;
                const elementHeight = $productBox.outerHeight();
                const elementBottom = elementTop + elementHeight;
                const $priceBox = $productBox.find('.price-box');
                const $discountBox = $productBox.find('.discount');

                const isVisible = (
                    (elementTop >= scrollTop && elementTop < scrollTop + viewportHeight) ||
                    (elementBottom > scrollTop && elementBottom <= scrollTop + viewportHeight) ||
                    (elementTop < scrollTop && elementBottom > scrollTop + viewportHeight)
                );

                if (isVisible) {
                    if (!$productBox.hasClass('product-card-show')) {
                        $productBox.addClass('product-card-show');
                    }

                    if ($priceBox.length && !$priceBox.data('animated') && !$priceBox.hasClass('price-show')) {
                        self.showPriceWithAnimation($priceBox, $discountBox);
                    }
                }
            });

            ticking = false;
        };

        $(window).on('scroll.priceAnimator', () => {
            if (!ticking) {
                requestAnimationFrame(checkVisibility);
                ticking = true;
            }
        });

        $(document).ready(() => {
            const initCheck = () => {
                requestAnimationFrame(() => {
                    checkVisibility();
                });
            };

            initCheck();
            setTimeout(initCheck, 50);
            setTimeout(initCheck, 100);
            setTimeout(initCheck, 200);
            setTimeout(initCheck, 300);
            setTimeout(initCheck, 500);
        });

        $(window).on('load.priceAnimator', () => {
            setTimeout(() => {
                checkVisibility();
            }, 100);
        });
    },

    /**
     * 页面加载触发模式（产品详情页等）
     * 页面加载完成后立即触发动画
     */
    initLoadTrigger() {
        const self = this;

        $(document).ready(() => {
            // 处理 .product-box 内的价格
            $('.product-box').each(function() {
                const $box = $(this);
                const $priceBox = $box.find('.price-box');
                const $discountBox = $box.find('.discount-sub, .discount');

                $box.addClass('product-box-show');

                if ($priceBox.length) {
                    self.showPriceWithAnimation($priceBox, $discountBox);
                }
            });

            // 处理没有 .product-box 的价格
            $('.price-box').each(function() {
                const $priceBox = $(this);
                if ($priceBox.data('animated')) return;
                const $discountBox = $priceBox.siblings('.discount-sub, .discount').add(
                    $priceBox.find('.discount-sub, .discount')
                );

                self.showPriceWithAnimation($priceBox, $discountBox);
            });

        });
    },

    /**
     * 结账页触发模式
     * 包含安全扫描动画、卡片进入动画、价格动画的复杂流程
     */
    initCheckoutTrigger() {
        const self = this;
        const STORAGE_KEY = 'securityScanPlayed';
        const hasPlayed = sessionStorage.getItem(STORAGE_KEY);
        const pageEl = document.querySelector('.page-checkout') || document.documentElement;
        let overlayDuration = this.getCssTime('--overlay-duration', pageEl);
        // 确保 overlayDuration 是有效数值，最小 1500ms
        if (!overlayDuration || !Number.isFinite(overlayDuration) || overlayDuration < 1500) {
            overlayDuration = 2000;
        }
        const cardEnterDuration = 800; // 卡片进入动画固定 800ms

        const $overlay = $('.security-scan-overlay');
        const $body = $('body, html');
        const $pageContent = $('.checkout-btn, .secret, .card');
        const $stampText = $('.secret .stamp-text');

        // 工具函数：等待
        const wait = (ms) => new Promise(resolve => setTimeout(resolve, ms));

        // 显示主内容
        const showMainContent = () => {
            $pageContent.addClass('show');
            $body.css('overflow', '');
        };

        // 播放扫描动画
        const playOverlay = () => {
            return new Promise((resolve) => {
                // 强制显示 overlay
                $overlay.css({
                    'display': 'flex',
                    'opacity': 1,
                    'visibility': 'visible'
                });
                $body.css('overflow', 'hidden');

                setTimeout(() => {
                    $overlay.fadeOut(300, () => {
                        $overlay.remove();
                        showMainContent();
                        resolve();
                    });
                }, overlayDuration);
            });
        };

        // 主流程
        const runCheckoutSequence = async (isFirstVisit) => {

            // 第一阶段：扫描动画或直接显示
            if (isFirstVisit && $overlay.length) {
                await playOverlay();
            } else {
                $overlay.remove();
                showMainContent();
            }

            // 第二阶段：准备价格动画
            const $orderPrice = $('#order-price');
            const $priceBox = $('.price-box').first();
            const $discountBox = $('.discount-sub');
            // marketPrice 从 #goods-price 或 data-market-price 读取
            const marketPrice = parseFloat($('#goods-price').text().replace(/,/g, '')) 
                || parseFloat($priceBox.data('market-price')) 
                || 0;
            // finalPrice 从 data-price 属性读取（不是从 #order-price 文本）
            const finalPrice = parseFloat($priceBox.data('price')) || 0;


            // 先显示原价（动画开始时会从原价动画到现价）
            if (marketPrice > 0) {
                $orderPrice.text(self.number_format(marketPrice));
            }

            // 第三阶段：等待卡片进入动画（固定 800ms）
            await wait(cardEnterDuration);

            // 第四阶段：播放价格动画
            if ($priceBox.length && marketPrice > 0 && finalPrice > 0 && marketPrice !== finalPrice) {
                // 显示价格容器并播放价格下降动画
                self.showPriceWithAnimation($priceBox, $discountBox);
            } else {
            }

            // 第五阶段：启动印章动画
            $stampText.addClass('stamp-text-show');
        };

        // 判断是否首次访问
        if (!hasPlayed && $overlay.length) {
            sessionStorage.setItem(STORAGE_KEY, '1');
            runCheckoutSequence(true);
        } else {
            runCheckoutSequence(false);
        }
    },

    /**
     * 手动触发模式
     * 用于特殊场景，需要手动调用
     * @param {string|jQuery} selector - 价格容器选择器或jQuery对象
     * @param {object} options - 配置选项
     */
    triggerManually(selector, options = {}) {
        const $priceBox = typeof selector === 'string' ? $(selector) : selector;
        const $discountBox = options.discountBox || $priceBox.find('.discount-sub, .discount');

        this.showPriceWithAnimation(
            $priceBox,
            $discountBox,
            options.duration,
            options.delay
        );
    },

    /**
     * 初始化入口
     * 自动检测页面类型并应用相应的触发模式
     * @param {string} mode - 触发模式：'scroll'、'load'、'checkout'、'manual'
     */
    init(mode = 'auto') {
        // 自动检测模式
        if (mode === 'auto') {
            // 结账页
            if ($('.security-scan-overlay').length || $('.page-checkout').length) {
                mode = 'checkout';
            }
            // 产品列表页
            else if ($('.product-card').length && !$('.product-box').length) {
                mode = 'scroll';
            }
            // 产品详情页或其他页面
            else {
                mode = 'load';
            }
        }

        // 根据模式初始化
        switch (mode) {
            case 'scroll':
                this.initScrollTrigger();
                break;
            case 'load':
                this.initLoadTrigger();
                break;
            case 'checkout':
                this.initCheckoutTrigger();
                break;
            case 'manual':
                // 手动模式不自动初始化，需要手动调用 triggerManually
                break;
        }
    },

};

// 自动初始化（如果页面上有 price-box）
$(document).ready(() => {
    if ($('.price-box').length) {
        // 检查是否有手动触发标记
        if ($('[data-price-animate="manual"]').length) {
            PriceAnimator.init('manual');
        } else {
            PriceAnimator.init('auto');
        }
    }

    // 首頁「已有 X 人」數字由 Blade 靜態顯示；不在進場時做 0→N 滾輪（大數字會很怪）。
    // 僅在購買訊息 +1 時由 update-box / BuyerMessageManager.animateCounter 播放短動畫。

    // 购买消息弹窗：首頁／結帳若已載入 SimpleLinkedMessageManager（update-box），由其單獨輪詢，避免雙重請求與重複 +1
    // 結帳頁、訂單查詢結果頁不初始化（與 update-box.blade 排除路徑一致）
    var pathname = (window.location && window.location.pathname) ? window.location.pathname : '';
    var omitBuyerMessagePopup = /(^|\/)(checkout)(\/|$)/.test(pathname) || /(^|\/)check\/[^/]+\/?$/.test(pathname);
    if (!window.SimpleLinkedMessageManagerInitialized && !omitBuyerMessagePopup) {
        BuyerMessageManager.init();
    }
});

/* ======================= 购买消息弹窗模块 ======================= */
const BuyerMessageManager = {
    container: null,
    $message: null,
    timer: null,
    apiBaseUrl: '/api/buyermessage',

    init: function() {
        this.container = $('.update-box');
        if (this.container.length === 0) {
            this.container = $('<div class="update-box"></div>');
            $('body').append(this.container);
        }

        this.$message = this.container.find('.update-item').first();
        if (this.$message.length === 0) {
            this.$message = $('<div class="update-item"></div>');
            this.container.append(this.$message);
        }

        this.start();
    },

    getNextMessage: function(callback) {
        $.ajax({
            url: this.apiBaseUrl + '/nextMessage',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.code == 1 && response.data) {
                    callback(response.data);
                }
            },
            error: function() {
                callback({
                    shouldShow: false,
                    nextInterval: 5000,
                });
            }
        });
    },

    showMessage: function() {
        var self = this;

        if (this.timer) {
            clearTimeout(this.timer);
            this.timer = null;
        }

        this.getNextMessage(function(result) {
            if (!result.shouldShow) {
                if (result.nextInterval === 0) {
                    var now = new Date();
                    var currentHour = now.getHours();
                    var nextHour = (currentHour + 1) % 24;
                    var nextHourTime = new Date(now);
                    nextHourTime.setHours(nextHour, 0, 0, 0);
                    if (nextHour < currentHour) {
                        nextHourTime.setDate(nextHourTime.getDate() + 1);
                    }
                    var timeUntilNextHour = nextHourTime.getTime() - now.getTime();
                    self.timer = setTimeout(function() {
                        self.showMessage();
                    }, timeUntilNextHour);
                } else {
                    self.timer = setTimeout(function() {
                        self.showMessage();
                    }, result.nextInterval);
                }
                return;
            }

            var messageHtml = result.messageHtml;
            var boxBuyers = result.boxBuyers;

            if (boxBuyers) {
                // 更新所有 .box-buyer-count 元素
                for (var key in boxBuyers) {
                    var countText = '近24小時已有' + boxBuyers[key] + '人訂購';
                    $('.box-buyer-count[data-box-count="' + key + '"]').text(countText);
                }

                // 调用 ProductBuyerCounter 更新 #totalsale（使用后端传来的盒数）
                if (typeof ProductBuyerCounter !== 'undefined' && ProductBuyerCounter.updateFromBoxBuyers) {
                    ProductBuyerCounter.updateFromBoxBuyers(boxBuyers);
                }
            }

            // 调用API增加首页用户计数并动画更新
            self.incrementUserCountAndAnimate();

            self.$message.html(messageHtml + '<p>剛剛</p>');

            self.$message.removeClass('slide-in fade-out');
            self.$message.css({
                'transform': 'translateX(120%)',
                'opacity': '0'
            });

            requestAnimationFrame(function() {
                self.$message[0].offsetHeight;

                requestAnimationFrame(function() {
                    self.$message.css({
                        'transform': '',
                        'opacity': ''
                    });
                    self.$message.addClass('slide-in');

                    setTimeout(function() {
                        self.$message.removeClass('slide-in').addClass('fade-out');

                        setTimeout(function() {
                            self.timer = setTimeout(function() {
                                self.showMessage();
                            }, result.nextInterval);
                        }, 400);
                    }, 3000);
                });
            });
        });
    },

    start: function() {
        var self = this;

        if (this.timer) {
            clearTimeout(this.timer);
            this.timer = null;
        }

        this.timer = setTimeout(function() {
            self.showMessage();
        }, 3000);
    },

    /**
     * 增加首页用户计数并动画更新（本地+1）
     */
    incrementUserCountAndAnimate: function() {
        var $counter = $('.user-count-number');
        if ($counter.length === 0) {
            return;
        }

        var self = this;
        // 與 DOM data-count 一致（.attr 與 jQuery .data 快取不同步時以屬性為準）
        var rawCount = $counter.attr('data-count');
        var currentCount = parseInt(String(rawCount == null ? '' : rawCount).replace(/,/g, ''), 10) || 0;
        var newCount = currentCount + 1;

        // 本地+1并播放动画
        $counter.data('count', newCount);
        // 播放滚轮动画
        self.animateCounter($counter, currentCount, newCount);
    },

    /**
     * 数字滚动动画 - 老虎机滚轮式（仅动画变化位数，保持逗号，+1效果）
     */
    animateCounter: function($el, from, to) {
        var duration = 400; // 动画时长
        var prevCleanupTimer = $el.data('userCountCleanupTimer');
        if (prevCleanupTimer) {
            clearTimeout(prevCleanupTimer);
        }

        // 数字格式化（添加千分位）
        var formatNumber = function(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        };

        // 构建滚轮HTML（只滚动1圈）
        var buildReelStripHTML = function() {
            var html = '';
            for (var d = 0; d <= 9; d++) {
                html += '<span class="user-count-reel__cell">' + d + '</span>';
            }
            return html;
        };

        // 先锁定宽度，避免 empty() 时 inline 内容塌缩导致整段数字“跳一下”
        var prevMinWidth = $el[0] ? $el[0].style.minWidth : '';
        var lockedWidth = Math.ceil($el.outerWidth());
        if (lockedWidth > 0) {
            $el.css('min-width', lockedWidth + 'px');
        }

        // 清空并添加slot类
        $el.empty().addClass('user-count--slot');

        // 获取格式化后的字符串
        var formattedFrom = formatNumber(from);
        var formattedTo = formatNumber(to);

        // 逐字符处理，保持逗号
        var fromLen = formattedFrom.length;
        var toLen = formattedTo.length;
        var maxLen = Math.max(fromLen, toLen);
        var reelsData = [];
        var appendNodes = [];

        // 从左到右构建
        for (var pos = 0; pos < maxLen; pos++) {
            var fChar = pos < fromLen ? formattedFrom[pos] : '';
            var tChar = pos < toLen ? formattedTo[pos] : '';

            if (fChar === ',') {
                appendNodes.push(
                    $('<span class="user-count-reel user-count-reel--static user-count-reel--comma">' + tChar + '</span>')[0]
                );
            } else if (fChar === tChar) {
                if (tChar !== '') {
                    appendNodes.push(
                        $('<span class="user-count-reel user-count-reel--static">' + tChar + '</span>')[0]
                    );
                }
            } else {
                var fromDigit = parseInt(fChar) || 0;
                var toDigit = parseInt(tChar) || 0;
                var diff = toDigit - fromDigit;
                if (diff < 0) diff += 10;

                var $reel = $('<span class="user-count-reel"></span>');
                var $track = $('<span class="user-count-reel__track"></span>');
                $track.html(buildReelStripHTML());
                $reel.append($track);

                var startOffset = fromDigit * -1;
                $track.css('transform', 'translateY(' + startOffset + 'em)');

                reelsData.push({
                    $track: $track,
                    fromOffset: startOffset,
                    toOffset: toDigit * -1
                });

                appendNodes.push($reel[0]);
            }
        }

        if (appendNodes.length) {
            var frag = document.createDocumentFragment();
            for (var ai = 0; ai < appendNodes.length; ai++) {
                frag.appendChild(appendNodes[ai]);
            }
            $el[0].appendChild(frag);
        }

        // 延迟后开始滚轮动画
        setTimeout(function() {
            reelsData.forEach(function(data) {
                var startTime = null;
                var animate = function(currentTime) {
                    if (startTime === null) startTime = currentTime;
                    var t = currentTime - startTime;
                    var progress = Math.min(t / duration, 1);
                    var easeOut = 1 - Math.pow(1 - progress, 3);
                    var currentOffset = data.fromOffset + (data.toOffset - data.fromOffset) * easeOut;
                    data.$track.css('transform', 'translateY(' + currentOffset + 'em)');

                    if (t < duration) {
                        requestAnimationFrame(animate);
                    }
                };
                requestAnimationFrame(animate);
            });
        }, 30);

        // 动画结束后完全清理（下一幀再清 min-width，避免與純文字寬度換算不同步閃一下）
        var cleanupTimer = setTimeout(function() {
            $el.text(formattedTo);
            $el.removeClass('user-count--slot');
            var prevMw = prevMinWidth || '';
            requestAnimationFrame(function() {
                requestAnimationFrame(function() {
                    $el.css('min-width', prevMw);
                });
            });
            $el.removeData('userCountCleanupTimer');
        }, duration + 80);
        $el.data('userCountCleanupTimer', cleanupTimer);
    }
};

/* ======================= 产品详情页购买数量模块 ======================= */
const ProductBuyerCounter = {
    CACHE_DURATION: 24 * 60 * 60 * 1000, // 24小时缓存
    apiBaseUrl: '/api/buyermessage',

    /**
     * 初始化 - 检查页面是否有相关元素
     */
    init: function() {
        var totalsaleEl = document.getElementById('totalsale');
        if (!totalsaleEl) return; // 非产品详情页，不初始化

        this.updateTotalSales();
    },

    /**
     * 获取当前产品的盒数（从后端 data-product-quantity 属性获取）
     */
    getProductBoxNum: function() {
        var totalsaleEl = document.getElementById('totalsale');
        if (totalsaleEl) {
            var quantity = totalsaleEl.getAttribute('data-product-quantity');
            if (quantity) {
                return parseInt(quantity) || 1;
            }
        }
        return 1; // 默认1盒
    },

    /**
     * 获取缓存键名
     */
    getCacheKey: function(boxNum) {
        return 'xenical_buyers_' + boxNum;
    },

    getCacheTimeKey: function(boxNum) {
        return 'xenical_buyers_time_' + boxNum;
    },

    /**
     * 从缓存获取数据
     */
    getFromCache: function(boxNum) {
        var cacheKey = this.getCacheKey(boxNum);
        var cacheTimeKey = this.getCacheTimeKey(boxNum);
        var cached = localStorage.getItem(cacheKey);
        var cacheTime = localStorage.getItem(cacheTimeKey);
        var now = Date.now();

        if (cached && cacheTime && (now - parseInt(cacheTime)) < this.CACHE_DURATION) {
            return {
                value: cached,
                fromCache: true
            };
        }
        return null;
    },

    /**
     * 保存到缓存
     */
    saveToCache: function(boxNum, value) {
        var cacheKey = this.getCacheKey(boxNum);
        var cacheTimeKey = this.getCacheTimeKey(boxNum);
        localStorage.setItem(cacheKey, value);
        localStorage.setItem(cacheTimeKey, Date.now().toString());
    },

    /**
     * 更新所有购买数量显示
     */
    updateAllBuyerCounts: function(data) {
        for (var boxNum in data) {
            var countText = '近24小時已有' + data[boxNum] + '人訂購';
            // 更新 .box-buyer-count 元素
            $('.box-buyer-count[data-box-count="' + boxNum + '"]').text(countText);
        }
    },

    /**
     * 更新当前选中的盒数购买数量
     */
    updateTotalsale: function(count) {
        var totalsaleEl = document.getElementById('totalsale');
        if (totalsaleEl) {
            totalsaleEl.textContent = count;
        }
    },

    /**
     * 主更新函数
     */
    updateTotalSales: function() {
        var self = this;
        var totalsaleEl = document.getElementById('totalsale');
        if (!totalsaleEl) return;

        // 从后端 data-product-quantity 获取当前产品的盒数
        var boxNum = this.getProductBoxNum();

        // 先尝试从缓存获取当前选中盒数的值
        var cached = this.getFromCache(boxNum);
        var initialValue = cached ? cached.value : null;

        // 无论是否有缓存，都调用 API 更新所有数据
        fetch(this.apiBaseUrl + '/boxBuyers')
            .then(function(r) { return r.json(); })
            .then(function(response) {
                if (response.code == 1 && response.data) {
                    // 更新所有盒数的显示
                    self.updateAllBuyerCounts(response.data);

                    // 获取当前选中盒数的购买数量
                    var count = response.data[boxNum] || 0;

                    // 如果没有缓存过该盒数，或者 API 返回的值与缓存不同，则更新缓存
                    if (!initialValue || parseInt(initialValue) !== count) {
                        self.saveToCache(boxNum, count);
                    }

                    // 更新当前选中盒数的显示
                    self.updateTotalsale(count);
                }
            })
            .catch(function() {});
    },

    /**
     * 从 BuyerMessageManager 获取的 boxBuyers 数据更新 #totalsale
     * @param {Object} boxBuyers - API 返回的各盒数购买人数对象
     */
    updateFromBoxBuyers: function(boxBuyers) {
        var totalsaleEl = document.getElementById('totalsale');
        if (!totalsaleEl) return;

        // 获取后端产品盒数
        var boxNum = this.getProductBoxNum();

        if (boxBuyers && boxBuyers[boxNum]) {
            totalsaleEl.textContent = boxBuyers[boxNum];
            // 更新缓存
            this.saveToCache(boxNum, boxBuyers[boxNum]);
        }
    }
};

// 页面加载完成后初始化产品详情页购买数量
document.addEventListener('DOMContentLoaded', function() {
    ProductBuyerCounter.init();
});
