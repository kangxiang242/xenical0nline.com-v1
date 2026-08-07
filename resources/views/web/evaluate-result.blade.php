<div class="figure">
    <div class="col">
        <p class="title">你的身體質量指數(BMI)約為</p>
        <div class="result">
            <p class="value" id="bmi-value">{{ $bmi }}</p>
        </div>
    </div>
    <div class="col">
        <p class="title">你的基礎代謝率(BMR)約為</p>
        <div class="result">
            <p class="value" id="bmr-value">{{ $bmr }}</p>
        </div>
    </div>
    <div class="col">
        <p class="title">你的一天總消耗熱量(TDEE)約為</p>
        <div class="result">
            <p class="value" id="tdee-value">{{ $tdee }}</p>
        </div>
    </div>
</div>

<div class="propose">
    <p class="title">醫師解讀</p>
    <div class="body">
        <div class="explain">
            {!! $inter['text'] !!}
        </div>
        @if($goods)
        <div class="wares">
            <div class="goods">
                <div class="img-wrap">
                    <img src="{{ asset_upload($goods->img) }}" alt="{{ $goods->name }}" loading="lazy" width="240" height="240">
                </div>
                <div class="info">
                    <p class="goods-title">{{ $goods->name }}</p>
                    @if($goods->label)
                        <p class="tags">
                            @foreach(explode('|',$goods->label) as $label)
                                <span>{{ $label }}</span>
                            @endforeach
                        </p>
                    @endif
                    <div class="attrs">
                        @foreach($goods->attr as $attr)
                            <p class="list">
                                <span class="attr-name">{{ $attr->name }}：</span>
                                <span class="attr-value">{{ $attr->value }}</span>
                            </p>
                        @endforeach
                    </div>
                    <div class="price">
                        <span class="now">NT$ {{ $goods->price }}</span>
                        @if($goods->market-$goods->price>0)
                            <span class="discount">優惠NT${{ $goods->market-$goods->price }}</span>
                        @endif
                    </div>
                </div>
                <a class="go-btn" href="{{ url('checkout/'.$goods->id) }}" data-observer="立即購買-推荐" data-track-section="calculator" data-track-name="calc.recommend.buy">立即購買</a>
            </div>
        </div>
        @endif
    </div>
</div>
