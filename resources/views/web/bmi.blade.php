@extends('web::layout.layout')

@section('style')
    @parent
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('static/less/bmi.css') }}?ver={{ config('app.asset_version') }}"/> -->
    <style>
        blockquote{
            border-left: 5px solid rgba(0,0,0,.05);
            padding: 20px;

            font-style: italic;

            position: relative;
            margin: 1.5em 1em 1.5em 3em;
            font-size: 1.2em;
            line-height: inherit;

        }
        .editor ul {
            list-style: disc;
            margin: 0 0 1.5em 3em;
        }
        .editor li{
            list-style: disc;
            margin-bottom: 20px;
        }
        .editor ul li::marker{

            unicode-bidi: isolate;
            font-variant-numeric: tabular-nums;
            text-transform: none;
            text-indent: 0px !important;
            text-align: start !important;
            text-align-last: start !important;
        }
        .editor p{
            margin-bottom: 1.8em;
            font-size: 18px;
        }

        .editor h2{
            margin-bottom: 20px;
            font-size: 2.4em;
        }
    </style>
@stop

@section('script')
    <script src="{{ asset('static/js/jquery.leoTextAnimate.js') }}?ver={{ config('app.asset_version') }}"></script>
    <script>
        const digitSymbols = ['?', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        let lastDigits = ['?', '?', '?'];

        function initDigit(el) {
            const inner = el.querySelector('.digit-inner');
            inner.innerHTML = digitSymbols.map(symbol => `<span>${symbol}</span>`).join('');
        }

        function animateDigit(el, num, alwaysSpin = false) {
            const inner = el.querySelector('.digit-inner');
            if (!inner) {
                return;
            }
            const firstSpan = inner.querySelector('span');
            let targetIndex = num === '?' ? 0 : (parseInt(num, 10) + 1);
            let currentTransform = inner.style.transform || 'translateY(0)';
            let currentIndex = 0;
            const emMatch = currentTransform.match(/translateY\(-([\d.]+)em\)/);
            if (emMatch) {
                currentIndex = Math.round(parseFloat(emMatch[1]));
            } else {
                const pxMatch = currentTransform.match(/translateY\(-([\d.]+)px\)/);
                if (pxMatch && firstSpan) {
                    const digitHeight = Math.max(1, Math.round(firstSpan.offsetHeight || firstSpan.getBoundingClientRect().height));
                    currentIndex = Math.round(parseFloat(pxMatch[1]) / digitHeight);
                }
            }

            if (!alwaysSpin && currentIndex === targetIndex) return;
            let rounds = alwaysSpin ? 1 : 0;
            let totalIndex = targetIndex + (rounds * 11);
            inner.style.transition = 'none';
            inner.style.transform = 'translateY(0)';
            void inner.offsetWidth;
            inner.style.transition = 'transform 1s ease-out';
            inner.style.transform = 'translateY(-' + totalIndex + 'em)';
        }

        function animateBMIDisplay(bmi) {
            const fixedBMI = bmi.toFixed(1);
            const [intPartRaw, decPartRaw] = fixedBMI.split('.');
            const intPart = intPartRaw.padStart(2, '0');
            const decPart = decPartRaw ? decPartRaw[0] : '0';
            const digits = [intPart[0], intPart[1], '.', decPart];
            const int1 = document.getElementById('int1');
            const int2 = document.getElementById('int2');
            const dec1 = document.getElementById('dec1');
            animateDigit(int1, digits[0] || '0', false);
            animateDigit(int2, digits[1] || '0', false);
            animateDigit(dec1, digits[3] || '0', false);
            lastDigits = [digits[0] || '0', digits[1] || '0', digits[3] || '0'];
        }

        document.addEventListener('DOMContentLoaded', function() {
            const digitEls = [
                document.getElementById('int1'),
                document.getElementById('int2'),
                document.getElementById('dec1'),
            ];

            digitEls.forEach(function(el) {
                initDigit(el);
                animateDigit(el, '?');
            });
            lastDigits = ['?', '?', '?'];

            document.querySelectorAll('#height, #weight').forEach(function(input) {
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/\D/g, '').slice(0, 3);
                });
            });

            document.querySelector('.count').addEventListener('click', function () {
                const height = parseFloat(document.getElementById('height').value);
                const weight = parseFloat(document.getElementById('weight').value);

                if (!height || !weight || height <= 0 || weight <= 0) {
                    alert("請正確輸入身高與體重");
                    return;
                }

                const bmi = weight / ((height / 100) ** 2);
                animateBMIDisplay(bmi);
            });

            document.querySelector('.reset').addEventListener('click', function () {
                document.getElementById('height').value = '';
                document.getElementById('weight').value = '';
                digitEls.forEach(function(el) {
                    animateDigit(el, '?');
                });
                lastDigits = ['?', '?', '?'];
            });
        });
    </script>
@stop

@section('content')
<main>
    @include('web.widgets.head-banner')
    @include('web.widgets.breadcrumb', ['itemsHtml' => '<li class="breadcrumb__item">BMI計算</li>'])
    {{--
    <section class="editor">
        {!! app('cache.config')->get('page_evaluate_article') !!}
    </section>
    --}}
    <section class="calc-wrapper" data-track-section-view data-track-section="calc.main" data-track-section-label="BMI 計算器">
        <h2 class="sr-only">立即計算你的BMI指數</h2>
        <div class="calculate">
            <h3 class="calc-title">BMI計算</h3>
            <p class="calc-sub">僅需輸入身高與體重，即可快速計算出BMI值，並參考BMI標準參照表，了解自己的身體狀況。</p>
            <form class="evaluate-form">
                <div class="form-group">
                    <label class="form-title" for="height">請輸入身高：</label>
                    <input class="form-control" type="number" id="height" name="height" placeholder="公分" inputmode="numeric" min="1" max="999">
                </div>
                <div class="form-group">
                    <label class="form-title" for="weight">請輸入體重：</label>
                    <input class="form-control" type="number" id="weight" name="weight" placeholder="公斤" inputmode="numeric" min="1" max="999">
                </div>
                <div class="btns">
                    <button class="btn reset" type="reset">重設</button>
                    <button class="btn count btn-ef1" type="button">開始計算</button>
                </div>
                <p class="privacy-note">本工具僅於瀏覽器運算，不會傳送或儲存任何輸入資料。如需更多資訊，請參閱<a href="/privacy">隱私權政策</a>。</p>
            </form>
            <div class="result">
                <h4 class="result-title">你的BMI結果為</h4>
                <p class="result-num" >
                    <span class="digit" id="int1" aria-hidden="true">
                        <span class="digit-inner">
                            <span>?</span><span>0</span><span>1</span><span>2</span><span>3</span><span>4</span><span>5</span><span>6</span><span>7</span><span>8</span><span>9</span>
                        </span>
                    </span>
                    <span class="digit" id="int2" aria-hidden="true">
                        <span class="digit-inner">
                            <span>?</span><span>0</span><span>1</span><span>2</span><span>3</span><span>4</span><span>5</span><span>6</span><span>7</span><span>8</span><span>9</span>
                        </span>
                    </span>
                    <span class="dot" aria-hidden="true">.</span>
                    <span class="digit" id="dec1" aria-hidden="true">
                        <span class="digit-inner">
                            <span>?</span><span>0</span><span>1</span><span>2</span><span>3</span><span>4</span><span>5</span><span>6</span><span>7</span><span>8</span><span>9</span>
                        </span>
                    </span>
                </p>
            </div>
        </div>

        <div class="calc-table">
            <h2 class="calc-title">BMI標準參照表</h2>
            {{--<p class="calc-sub">描述</p>--}}
            <table class="bmi-table">
                <thead>
                    <tr>
                    <th>歐美BMI標準</th>
                    <th>亞太區BMI標準</th>
                    <th>建議</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bmi-underweight">
                    <td>&lt;18.5</td>
                    <td>&lt;18.5</td>
                    <td>「體重過輕」，需要多運動，均衡飲食，以增加體能，維持健康！</td>
                    </tr>
                    <tr class="bmi-normal">
                    <td>18.5-24.9</td>
                    <td>18.5-22.9</td>
                    <td>恭喜！「健康體重」，要繼續保持！</td>
                    </tr>
                    <tr class="bmi-overweight">
                    <td>25-29.9</td>
                    <td>23-24.9</td>
                    <td>哦！有點「體重過重」了，要小心囉，趕快力行「健康體重管理」！</td>
                    </tr>
                    <tr class="bmi-obese">
                    <td>&ge;30</td>
                    <td>&ge;25</td>
                    <td>啊～「肥胖」了，需要立刻力行「健康體重管理」囉！</td>
                    </tr>
                </tbody>
            </table>
            <p class="calc-sub">資料來源：衛生福利部國民健康署</p>
            <p class="calc-link">想知道自己的每日總消耗熱量嗎？<a class="btn-ef1" href="{{ url('bmr') }}" data-observer="BMI-前往BMR" data-track-section="calc.main" data-track-name="calc.cross.bmr">立即計算你的BMR</a></p>
        </div>
    </section>

    <section class="page-news">
        <h2 class="sec-title">BMI專欄閱讀</h2>
        <div class="news-wrap">
            @foreach($news as $item)
                @include('web.widgets.news-card', ['item' => $item])
            @endforeach
        </div>
    </section>
        
    
</main>
@include('web.widgets.update-box')
@endsection