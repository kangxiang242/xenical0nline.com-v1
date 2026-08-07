@extends('web::layout.layout')

@section('style')
    @parent
@stop

@section('script')

    <script src="{{ asset('static/js/sweetalert2.js') }}"></script>
        <script src="{{ asset('static/js/FormHelper.js') }}"></script>
    <script>
        // 查询方式切换
        document.addEventListener('DOMContentLoaded', function() {
            const radios = document.querySelectorAll('input[name="check_type"]');
            const sectionOrderId = document.getElementById('section-order-id');
            const sectionContact = document.getElementById('section-contact');
            const orderIdInput = document.getElementById('order_id');
            const phoneInput = document.getElementById('phone');
            const emailInput = document.getElementById('email');
            const activeBg = document.querySelector('.check-type-toggle .active-bg');

            function syncCheckTypePill() {
                if (!activeBg || !radios.length) return;
                const checked = document.querySelector('input[name="check_type"]:checked');
                if (!checked) return;
                const index = Array.from(radios).indexOf(checked);
                activeBg.style.transform = 'translateX(' + (index * 100) + '%)';
            }

            function toggleCheckType() {
                const selectedType = document.querySelector('input[name="check_type"]:checked').value;

                if (selectedType === 'order_id') {
                    sectionOrderId.style.display = 'block';
                    sectionContact.style.display = 'none';
                    orderIdInput.required = true;
                    phoneInput.required = false;
                    emailInput.required = false;
                } else {
                    sectionOrderId.style.display = 'none';
                    sectionContact.style.display = 'block';
                    orderIdInput.required = false;
                    phoneInput.required = true;
                    emailInput.required = true;
                }
                syncCheckTypePill();
            }

            radios.forEach(radio => {
                radio.addEventListener('change', toggleCheckType);
            });

            // 初始化
            toggleCheckType();
        });

        // 表单提交
        document.querySelector("#check-form").addEventListener("submit", e => {
            e.preventDefault();

            const checkType = document.querySelector('input[name="check_type"]:checked').value;
            let rules = {};

            if (checkType === 'order_id') {
                const orderId = document.getElementById('order_id').value.trim();
                if (!orderId) {
                    if (window.XenicalTracker) {
                        XenicalTracker.trackFormResult('order_check', false, 'order_id_empty');
                    }
                    Swal.fire({
                        icon: 'error',
                        text: '請填寫訂單編號',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }
                if (!/^\d{4}\s\d{4}\s\d{4}\s\d{4}$/.test(orderId)) {
                    if (window.XenicalTracker) {
                        XenicalTracker.trackFormResult('order_check', false, 'order_id_format');
                    }
                    Swal.fire({
                        icon: 'error',
                        text: '訂單編號格式不正確',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }
            } else {
                rules = {
                    email: {
                        type: "email|required",
                        messages: { required: "請填寫郵箱", email: "郵箱格式不正確" }
                    },
                    phone: {
                        type: "required|phone",
                        messages: { required: "請填寫訂購電話", phone: "電話格式不正確" }
                    }
                };
            }

            FormHelper.submit("#check-form", {
                rules: rules,
                onValidateFail: function () {
                    if (window.XenicalTracker) {
                        XenicalTracker.trackFormResult('order_check', false, 'validation');
                    }
                },
                onSuccess: function () {
                    if (window.XenicalTracker) {
                        XenicalTracker.trackFormResult('order_check', true);
                    }
                }
            });
        });

    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

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


            const emailInput = document.getElementById('email');
            const emailHelper = document.getElementById('email-helper');

            if (emailInput && emailHelper) {
                const isAndroid = /Android/i.test(navigator.userAgent);

                const typoMap = {
                    'gmial.com': 'gmail.com',
                    'gamil.com': 'gmail.com',
                    'gnail.com': 'gmail.com',
                    'hotnail.com': 'hotmail.com',
                    'outllok.com': 'outlook.com'
                };

                function hideHelper() {
                    emailHelper.hidden = true;
                    emailHelper.textContent = '';
                }

                function showHelper(text) {
                    emailHelper.textContent = text;
                    emailHelper.hidden = false;
                }

                emailInput.addEventListener('input', () => {
                    const value = emailInput.value;
                    const atPos = value.indexOf('@');

                    if (atPos === -1) {
                        hideHelper();
                        return;
                    }

                    if (!isAndroid) return;

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
                    const value = emailInput.value.trim();
                    const parts = value.split('@');

                    if (parts.length !== 2) return;

                    const [name, domain] = parts;
                    const fixed = typoMap[domain];

                    if (fixed) {
                        showHelper(`${name}@${fixed}`);
                    }
                });

                emailHelper.addEventListener('click', () => {
                    emailInput.value = emailHelper.textContent;
                    hideHelper();
                    emailInput.focus();
                });
            }

            const orderIdInput = document.getElementById('order_id');
            if (orderIdInput) {
                function formatOrderId(value) {
                    const digits = value
                        .replace(/[０-９]/g, d => String.fromCharCode(d.charCodeAt(0) - 65248))
                        .replace(/\D/g, '')
                        .slice(0, 16);

                    return digits.replace(/(\d{4})(?=\d)/g, '$1 ');
                }

                function getCursorAfterFormat(oldValue, newValue, cursor) {
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

                orderIdInput.addEventListener('input', (e) => {
                    const oldValue = e.target.value;
                    const cursor = e.target.selectionStart;

                    const formatted = formatOrderId(oldValue);
                    const newCursor = getCursorAfterFormat(oldValue, formatted, cursor);

                    e.target.value = formatted;
                    e.target.setSelectionRange(newCursor, newCursor);
                });

                orderIdInput.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pasted = (e.clipboardData || window.clipboardData).getData('text');
                    orderIdInput.value = formatOrderId(pasted);
                });
            }

            const inputs = document.querySelectorAll('#phone, #email, #order_id');

            inputs.forEach(input => {
                const wrap = input.closest('.form-input');
                if (!wrap) return;

                function syncHint() {
                    const hasValue = input.value.trim().length > 0;
                    wrap.classList.toggle('has-value', hasValue);
                    console.log(input.id, 'has-value:', hasValue);
                }

                input.addEventListener('input', syncHint);
                input.addEventListener('blur', syncHint);

                syncHint();
            });
        });
    </script>
@stop

@section('content')
<main class="page-form">
    @include('web.widgets.head-banner')
    @include('web.widgets.breadcrumb', ['itemsHtml' => '<li class="breadcrumb__item">訂單查詢</li>'])

    <form action="" id="check-form" method="post" onsubmit="return orderCheck()" data-track-section-view data-track-section="order_check.form" data-track-section-label="訂單查詢">
        {{ csrf_field() }}

        <div class="check-type-selector">
            <div class="check-type-toggle" role="radiogroup" aria-label="查詢方式">
                <input type="radio" name="check_type" id="check_type_order" value="order_id" checked>
                <label for="check_type_order">訂單編號查詢</label>
                <input type="radio" name="check_type" id="check_type_contact" value="contact">
                <label for="check_type_contact">聯絡資訊查詢</label>
                <div class="active-bg" aria-hidden="true"></div>
            </div>
        </div>

        <!-- 订单编号查询 -->
        <div class="check-section" id="section-order-id">
            <div class="data-group">
                <div class="form-item">
                    <svg class="formicon" viewBox="0 0 1024 1024">
                        <path d="M416 151.2h192c26.4 0 48-21.6 48-48s-21.6-48-48-48H416c-26.4 0-48 21.6-48 48s21.6 48 48 48z" fill="currentColor"/>
                        <path d="M848 103.3H728c0 52.8-43.2 96-96 96H392c-52.8 0-96-43.2-96-96H176c-39.8 0-72 32.2-72 72v721.5c0 39.8 32.2 72 72 72h672c39.8 0 72-32.2 72-72V175.3c0-39.8-32.3-72-72-72zM280 343.2h464c13.2 0 24 10.8 24 24s-10.8 24-24 24H280c-13.2 0-24-10.8-24-24s10.8-24 24-24z m0 168h464c13.2 0 24 10.8 24 24s-10.8 24-24 24H280c-13.2 0-24-10.8-24-24s10.8-24 24-24z m232 192c0 13.2-10.8 24-24 24H280c-13.2 0-24-10.8-24-24s10.8-24 24-24h208c13.2 0 24 10.8 24 24z m264 0c0 13.2-10.8 24-24 24H608c-13.2 0-24-10.8-24-24s10.8-24 24-24h144c13.2 0 24 10.8 24 24z" fill="currentColor"/>
                    </svg>
                    <div class="form-input">
                        <input type="text" id="order_id" name="order_id" inputmode="numeric" autocomplete="off" pattern="^\d{4}\s\d{4}\s\d{4}\s\d{4}$" minlength="19" maxlength="19" placeholder="**** **** **** ****">
                        <label>
                            <span style="transition-delay:0ms">請</span>
                            <span style="transition-delay:50ms">輸</span>
                            <span style="transition-delay:100ms">入</span>
                            <span style="transition-delay:150ms">您</span>
                            <span style="transition-delay:200ms">的</span>
                            <span style="transition-delay:300ms">訂</span>
                            <span style="transition-delay:350ms">單</span>
                            <span style="transition-delay:400ms">編</span>
                            <span style="transition-delay:450ms">號</span>
                        </label>
                        <p class="hint">**** **** **** ****</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 联络资讯查询 -->
        <div class="check-section" id="section-contact" style="display: none;">
            <div class="data-group">
                <div class="form-item">
                    <svg class="formicon" viewBox="0 0 1024 1024"><use href="#icon-formicon-phone"></use></svg>
                    <div class="form-input">
                        <input type="tel" id="phone" name="phone" inputmode="tel" autocomplete="tel" pattern="09\d{2}\s\d{3}\s\d{3}" maxlength="12" placeholder="09** *** ***">
                        <label>
                            <span style="transition-delay:0ms">請</span>
                            <span style="transition-delay:50ms">輸</span>
                            <span style="transition-delay:100ms">入</span>
                            <span style="transition-delay:150ms">訂</span>
                            <span style="transition-delay:200ms">單</span>
                            <span style="transition-delay:300ms">預</span>
                            <span style="transition-delay:350ms">留</span>
                            <span style="transition-delay:400ms">電</span>
                            <span style="transition-delay:450ms">話</span>
                        </label>
                        <p class="hint">09** *** ***</p>
                    </div>
                </div>
                <div class="form-item">
                    <svg class="formicon" viewBox="0 0 1024 1024"><use href="#icon-formicon-email"></use></svg>
                    <div class="form-input">
                        <input type="email" id="email" name="email" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false"  autocorrect="off" placeholder="********@gmail.com" enterkeyhint="done">
                        <label>
                            <span style="transition-delay:0ms">請</span>
                            <span style="transition-delay:50ms">輸</span>
                            <span style="transition-delay:100ms">入</span>
                            <span style="transition-delay:150ms">訂</span>
                            <span style="transition-delay:200ms">單</span>
                            <span style="transition-delay:300ms">預</span>
                            <span style="transition-delay:350ms">留</span>
                            <span style="transition-delay:400ms">郵</span>
                            <span style="transition-delay:450ms">箱</span>
                        </label>
                        <p class="hint">********@gmail.com</p>
                    </div>
                </div>
            </div>
        </div>
        <button class="form-btn btn-ef1" type="submit" data-observer="訂單查詢-立即查詢" data-track-section="order_check" data-track-name="order_check.submit"><svg class="btn-icon" viewBox="0 0 1024 1024"><use href="#icon-checkicon"></use></svg>立即查詢</button>
        <p class="form-desc"><svg class="righticon" viewBox="0 0 1024 1024"><use href="#icon-righticon"></use></svg>訂單查詢結果的個人隱私資料已加密保護</p>
    </form>
    @include('web.widgets.qa', ['faqs' => $faqs])
</main>
@endsection
