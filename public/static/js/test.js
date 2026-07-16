__webpack_require__.r(__webpack_exports__);
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.js");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var swiper_js_swiper__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! swiper/js/swiper */ "./node_modules/swiper/js/swiper.js");
/* harmony import */ var swiper_js_swiper__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(swiper_js_swiper__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _mixin_public_mixin__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./mixin/public_mixin */ "./src/js/mixin/public_mixin.js");
/* harmony import */ var _components_ring__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./components/ring */ "./src/js/components/ring.js");
/* harmony import */ var _components_lightBox__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./components/lightBox */ "./src/js/components/lightBox.js");


// component



vue__WEBPACK_IMPORTED_MODULE_0___default.a.config.productionTip = false;
var app = new vue__WEBPACK_IMPORTED_MODULE_0___default.a({
    components: {
        ring: _components_ring__WEBPACK_IMPORTED_MODULE_3__["default"],
        LightBox: _components_lightBox__WEBPACK_IMPORTED_MODULE_4__["default"]
    },
    mixins: [_mixin_public_mixin__WEBPACK_IMPORTED_MODULE_2__["default"]],
    data: function data() {
        return {
            bannerMainSwiper: null,
            bannerSubSwiper: null,
            bannerMainSwiperIndex: 0,
            bannerImageMove: 1,
            // swiper 圖片移動速度
            bannerImageScale: 1.2,
            // swiper 圖片放大程度
            bannerCountdown: false,
            // swiper 進度條倒數
            courseActiveType: '',
            // 顯示的全方位課程
            announcementCloseStorage: false,
            // 關閉公告紀錄
            loadingTime: 1500 // 將 isLoading 改為 false 的時間延遲

        };
    },
    watch: {
        bannerMainSwiperIndex: function bannerMainSwiperIndex() {
            var self = this;

            if (self.bannerMainSwiper) {
                if (self.bannerSubSwiper.realIndex !== self.bannerMainSwiperIndex) {
                    self.bannerSubSwiper.slideToLoop(self.bannerMainSwiperIndex);
                }
            }
        }
    },
    mounted: function mounted() {
        this.isHeaderWhite = true;
        this.headerCopyrightStatus = false;
        this.initSwiper();
        this.openAnnouncement();
    },
    methods: {
        customStart: function customStart() {
            // 自定義起始程式，如果沒有特別的起始動作整個拿掉，最後記得使用defaultStart拔掉loading狀態
            this.defaultStart();
        },
        initSwiper: function initSwiper() {
            var self = this;
            self.bannerMainSwiper = new swiper_js_swiper__WEBPACK_IMPORTED_MODULE_1___default.a('.js-index-main-banner', {
                allowTouchMove: true,
                autoplay: {
                    delay: 4000,
                    disableOnInteraction: false
                },
                grabCursor: true,
                watchSlidesProgress: true,
                mousewheelControl: true,
                speed: 1000,
                loop: true,
                on: {
                    init: function init() {
                        self.bannerCountdown = true;
                    },
                    slideChange: function slideChange() {
                        self.bannerMainSwiperIndex = this.realIndex;
                    },
                    progress: function progress() {
                        var selfSwiper = this;

                        for (var i = 0; i < selfSwiper.slides.length; i++) {
                            var slideProgress = selfSwiper.slides[i].progress;
                            var innerOffset = selfSwiper.width * self.bannerImageMove;
                            var innerTranslate = slideProgress * innerOffset;
                            var innerScaleOffset = Math.abs(1 - self.bannerImageScale);
                            var innerScale = Math.abs(slideProgress * innerScaleOffset) + 1;
                            selfSwiper.slides[i].querySelector('.o-index-banner__main-image').style.transform = "translate3d(".concat(innerTranslate, "px, 0, 0) scale(").concat(innerScale, ")");
                        }
                    },
                    touchStart: function touchStart() {
                        var selfSwiper = this;

                        for (var i = 0; i < selfSwiper.slides.length; i++) {
                            selfSwiper.slides[i].style.transition = '';
                        }
                    },
                    transitionStart: function transitionStart() {
                        self.bannerCountdown = false;
                    },
                    transitionEnd: function transitionEnd() {
                        self.bannerCountdown = true;
                    },
                    setTransition: function setTransition(speed) {
                        var selfSwiper = this;

                        for (var i = 0; i < selfSwiper.slides.length; i++) {
                            selfSwiper.slides[i].style.transition = "".concat(speed, "ms");
                            selfSwiper.slides[i].querySelector('.o-index-banner__main-image').style.transition = "".concat(speed, "ms");
                        }
                    }
                }
            });
            self.bannerSubSwiper = new swiper_js_swiper__WEBPACK_IMPORTED_MODULE_1___default.a('.js-index-sub-banner', {
                direction: 'vertical',
                allowTouchMove: false,
                watchSlidesProgress: true,
                speed: 1000,
                loop: true,
                on: {
                    progress: function progress() {
                        var selfSwiper = this;

                        for (var i = 0; i < selfSwiper.slides.length; i++) {
                            var slideProgress = selfSwiper.slides[i].progress;
                            var innerOffset = selfSwiper.height * self.bannerImageMove;
                            var innerTranslate = slideProgress * innerOffset;
                            var innerScaleOffset = Math.abs(1 - self.bannerImageScale);
                            var innerScale = Math.abs(slideProgress * innerScaleOffset) + 1;
                            selfSwiper.slides[i].querySelector('.o-index-banner__sub-image').style.transform = "translate3d(0, ".concat(innerTranslate, "px, 0) scale(").concat(innerScale, ")");
                        }
                    },
                    touchStart: function touchStart() {
                        var selfSwiper = this;

                        for (var i = 0; i < selfSwiper.slides.length; i++) {
                            selfSwiper.slides[i].style.transition = '';
                        }
                    },
                    setTransition: function setTransition(speed) {
                        var selfSwiper = this;

                        for (var i = 0; i < selfSwiper.slides.length; i++) {
                            selfSwiper.slides[i].style.transition = "".concat(speed, "ms");
                            selfSwiper.slides[i].querySelector('.o-index-banner__sub-image').style.transition = "".concat(speed, "ms");
                        }
                    }
                }
            });
        },
        clickBannerMore: function clickBannerMore($event) {
            location.href = $event.target.dataset.href || '';
        },
        setActiveCourseType: function setActiveCourseType() {
            var optionEl = document.querySelectorAll('.o-index-course-list__filter-option');

            if (optionEl.length) {
                var firstEl = optionEl[0];
                this.courseActiveType = firstEl.dataset.type;
            }
        },
        chgCourseTab: function chgCourseTab(e) {
            this.courseActiveType = e.currentTarget.dataset.type;
        },
        setCourseCategory: function setCourseCategory() {
            this.courseCategories = JSON.parse(this.$refs['course-categories'].dataset.rawCourse);
        },
        switchCourseCategory: function switchCourseCategory(index) {
            this.courseActiveType = Object.keys(this.courseCategories)[index];
        },
        openAnnouncement: function openAnnouncement() {
            if (this.$refs.indexAnnounceActive) {
                this.announcementCloseStorage = sessionStorage.getItem('announcementClose') || false;
                if (!this.announcementCloseStorage) this.toggleModal('isLightBox', this.$refs.lightBox, true);
            }
        },
        closeAnnouncement: function closeAnnouncement() {
            sessionStorage.setItem('announcementClose', true);
            this.announcementCloseStorage = true;
        }
    }
});

//# sourceURL=webpack:///./src/js/index.js?
