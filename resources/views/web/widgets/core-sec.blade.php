@php
    $variant = $variant ?? 'grid';
    $class = $class ?? '';
    $shipTitle = ($period === 'morning') ? '當天出貨' : '現貨供應';
    $items = [
        ['grad' => 1, 'title' => '原廠正品', 'sub' => '歐洲原裝進口<br>原廠正品保證'],
        ['grad' => 2, 'title' => '隱密包裝', 'sub' => '包裹外觀及簡訊<br>絕無敏感字樣'],
        ['grad' => 3, 'title' => $shipTitle, 'sub' => '現貨每日寄出<br>最快隔天到貨'],
        ['grad' => 4, 'title' => '安心訂購', 'sub' => '限時優惠&免運<br>七天鑑賞可退換'],
    ];
    $isHero = $variant === 'hero';
@endphp

<ul class="core-sec{{ $isHero ? ' core-sec--hero' : '' }} {{ $class }}">
    @foreach($items as $item)
        <li class="core-item">
            @if($isHero)
                <div class="core-icon-box">
                    <div class="icon-float">
                        <svg class="core-icon" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><use href="#icon-grad-{{ $item['grad'] }}"/></svg>
                        <div class="icon-bottom"></div>
                    </div>
                </div>
                <h3 class="core-title">{{ $item['title'] }}</h3>
                <p class="core-sub">{!! $item['sub'] !!}</p>
            @else
                <svg class="core-icon" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><use href="#icon-grad-{{ $item['grad'] }}"/></svg>
                <p class="core-title">{{ $item['title'] }}</p>
                <p class="core-sub">{!! $item['sub'] !!}</p>
            @endif
        </li>
    @endforeach
</ul>
