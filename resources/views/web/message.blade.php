@extends('web::layout.layout')

@section('style')
    @parent
@stop

@section('script')
    <script src="{{ asset('static/js/sweetalert2.js') }}"></script>
    <script src="{{ asset('static/js/FormHelper.js') }}"></script>
    <script>
        document.querySelector("#message-form").addEventListener("submit", e => {
            e.preventDefault();
            FormHelper.submit("#message-form", {
                rules: {
                    name: {
                        type: "required",
                        messages: { required: "請填寫您的暱稱" }
                    },
                    email: {
                        type: "email|required",
                        messages: { required: "請填寫郵箱", email: "郵箱格式不正確" }
                    },
                    content: {
                        type: "required",
                        messages: { required: "請填寫留言內容" }
                    },
                },
                onValidateFail: function () {
                    if (window.XenicalTracker) {
                        XenicalTracker.trackFormResult('message', false, 'validation');
                    }
                },
                onSuccess: function () {
                    if (window.XenicalTracker) {
                        XenicalTracker.trackFormResult('message', true);
                    }
                }
            });
        });

    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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

            const inputs = document.querySelectorAll('#email');

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

        @include('web.widgets.breadcrumb', ['itemsHtml' => '<li class="breadcrumb__item">線上客服</li>'])

        <form method="post" action="" id="message-form" data-track-section-view data-track-section="message.form" data-track-section-label="留言表單">
            {!! csrf_field() !!}
            <div class="data-group">
                <textarea class="form-textarea" name="content" rows="4" required minlength="10" maxlength="600" autocomplete="off" autocapitalize="none" autocorrect="off" spellcheck="false" inputmode="text" enterkeyhint="done" placeholder="我想補充訂單備註／想修改訂單地址..."></textarea>

                <div class="form-item">
                    <svg class="formicon" viewBox="0 0 1024 1024" width="200" height="200"><path d="M858.5 763.6c-18.9-44.8-46.1-85.2-80.8-120.7-34.7-35.5-74.5-63.2-119.5-83.2-45-20-92.8-30-143.2-30s-98.2 10-143.2 30c-45 20-84.8 47.7-119.5 83.2-34.7 35.5-61.9 76-80.8 120.7C151.8 808.3 172 858 210.2 894c38.2 36 84.5 54 138.9 54h429.8c54.4 0 100.7-18 138.9-54 38.2-36 58.4-85.7 60.7-130.4zM512 512c47.5 0 90.1-16.8 127.8-50.5s52.2-73.5 52.2-120.5-17.4-86.8-52.2-120.5C602.1 186.8 559.5 170 512 170s-90.1 16.8-127.8 50.5C346.5 254.2 332 298.3 332 345s14.5 86.8 52.2 116.5c37.7 33.7 80.3 50.5 127.8 50.5z" fill="currentColor"></path></svg>
                    <div class="form-input">
                        <input type="text" name="name" id="name" autocomplete="name" placeholder="請輸入您的暱稱" required>
                        <label>
                            <span style="transition-delay:0ms">請</span>
                            <span style="transition-delay:50ms">輸</span>
                            <span style="transition-delay:100ms">入</span>
                            <span style="transition-delay:150ms">您</span>
                            <span style="transition-delay:200ms">的</span>
                            <span style="transition-delay:250ms">暱</span>
                            <span style="transition-delay:300ms">稱</span>
                        </label>
                        <p class="hint">請輸入您的暱稱</p>
                    </div>
                </div>

                <div class="form-item">
                    <svg t="1765935415924" class="formicon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="159398" width="200" height="200"><path d="M1024 768c0 22.4-6.2 43.4-16.4 61.6L684.4 467.8 1004.2 188c12.4 19.8 19.8 42.8 19.8 68L1024 768 1024 768 1024 768zM512 533.6 957 144.2c-18.2-10-38.8-16.2-61-16.2L128 128c-22.2 0-42.8 6.2-61 16.2L512 533.6 512 533.6 512 533.6zM636.2 510l-103 90.2c-6 5.2-13.6 7.8-21 7.8-7.6 0-15-2.6-21-7.8L388 510 60.6 876.4c19.6 12.2 42.6 19.8 67.4 19.8l768.2 0c24.8 0 47.8-7.4 67.4-19.8L636.2 510 636.2 510 636.2 510zM19.8 188C7.4 207.8 0 230.8 0 256l0 512c0 22.4 6.2 43.4 16.4 61.6l323.4-362L19.8 188 19.8 188 19.8 188zM19.8 188" p-id="159399" fill="currentColor"></path></svg>
                    <div class="form-input">
                        <input type="email" id="email" name="email" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false"  autocorrect="off" placeholder="********@gmail.com" enterkeyhint="done" required>
                        <label>
                            <span style="transition-delay:0ms">請</span>
                            <span style="transition-delay:50ms">留</span>
                            <span style="transition-delay:100ms">下</span>
                            <span style="transition-delay:150ms">您</span>
                            <span style="transition-delay:200ms">的</span>
                            <span style="transition-delay:300ms">電</span>
                            <span style="transition-delay:350ms">子</span>
                            <span style="transition-delay:400ms">郵</span>
                            <span style="transition-delay:450ms">箱</span>
                        </label>
                        <p class="hint">********@gmail.com</p>
                    </div>
                </div>

            </div>

            <button class="form-btn btn-ef1" type="submit" data-observer="留言-確認送出" data-track-section="message" data-track-name="message.submit"><svg class="btn-icon sent-icon" viewBox="0 0 1024 1024"><use href="#icon-senticon"></use></svg>確認送出
            </button>
            <p class="form-desc"><svg class="righticon" viewBox="0 0 1024 1024"><use href="#icon-righticon"></use></svg>專業客服將在第一時間回覆您</p>
        </form>

        @include('web.widgets.qa', ['faqs' => $faqs])

    </main>
@include('web.widgets.update-box')
@endsection
