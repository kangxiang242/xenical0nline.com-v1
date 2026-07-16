@extends('web::layout.layout')

@section('style')
    @parent
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('static/less/bmr.css') }}?ver={{ config('app.asset_version') }}"/> -->
    @if(!empty($css))
    <style type="text/css">
        {!! $css !!}
    </style>
    @endif

@stop

@section('script')
    <script>
        function animateDigit(el, num, alwaysSpin = false) {
            const inner = el.querySelector('.digit-inner');
            if (!inner) {
                return;
            }
            const firstSpan = inner.querySelector('span');
            const targetIndex = num === '?' ? 0 : (parseInt(num, 10) + 1);
            let currentIndex = 0;
            const currentTransform = inner.style.transform || 'translateY(0)';
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
            const totalIndex = targetIndex + (alwaysSpin ? 11 : 0);
            inner.style.transition = 'none';
            inner.style.transform = 'translateY(0)';
            void inner.offsetWidth;
            inner.style.transition = 'transform 1s ease-out';
            inner.style.transform = 'translateY(-' + totalIndex + 'em)';
        }

        function animateBMRDisplay(bmr) {
            const fixedBMR = bmr.toFixed(1).padStart(5, '0');
            const [intPartRaw, decPartRaw] = fixedBMR.split('.');
            const intPart = intPartRaw.padStart(4, '0');
            const decPart = decPartRaw ? decPartRaw[0] : '0';

            animateDigit(document.getElementById('int1'), intPart[0]);
            animateDigit(document.getElementById('int2'), intPart[1]);
            animateDigit(document.getElementById('int3'), intPart[2]);
            animateDigit(document.getElementById('int4'), intPart[3]);
            animateDigit(document.getElementById('dec1'), decPart);
        }

        function updateTDEEValues(bmr) {
            document.querySelectorAll('.tdee-item').forEach(item => {
            const formulaEl = item.querySelector('.tdee-formula');
            const resultWrapper = item.querySelector('.tdee-result');
            const resultNum = item.querySelector('.tdee-num');

            if (!formulaEl || !resultWrapper || !resultNum) return;

            const match = formulaEl.textContent.match(/×\s*([\d.]+)/);
            if (!match) return;

            const factor = parseFloat(match[1]);
            if (isNaN(factor)) return;

            const tdee = (bmr * factor).toFixed(1);
            resultNum.textContent = tdee;
            resultWrapper.classList.add('show');
            });
        }

        window.addEventListener('DOMContentLoaded', () => {
            
            ['int1', 'int2', 'int3', 'int4', 'dec1'].forEach(id => {
            animateDigit(document.getElementById(id), '?');
            });

            
            const genderRadios = document.querySelectorAll('.gender-toggle input[type="radio"]');
            const activeBg = document.querySelector('.gender-toggle .active-bg');

            genderRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                const index = Array.from(genderRadios).indexOf(radio);
                activeBg.style.transform = `translateX(${index * 100}%)`;
            });
            });

            const initialChecked = document.querySelector('.gender-toggle input[type="radio"]:checked');
            if (initialChecked) {
            const index = Array.from(genderRadios).indexOf(initialChecked);
            activeBg.style.transform = `translateX(${index * 100}%)`;
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelector('.count').addEventListener('click', () => {
            const gender = document.querySelector('input[name="gender"]:checked')?.value;
            const age = parseFloat(document.getElementById('age').value);
            const height = parseFloat(document.getElementById('height').value);
            const weight = parseFloat(document.getElementById('weight').value);

            if (!gender || !age || !height || !weight || age <= 0 || height <= 0 || weight <= 0) {
                alert("請正確輸入所有欄位");
                return;
            }

            const sexValue = gender === 'male' ? 1 : 0;
            const bmr = 9.99 * weight + 6.25 * height - 4.92 * age + (166 * sexValue - 161);

            animateBMRDisplay(bmr);
            updateTDEEValues(bmr);
            });

            document.querySelector('.reset').addEventListener('click', () => {
            // 清空欄位
            ['age', 'height', 'weight'].forEach(id => document.getElementById(id).value = '');
            // 重設動畫
            ['int1', 'int2', 'int3', 'int4', 'dec1'].forEach(id => {
                animateDigit(document.getElementById(id), '?');
            });
            // 清除 TDEE 顯示
            document.querySelectorAll('.tdee-result').forEach(el => {
                el.classList.remove('show');
                const numEl = el.querySelector('.tdee-num');
                if (numEl) numEl.textContent = '';
            });
            });
        });
    </script>

@stop

@section('content')
<main>
    @include('web.widgets.head-banner')
    @include('web.widgets.breadcrumb', ['itemsHtml' => '<li class="breadcrumb__item">BMR計算</li>'])
    {{--
    <section class="editor">
        {!! app('cache.config')->get('page_compute_article') !!}
    </section>
    --}}
    <section class="calc-wrapper" data-track-section-view data-track-section="calc.main" data-track-section-label="BMR 計算器">
        <h2 class="sr-only">立即計算你的BMR指數及TDEE</h2>
        <div class="calculate">
            <h3 class="calc-title">BMR計算</h3>
            <p class="calc-sub">僅需輸入身高、體重、年齡及生理性別，即可快速計算出BMR指數及TDEE，並參考TDEE建議值參照表，了解自己的身體狀況。</p>

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
                <p class="privacy-note">本BMR計算僅於瀏覽器端運算，不會傳送或儲存任何輸入資料。如需更多資訊，請參閱<a href="/privacy" rel="nofollow">隱私權政策</a>。</p>
            </form>
            <div class="result">
                <h4 class="result-title">你的BMR結果為</h4>
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
                    <span class="digit" id="int3" aria-hidden="true">
                        <span class="digit-inner">
                            <span>?</span><span>0</span><span>1</span><span>2</span><span>3</span><span>4</span><span>5</span><span>6</span><span>7</span><span>8</span><span>9</span>
                        </span>
                    </span>
                    <span class="digit" id="int4" aria-hidden="true">
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
            <h3 class="calc-title">你的每日TDEE（總熱量消耗）</h3>
            {{--<p class="calc-sub">{!! app('cache.config')->get('page_compute_tdee_sub_gb') !!}</p>--}}
            <ul class="tdee-list">
                <li class="tdee-item">
                    <figure class="tdee-item-wrap">
                        <img src="/static/img/lv1.webp" alt="TDEE靜態活動等級">
                        <figcaption class="tdee-item-info">
                            <p class="tdee-item-name"><strong class="tdee-item-title">身體活動趨於靜態</strong>（幾乎沒有在運動）</p>
                            <p class="tdee-item-sub">坐式生活型態，如：靜臥、久坐、看電視</p>
                            <p class="tdee-formula">TDEE = BMR × 1.2</p>
                            <p class="tdee-result">TDEE = <strong class="tdee-num"></strong></p>
                        </figcaption>
                    </figure>
                </li>

                <li class="tdee-item">
                    <figure class="tdee-item-wrap">
                        <img src="/static/img/lv2.webp" alt="TDEE較低活動等級">
                        <figcaption class="tdee-item-info">
                            <p class="tdee-item-name"><strong class="tdee-item-title">身體活動程度較低</strong>（每週有運動 1~3 天）</p>
                            <p class="tdee-item-sub">不太費力的基本活動，如：開車、烹飪、散步</p>
                            <p class="tdee-formula">TDEE = BMR × 1.375</p>
                            <p class="tdee-result">TDEE = <strong class="tdee-num"></strong></p>
                        </figcaption>
                    </figure>
                </li>

                <li class="tdee-item">
                    <figure class="tdee-item-wrap">
                        <img src="/static/img/lv3.webp" alt="TDEE中等活動等級">
                        <figcaption class="tdee-item-info">
                        <p class="tdee-item-name"><strong class="tdee-item-title">身體活動程度正常</strong>（每週有運動 3~5 天）</p>
                        <p class="tdee-item-sub">呼吸及心跳些微加快的活動，如：掃地、拖地、逛街、健走</p>
                        <p class="tdee-formula">TDEE = BMR × 1.55</p>
                        <p class="tdee-result">TDEE = <strong class="tdee-num"></strong></p>
                        </figcaption>
                    </figure>
                </li>

                <li class="tdee-item">
                    <figure class="tdee-item-wrap">
                        <img src="/static/img/lv4.webp" alt="TDEE較高活動等級">
                        <figcaption class="tdee-item-info">
                            <p class="tdee-item-name"><strong class="tdee-item-title">身體活動程度較高</strong>（每週有運動 6~7 天）</p>
                            <p class="tdee-item-sub">呼吸及心跳快速且大量流汗的活動，如：打球、騎腳踏車、有氧運動、游泳、登山</p>
                            <p class="tdee-formula">TDEE = BMR × 1.72</p>
                            <p class="tdee-result">TDEE = <strong class="tdee-num"></strong></p>
                        </figcaption>
                    </figure>
                </li>

                <li class="tdee-item">
                    <figure class="tdee-item-wrap">
                        <img src="/static/img/lv5.webp" alt="TDEE激烈活動等級">
                        <figcaption class="tdee-item-info">
                            <p class="tdee-item-name"><strong class="tdee-item-title">身體活動程度激烈</strong>(長時間運動或體力勞動工作)</p>
                            <p class="tdee-item-sub">長時間耗費體力的活動，如：長跑、運動訓練、競賽型運動</p>
                            <p class="tdee-formula">TDEE = BMR × 1.9</p>
                            <p class="tdee-result">TDEE = <strong class="tdee-num"></strong></p>
                        </figcaption>
                    </figure>
                </li>
            </ul>
            <p class="calc-link">想知道自己的體脂肪率嗎？<a class="btn-ef1" href="{{ url('body-fat') }}" data-observer="BMR-前往體脂肪" data-track-section="calc.main" data-track-name="calc.cross.body_fat">立即計算你的體脂肪率</a></p>
        </div>
    </section>
    <section class="page-news">
        <h2 class="sec-title">BMR知識延伸閱讀</h2>
        <div class="news-wrap">
            @foreach($news as $item)
                @include('web.widgets.news-card', ['item' => $item])
            @endforeach
        </div>
    </section>
    
</main>
@include('web.widgets.update-box')
@endsection