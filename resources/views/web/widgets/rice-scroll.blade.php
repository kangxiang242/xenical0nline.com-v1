@php
    $riceScrollConfig = json_decode(\App\Services\ConfigService::get('home_rice_scroll', ''), true);
@endphp

@if(!empty($riceScrollConfig))
<ul class="rice-scroll">
    @foreach($riceScrollConfig as $item)
    <li class="rice-item"><p class="rice-text"><span class="big-text">{{ $item['title'] }}</span>{{ $item['subtitle'] }}</p></li>
    @endforeach
</ul>
@endif

@push('rice-scroll')
<script>
(function() {
    /* 跑馬燈一圈時間（秒）；數字越小越快。與 _index.scss 預設 40s 對齊，必要時再覆寫 inline */
    var RICE_SCROLL_DURATION = 40;

    $(document).ready(function() {
        $('.rice-scroll').each(function() {
            var $riceScroll = $(this);
            if ($riceScroll.attr('data-rice-enhanced') === '1') {
                return;
            }
            var $riceItems = $riceScroll.children('.rice-item');
            if ($riceItems.length === 0) {
                return;
            }

            $riceScroll.attr('data-rice-enhanced', '1');

            var itemsHtml = [];
            $riceItems.each(function() {
                itemsHtml.push($(this)[0].outerHTML);
            });

            var totalItems = itemsHtml.length;
            var parentWidth = $riceScroll.parent().outerWidth() || window.innerWidth;
            var baseWidth = 0;
            $riceItems.each(function() {
                baseWidth += $(this).outerWidth(true);
            });
            var copies = 2;
            if (baseWidth > 0) {
                copies = Math.max(2, Math.ceil((parentWidth * 2) / baseWidth) + 1);
            }
            $riceScroll.empty();

            for (var i = 0; i < copies; i++) {
                for (var j = 0; j < totalItems; j++) {
                    $riceScroll.append(itemsHtml[j]);
                }
            }

            $riceScroll.css('animation-duration', RICE_SCROLL_DURATION + 's');
        });
    });
})();
</script>
@endpush
