@extends('web::layout.layout')

@section('style')
    @parent
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('static/less/bodyfat.css') }}?ver={{ config('app.asset_version') }}"/> -->
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
        .table-scroll{
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
        }
        .table-scroll .bodyfat-table{
            min-width: 680px;
        }
    </style>
    @if(isset($css) && $css)
    <style type="text/css">
        {!! $css !!}
    </style>
    @endif
@stop

@section('script')
    <script>
        let lastDigits = ['?', '?', '?'];

        function animateDigit(el, num, alwaysSpin = false) {
            const inner = el.querySelector('.digit-inner');
            if (!inner) {
                return;
            }
            const firstSpan = inner.querySelector('span');
            const targetIndex = num === '?' ? 0 : (parseInt(num, 10) + 1);
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
            const rounds = alwaysSpin ? 1 : 0;
            const totalIndex = targetIndex + (rounds * 11);
            inner.style.transition = 'none';
            inner.style.transform = 'translateY(0)';
            void inner.offsetWidth;
            inner.style.transition = 'transform 1s ease-out';
            inner.style.transform = 'translateY(-' + totalIndex + 'em)';
        }

        function animateBodyFatDisplay(bf) {
            const fixedBF = bf.toFixed(1);
            const [intPartRaw, decPartRaw] = fixedBF.split('.');
            const intPart = intPartRaw.padStart(2, '0');
            const decPart = decPartRaw ? decPartRaw[0] : '0';
            animateDigit(document.getElementById('int1'), intPart[0]);
            animateDigit(document.getElementById('int2'), intPart[1]);
            animateDigit(document.getElementById('dec1'), decPart);
            lastDigits = [intPart[0], intPart[1], decPart];
        }

        window.addEventListener('DOMContentLoaded', function () {
            ['int1', 'int2', 'dec1'].forEach(id => animateDigit(document.getElementById(id), '?'));
            lastDigits = ['?', '?', '?'];
        });

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelector('.count').addEventListener('click', function () {
                const height = parseFloat(document.getElementById('height').value);
                const weight = parseFloat(document.getElementById('weight').value);
                const age = parseFloat(document.getElementById('age').value);
                const gender = document.querySelector('input[name="gender"]:checked')?.value;

                if (!height || !weight || !age || !gender || height <= 0 || weight <= 0 || age <= 0) {
                    alert("請正確輸入所有欄位");
                    return;
                }

                const bmi = weight / ((height / 100) ** 2);
                const bf = gender === 'male'
                    ? (1.20 * bmi + 0.23 * age - 16.2)
                    : (1.20 * bmi + 0.23 * age - 5.4);

                animateBodyFatDisplay(bf);
            });

            document.querySelector('.reset').addEventListener('click', function () {
                ['height', 'weight', 'age'].forEach(id => document.getElementById(id).value = '');
                ['int1', 'int2', 'dec1'].forEach(id => animateDigit(document.getElementById(id), '?'));
                lastDigits = ['?', '?', '?'];
            });
        });
    </script>
@stop


@section('content')
<main>
    @include('web.widgets.head-banner')
    @include('web.widgets.breadcrumb', ['itemsHtml' => '<li class="breadcrumb__item">體脂肪率計算</li>'])
    {{--
    <section class="editor">
        {!! app('cache.config')->get('page_body_fat_article') !!}
    </section>
    --}}
    <section class="calc-wrapper" data-track-section-view data-track-section="calc.main" data-track-section-label="體脂肪計算器">
        <h2 class="sr-only">立即計算你的體脂肪率</h2>
        <div class="calculate">
            <h3 class="calc-title">體脂肪率計算</h3>
            <p class="calc-sub">僅需輸入身高、體重、年齡及生理性別，即可快速計算出體脂肪率，並參考體脂肪率建議值參照表，了解自己的身體狀況。</p>
            <form class="evaluate-form" onsubmit="return false;">
                <div class="form-group">
                    <label class="form-title visually-hidden" for="gender">生理性別：</label>
                    <div class="gender-toggle" role="radiogroup" id="genderToggle">
                        <input type="radio" name="gender" id="female" value="female" checked>
                        <label for="female">女性</label>
                        <input type="radio" name="gender" id="male" value="male">
                        <label for="male">男性</label>
                        <div class="active-bg"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-title" for="age">年齡：</label>
                    <input class="form-control" type="number" id="age" name="age" placeholder="歲" inputmode="numeric">
                </div>
                <div class="form-group">
                    <label class="form-title" for="height">身高：</label>
                    <input class="form-control" type="number" id="height" name="height" placeholder="公分" inputmode="decimal">
                </div>
                <div class="form-group">
                    <label class="form-title" for="weight">體重：</label>
                    <input class="form-control" type="number" id="weight" name="weight" placeholder="公斤" inputmode="decimal">
                </div>
                <div class="btns">
                    <button class="btn reset" type="reset">重設</button>
                    <button class="btn count btn-ef1" type="button">開始計算</button>
                </div>
                <p class="privacy-note">本體脂肪率計算僅於瀏覽器端運算，不會傳送或儲存任何輸入資料。如需更多資訊，請參閱<a href="/privacy" rel="nofollow">隱私權政策</a>。</p>
                
            </form>
            <div class="result">
                <h4 class="result-title">你的體脂肪率結果為</h4>
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
                    <span class="percent" aria-hidden="true">%</span>
                </p>
            </div>
        </div>
        <div class="calc-table">
            <h3 class="calc-title">男女體脂率建議值參考</h3>
            {{--<p class="calc-sub">{!! app('cache.config')->get('page_bodyfat_subdesc2_gb') !!}</p>--}}
            <div class="table-scroll">
                <table class="bodyfat-table" width="100%" border="1" cellspacing="0" cellpadding="0" aria-label="男性體脂率建議表">
                    <caption class="visually-hidden">男性體脂率建議值表</caption>
                    <thead>
                        <tr>
                        <th colspan="5" class="table-title male">男性</th>
                        </tr>
                        <tr>
                        <th>年齡</th>
                        <th>消瘦</th>
                        <th>標準</th>
                        <th>微胖</th>
                        <th>肥胖</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <th scope="row">18～39 歲</th>
                        <td>10% 以下</td>
                        <td>11% ~ 21%</td>
                        <td>22% ~ 26%</td>
                        <td>27% 以上</td>
                        </tr>
                        <tr>
                        <th scope="row">40～59 歲</th>
                        <td>11% 以下</td>
                        <td>12% ~ 22%</td>
                        <td>23% ~ 27%</td>
                        <td>28% 以上</td>
                        </tr>
                        <tr>
                        <th scope="row">60 歲以上</th>
                        <td>13% 以下</td>
                        <td>14% ~ 24%</td>
                        <td>25% ~ 29%</td>
                        <td>30% 以上</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="table-scroll">
                <table class="bodyfat-table" width="100%" border="1" cellspacing="0" cellpadding="0" aria-label="女性體脂率建議表">
                    <caption class="visually-hidden">女性體脂率建議值表</caption>
                    <thead>
                        <tr>
                        <th colspan="5" class="table-title female">女性</th>
                        </tr>
                        <tr>
                        <th>年齡</th>
                        <th>消瘦</th>
                        <th>標準</th>
                        <th>微胖</th>
                        <th>肥胖</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <th scope="row">18～39 歲</th>
                        <td>20% 以下</td>
                        <td>21% ~ 34%</td>
                        <td>35% ~ 39%</td>
                        <td>40% 以上</td>
                        </tr>
                        <tr>
                        <th scope="row">40～59 歲</th>
                        <td>21% 以下</td>
                        <td>22% ~ 35%</td>
                        <td>36% ~ 40%</td>
                        <td>41% 以上</td>
                        </tr>
                        <tr>
                        <th scope="row">60 歲以上</th>
                        <td>22% 以下</td>
                        <td>23% ~ 36%</td>
                        <td>37% ~ 41%</td>
                        <td>42% 以上</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="calc-link">想知道自己的身體質量指數BMI嗎？<a class="btn-ef1" href="{{ url('bmi') }}" data-observer="體脂肪-前往BMI" data-track-section="calc.main" data-track-name="calc.cross.bmi">立即計算你的BMI</a></p>
        </div>
    </section>
    <div class="page-news">
        <h2 class="sec-title">體脂肪率知識分享閱讀</h2>
        <div class="news-wrap">
            @foreach($news as $item)
                @include('web.widgets.news-card', ['item' => $item])
            @endforeach
        </div>
    </div>
    
</main>
@include('web.widgets.update-box')

@endsection
