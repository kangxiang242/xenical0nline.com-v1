<footer class="main-footer">
    <a class="footer-logo" href="/">
        <img class="footer-logo-img" src="/static/img/logo-header.svg" alt="Xenical 羅氏鮮" width="200" height="50" decoding="async" fetchpriority="high" />
    </a>
    <nav class="footer-menu">
        <section class="menu">
            <p class="en-title">Product</p>
            <h2 class="footer-title">減肥藥產品</h2>
            <ul class="nav">
                <li><a href="{{ url('about') }}" data-observer="頁腳-羅氏鮮介紹" data-track-section="footer" data-track-name="footer.about">減肥藥羅氏鮮介紹</a></li>
                <li><a href="{{ url('product') }}" data-observer="頁腳-線上訂購" data-track-section="footer" data-track-name="footer.product">羅氏鮮線上訂購</a></li>
            </ul>
        </section>
        <section class="menu">
            <p class="en-title">Slimming</p>
            <h2 class="footer-title">瘦身資訊</h2>
            <ul class="nav">
                <li><a href="{{ url('bmi') }}" data-observer="頁腳-BMI" data-track-section="footer" data-track-name="footer.bmi">BMI計算</a></li>
                <li><a href="{{ url('bmr') }}" data-observer="頁腳-BMR" data-track-section="footer" data-track-name="footer.bmr">BMR計算</a></li>
                <li><a href="{{ url('body-fat') }}" data-observer="頁腳-體脂肪" data-track-section="footer" data-track-name="footer.body_fat">體脂肪率計算</a></li>
                <li><a href="{{ url('news') }}" data-observer="頁腳-專欄" data-track-section="footer" data-track-name="footer.news">減肥瘦身專欄</a></li>
            </ul>
        </section>
        <section class="menu">
            <p class="en-title">Service</p>
            <h2 class="footer-title">購物服務</h2>
            <ul class="nav">
                <li><a href="{{ url('guide') }}" data-observer="頁腳-購前須知" data-track-section="footer" data-track-name="footer.guide">購前須知</a></li>
                <li><a href="{{ url('payment-delivery') }}" data-observer="頁腳-付款配送" data-track-section="footer" data-track-name="footer.payment">付款與配送</a></li>
                <li><a href="{{ url('after-sales') }}" data-observer="頁腳-售後" data-track-section="footer" data-track-name="footer.after_sales">售後服務</a></li>
                <li><a href="{{ url('check') }}" data-observer="頁腳-訂單追蹤" data-track-section="footer" data-track-name="footer.check">訂單追蹤</a></li>
                <li><a href="{{ url('message') }}" data-observer="頁腳-取得協助" data-track-section="footer" data-track-name="footer.message">取得協助</a></li>
                <li><a href="{{ url('privacy') }}" data-observer="頁腳-隱私" data-track-section="footer" data-track-name="footer.privacy">隱私權政策</a></li>
            </ul>
        </section>
    </nav>
    <div class="partner">
        <img src="{{ asset('static/img/Roche.svg') }}" alt="Roche">
        <img src="{{ asset('static/img/FDA.svg') }}" alt="FDA">
        <img src="{{ asset('static/img/EMA.svg') }}" alt="EMA">
        <img src="{{ asset('static/img/cheplapharm.svg') }}" alt="cheplapharm">
    </div>
    <p class="copyright">{!! app('cache.config')->get('copyright') !!}</p>

</footer>
