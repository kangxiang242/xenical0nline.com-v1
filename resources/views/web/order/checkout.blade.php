@extends('web::layout.layout')

@php
    $freight_where = \App\Services\ConfigService::get('freight_where',0);
    $freight_price = \App\Services\ConfigService::get('freight',0);

    $delivery_type_all = \App\Services\ConfigService::get('delivery_type',[]);
    if($delivery_type_all){
        $delivery_type_all = json_decode(\App\Services\ConfigService::get('delivery_type',[]),true);
    }
@endphp
@section('style')
    @parent
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet, noimageindex">
    <link rel="stylesheet" type="text/css" href="{{ asset('static/less/checkout.css') }}?ver={{ config('app.asset_version') }}"/>
    <style>
        body{
            min-height: 100vh;
            --price-duration: 2s;
            --overlay-duration: 2s;
        }
    </style>
@stop




@section('title','快速結賬-'.$goods->name)
@section('body-class', 'page-checkout')

@section('script')
    <script src="{{ asset('static/js/sweetalert2.js') }}"></script>
    <script src="{{ asset('static/js/xarea.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('static/js/FormHelper.js') }}"></script>
    <script src="{{ asset('static/js/price-animator.js') }}"></script>
    <script src="{{ asset('static/js/fp.min.js') }}"></script>
    <script id="CHECKOUT-M-1">

        var freight_where = parseInt('{{ $freight_where }}');

        var freight_price = parseInt('{{ $freight_price }}');

        var fpPromise = FingerprintJS.load();
        fpPromise.then(fp => fp.get()).then(
            function (result) {
                if (result.visitorId){
                    var code = result.visitorId;
                    $('input[name="fingerprint_token"]').val(code);

                }
            }
        )

    </script>
    <script>
        const formRules = {
            rules: {
                name: {
                    type: "required",
                        messages: { required: "請填寫收貨人名稱"}
                },
                phone: {
                    type: "phone|required",
                        messages: { required: "請填寫電話號碼", phone: "電話號碼格式不正確" }
                },
                email: {
                    type: "email|required",
                        messages: { required: "請填寫郵箱", email: "郵箱格式不正確" }
                },
                order_type: {
                    type: "required|number",
                        messages: { required: "請選擇配送方式"}
                },
                city: {
                    type: "required",
                        messages: { required: "請選擇縣市"}
                },
                county: {
                    type: "required",
                        messages: { required: "請選擇地區"}
                },
                street: {
                    type: "required",
                        messages: { required: "請選擇路段"}
                },
                goods_id: {
                    type: "required|number",
                        messages: { required: "產品數據出錯，請刷新重試"}
                },
                store_id: {
                    custom: function(value, formData) {
                        var orderType = formData.get('order_type') || document.querySelector('input[name="order_type"]:checked')?.value;
                        if (parseInt(orderType) > 0 && (!value || value === '0')) {
                            return '請選擇取貨門店';
                        }
                        return '';
                    },
                    messages: { custom: '請選擇取貨門店' }
                }
            },
        }
        let order_form = document.querySelector("#order-form");
        order_form.addEventListener("submit", e => {
            e.preventDefault();
            FormHelper.submit("#order-form", Object.assign({}, formRules, {
                onValidateFail: function (errors) {
                    if (window.XenicalTracker) {
                        var code = (errors[0] && errors[0].field) || 'validation_failed';
                        XenicalTracker.markCheckoutValidationFail(code);
                    }
                },
                onSuccess: function (data) {
                    if (data && data.redirect) {
                        if (window.XenicalTracker) {
                            XenicalTracker.conversion('purchase', {
                                product_id: document.querySelector('#order-form input[name="goods_id"]') ? document.querySelector('#order-form input[name="goods_id"]').value : '',
                                order_no: data.order_no || data.order_id || '',
                                amount: data.amount || data.total || ''
                            });
                        }
                        // 跳转到订单详情页
                        window.location.href = data.redirect;
                    } else if (data && data.msg) {
                        // 顯示服務器返回的錯誤訊息
                        Swal.fire({
                            icon: 'error',
                            iconColor: '#fff',
                            text: data.msg,
                            color: '#fff',
                            background: 'rgba(0,0,0,0.7)',
                            width: 'auto',
                            backdrop: false,
                            timer: 2000,
                            showConfirmButton: false,
                        });
                    }
                }
            }));
        });
        setInterval(function () {
            FormHelper.validate(order_form, new FormData(order_form),formRules).then(errors => {
                if(!errors.length){
                    $('.checkout-btn').addClass('ready');
                }
            });
        },1000)



    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 初始化价格动画和安全扫描动画


            // Phone 输入框格式化
            const phoneInput = document.getElementById('phone');
            if (phoneInput) {
                function formatTaiwanPhone(value) {
                    const digits = value.replace(/\D/g, '').slice(0, 10);
                    if (digits.length <= 4) {
                        return digits;
                    }
                    if (digits.length <= 7) {
                        return digits.replace(/^(\d{4})(\d+)/, '$1 $2');
                    }
                    return digits.replace(/^(\d{4})(\d{3})(\d+)/, '$1 $2 $3');
                }

                function getCursorPositionAfterFormat(oldValue, newValue, cursor) {
                    const digitsBeforeCursor = oldValue
                        .slice(0, cursor)
                        .replace(/\D/g, '')
                        .length;
                    let count = 0;
                    for (let i = 0; i < newValue.length; i++) {
                        if (/\d/.test(newValue[i])) count++;
                        if (count === digitsBeforeCursor) {
                            return i + 1;
                        }
                    }
                    return newValue.length;
                }

                phoneInput.addEventListener('input', (e) => {
                    const oldValue = e.target.value;
                    const cursor = e.target.selectionStart;
                    const formatted = formatTaiwanPhone(oldValue);
                    const newCursor = getCursorPositionAfterFormat(oldValue, formatted, cursor);
                    e.target.value = formatted;
                    e.target.setSelectionRange(newCursor, newCursor);
                });

                phoneInput.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                    const digits = pastedText.replace(/\D/g, '').slice(0, 10);
                    const formatted = formatTaiwanPhone(digits);
                    phoneInput.value = formatted;
                });
            }

            // Email 输入框自动完成
            const emailInput = document.getElementById('email');
            const emailHelper = document.getElementById('email-helper');

            if (emailInput) {
                const isAndroid = /Android/i.test(navigator.userAgent);

                const typoMap = {
                    'gmial.com': 'gmail.com',
                    'gamil.com': 'gmail.com',
                    'gnail.com': 'gmail.com',
                    'hotnail.com': 'hotmail.com',
                    'outllok.com': 'outlook.com'
                };

                function hideHelper() {
                    if (emailHelper) {
                        emailHelper.hidden = true;
                        emailHelper.textContent = '';
                    }
                }

                function showHelper(text) {
                    if (emailHelper) {
                        emailHelper.textContent = text;
                        emailHelper.hidden = false;
                    }
                }

                emailInput.addEventListener('input', () => {
                    const value = emailInput.value;
                    const atPos = value.indexOf('@');

                    if (atPos === -1) {
                        hideHelper();
                        return;
                    }

                    if (!isAndroid || !emailHelper) return;

                    const name = value.slice(0, atPos);
                    const domain = value.slice(atPos + 1).toLowerCase();

                    if (domain.length > 3) {
                        hideHelper();
                        return;
                    }

                    if ('gmail.com'.startsWith(domain)) {
                        showHelper(`${name}@gmail.com`);
                    } else {
                        hideHelper();
                    }
                });

                emailInput.addEventListener('blur', () => {
                    if (!emailHelper) return;

                    const value = emailInput.value.trim();
                    const parts = value.split('@');

                    if (parts.length !== 2) return;

                    const [name, domain] = parts;
                    const fixed = typoMap[domain];

                    if (fixed) {
                        showHelper(`${name}@${fixed}`);
                    }
                });

                if (emailHelper) {
                    emailHelper.addEventListener('click', () => {
                        emailInput.value = emailHelper.textContent;
                        hideHelper();
                        emailInput.focus();
                    });
                }
            }

            // 所有输入框的 has-value 类管理
            const inputs = document.querySelectorAll('#name, #phone, #email, #address');

            inputs.forEach(input => {
                const wrap = input.closest('.form-input');
                if (!wrap) return;

                function syncHint() {
                    const hasValue = input.value.trim().length > 0;
                    wrap.classList.toggle('has-value', hasValue);
                }

                input.addEventListener('input', syncHint);
                input.addEventListener('blur', syncHint);
                input.addEventListener('change', syncHint);

                // 初始化状态
                syncHint();
            });
        });
    </script>
@stop

@section('footer-menu')

    <div class="footer-menu">
        <div class="shop-price">
            <p class="goods-title">{{ $goods->name }}</p>
            <p class="red-price"><span style="font-size: 0.22rem; font-weight: 700; margin-right: 0.1rem;">訂單總額：NT$</span><span id="foot-order-price">{{ number_format(round($goods->price>=$freight_where?$goods->price:$goods->price+$freight_price)) }}</span></p>
        </div>
        <div class="shop-buy">
            <button class="form-btn" onclick="$('#order-form').submit();">
                <svg class="checkouticon" viewBox="0 0 1024 1024"><use href="#icon-checkouticon"></use></svg>提交訂單
            </button>
        </div>
    </div>
    <div id="cover"></div>
@stop

@section('content')
<main class="page-checkout">
    <form class="checkout-container" method="POST" action="{{ url('order') }}" id="order-form" >
        {{ csrf_field() }}
        <input type="hidden" value="{{ $form_token }}" name="form_token">
        <input type="hidden" value="{{ $goods->id }}" name="goods_id">
        <input type="hidden" value="" name="timezone">
        <input type="hidden" value="" name="fingerprint_token">

        <div class="card" data-track-section-view data-track-section="checkout.summary" data-track-section-label="訂購內容">
            <p class="form-title">訂購內容：</p>
            <div class="goods">
                <div class="img-wrap">
                    <img class="goods-img" src="{{ asset_upload($goods->img) }}" alt="{{ $goods->name }}">
                </div>
                <div class="info">
                    <p class="goods-title">{{ $goods->name }}<br>{{ $goods->sub_name }}</p>
                    <p class="sub-title">• 歐洲原裝進口</p>
                    <p class="sub-title">• 42顆/盒 共{{ $goods->quantity }}盒</p>
                    <p class="sub-title">• 隱密包裝</p>
                </div>
            </div>
            <dl class="order-summary">
                <dt>商品原價</dt>
                <dd><span class="twd">NT$</span><span id="goods-price">{{ number_format(round($goods->market_price)) }}</span></dd>

                @if((int) $goods->discount_percent > 0)
                <dt>官網優惠</dt>
                <dd>
                    <span class="twd">— NT$</span><span id="discount-price">{{ number_format(round($goods->market_price-$goods->price)) }}</span>
                    <p class="discount-sub">(已為您優惠<span class="descount-num">{{ (int) $goods->discount_percent }}</span>%)</p>
                </dd>
                @endif

                <dt>運費</dt>
                <dd>
                    <span id="freight-price">
                        @if($goods->price<$freight_where)
                            <span class="twd">NT$</span>{{ round($freight_price) }}
                        @else
                            <span class="twd">NT$</span>0
                        @endif
                    </span>
                </dd>

                <dt>訂單總額</dt>
                <dd>
                    <div class="price-box" data-product-id="{{ $goods->id }}" data-market-price="{{ round($goods->market_price) }}" data-price="@if($goods->price<$freight_where){{ round($goods->price+$freight_price) }}@else{{ round($goods->price) }}@endif">
                        <span class="twd">NT$</span><span class="price-number" id="order-price">@if($goods->price<$freight_where){{ number_format(round($goods->price+$freight_price)) }}@else{{ number_format(round($goods->price)) }}@endif
                        </span>
                    </div>
                </dd>

            </dl>

        </div>

        <div class="card" data-track-section-view data-track-section="checkout.form" data-track-section-label="配送表單">
            <p class="form-title">配送訊息：</p>
            <div class="data-group">
                <div class="form-item">
                    <svg class="formicon" viewBox="0 0 1024 1024"><use href="#icon-formicon-name"></use></svg>
                    <div class="form-input">
                        <input type="text" id="name" name="name" autocomplete="off"  placeholder="請輸入收貨人姓名" required>
                        <label>
                            <span style="transition-delay:0ms">請</span>
                            <span style="transition-delay:50ms">問</span>
                            <span style="transition-delay:100ms">如</span>
                            <span style="transition-delay:150ms">何</span>
                            <span style="transition-delay:200ms">稱</span>
                            <span style="transition-delay:300ms">呼</span>
                            <span style="transition-delay:350ms">您</span>
                        </label>
                        <p class="hint">請輸入收貨人名字</p>
                    </div>
                    <svg class="safeicon" viewBox="0 0 1024 1024"><use href="#icon-safeicon"></use></svg>
                </div>
                <div class="form-item">
                    <svg class="formicon" viewBox="0 0 1024 1024"><use href="#icon-formicon-phone"></use></svg>
                    <div class="form-input">
                        <input type="tel" id="phone" name="phone" inputmode="tel" autocomplete="tel" pattern="09\d{2}\s\d{3}\s\d{3}" maxlength="12" placeholder="09** *** ***" required>
                        <label>
                            <span style="transition-delay:0ms">請</span>
                            <span style="transition-delay:50ms">輸</span>
                            <span style="transition-delay:100ms">入</span>
                            <span style="transition-delay:150ms">收</span>
                            <span style="transition-delay:200ms">貨</span>
                            <span style="transition-delay:300ms">人</span>
                            <span style="transition-delay:350ms">電</span>
                            <span style="transition-delay:400ms">話</span>
                            <span style="transition-delay:450ms">號</span>
                            <span style="transition-delay:500ms">碼</span>
                        </label>
                        <p class="hint">09** *** ***</p>
                    </div>
                    <svg class="safeicon" viewBox="0 0 1024 1024"><use href="#icon-safeicon"></use></svg>
                </div>
                <div class="form-item">
                    <svg class="formicon" viewBox="0 0 1024 1024"><use href="#icon-formicon-email"></use></svg>
                    <div class="form-input">
                        <input type="email" id="email" name="email" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false"  autocorrect="off" placeholder="********@gmail.com" enterkeyhint="done" required>
                        <label>
                            <span style="transition-delay:0ms">請</span>
                            <span style="transition-delay:50ms">輸</span>
                            <span style="transition-delay:100ms">入</span>
                            <span style="transition-delay:150ms">電</span>
                            <span style="transition-delay:200ms">子</span>
                            <span style="transition-delay:300ms">信</span>
                            <span style="transition-delay:350ms">箱</span>
                        </label>
                        <p class="hint">********@gmail.com</p>
                    </div>
                    <svg class="safeicon" viewBox="0 0 1024 1024"><use href="#icon-safeicon"></use></svg>
                </div>
            </div>

            <p class="form-title">配送與付款方式：</p>
            <div class="radio-box">
                <div class="form-radio">
                    <input type="radio" id="order-type-1" name="order_type" value="1" checked>
                    <label class="radio-label" for="order-type-1">
                        <svg class="sevenicon" viewBox="0 0 272.68729 257.44435"><use href="#icon-sevenicon-1"></use></svg>
                        <span class="text">7-Eleven便利店<br>取貨付款</span>
                    </label>
                </div>
                <div class="form-radio">
                    <input type="radio" id="order-type-0" name="order_type" value="0">
                    <label class="radio-label" for="order-type-0">
                        <svg class="sevenicon" style="transform: scale(1.15);" viewBox="0 0 1548 1123"><use href="#icon-sevenicon-2"></use></svg>
                        <span class="text">黑貓宅配到府<br>貨到付款</span>
                    </label>
                </div>
            </div>

            <p class="form-title" id="order-type-title">配送至</p>
            <div class="form-group">
                <div class="form-select">
                    <div class="select-box" id="load-1">
                        <select name="city" id="city">
                            <option value="0">選擇縣市</option>
                        </select>
                        <svg class="select-icon" viewBox="0 0 1280 1024"><use href="#icon-select-icon"></use></svg>
                    </div>

                    <div class="select-box" id="load-2">
                        <select name="county" id="county">
                            <option value="0">選擇地區</option>
                        </select>
                        <svg class="select-icon" viewBox="0 0 1280 1024"><use href="#icon-select-icon"></use></svg>
                    </div>

                    <div class="select-box" id="load-3">
                        <select name="street" id="street">
                            <option value="0">選擇路段</option>
                        </select>
                        <svg class="select-icon" viewBox="0 0 1280 1024"><use href="#icon-select-icon"></use></svg>
                    </div>

                </div>
                <div class="form-item form-address" id="form-address-row">
                    <svg class="formicon" viewBox="0 0 1024 1024"><use href="#icon-formicon-address"></use></svg>
                    <div class="form-input">
                    <input type="text" id="address" name="address" autocomplete="off" placeholder="請輸入詳細收貨地址">
                        <label>
                            <span style="transition-delay:0ms">請</span>
                            <span style="transition-delay:50ms">輸</span>
                            <span style="transition-delay:100ms">入</span>
                            <span style="transition-delay:150ms">詳</span>
                            <span style="transition-delay:200ms">細</span>
                            <span style="transition-delay:300ms">收</span>
                            <span style="transition-delay:350ms">貨</span>
                            <span style="transition-delay:400ms">地</span>
                            <span style="transition-delay:450ms">址</span>
                        </label>
                        <p class="hint">請輸入詳細收貨地址</p>
                    </div>
                    <svg class="safeicon" viewBox="0 0 1024 1024"><use href="#icon-safeicon"></use></svg>
                </div>
                <div class="form-store" id="form-store-row">
                </div>
            </div>

            <p class="form-title">訂單備註</p>
            <textarea class="form-textarea" name="remarks" placeholder="（選填）"></textarea>
        </div>
        <button class="checkout-btn" type="submit" data-observer="提交訂單" data-track-section="checkout" data-track-name="checkout.submit">
            <svg class="checkouticon" viewBox="0 0 1024 1024"><use href="#icon-safeicon"></use></svg>提交訂單
        </button>
    </form>

    <div class="security-scan-overlay">
        <svg class="pro-icon" viewBox="0 0 1024 1024"><use href="#icon-pro-icon"></use></svg>
        <p class="security-scan-text">安全環境檢測中</p>
        <p class="security-scan-status">正在掃描結帳環境...</p>
        <div class="security-scan-progress">
            <div class="security-scan-progress-bar"></div>
        </div>

    </div>
</main>
@endsection






