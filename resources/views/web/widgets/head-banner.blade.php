@php
    // 随机获取背景图片 - 从global_banners池
    $bannerImages = json_decode(\App\Services\ConfigService::get('global_banners', '[]'), true);
    $randomBg = !empty($bannerImages) ? asset_upload($bannerImages[array_rand($bannerImages)]) : '';
    $bannerStyle = $randomBg ? "background-image: url('{$randomBg}');" : '';

    // 根据当前路径获取对应的标题和描述
    $path = request()->path();
    $pathMap = [
        '' => ['title' => 'home_about_title', 'desc' => 'home_about'],
        'product' => ['title' => 'page_product_title', 'desc' => 'page_product_desc'],
        'news' => ['title' => 'page_news_title', 'desc' => 'page_news_desc'],
        'check' => ['title' => 'page_check_title', 'desc' => 'page_check_desc'],
        'message' => ['title' => 'page_message_title', 'desc' => 'page_message_desc'],
        'about' => ['title' => 'about_title', 'desc' => ''],
        'guide' => ['title' => 'notes_buy_title', 'desc' => ''],
        'faq' => ['title' => 'page_faq_title', 'desc' => ''],
        'bmi' => ['title' => 'page_evaluate_title', 'desc' => 'page_evaluate_desc'],
        'body-fat' => ['title' => 'page_body_fat_title', 'desc' => 'page_body_fat_desc'],
        'bmr' => ['title' => 'page_compute_title', 'desc' => 'page_compute_desc'],
        'payment-delivery' => ['title' => 'page_payment_title', 'desc' => ''],
        'after-sales' => ['title' => 'page_after_sales_title', 'desc' => ''],
        'privacy' => ['title' => 'page_privacy_title', 'desc' => ''],
    ];

    $config = $pathMap[$path] ?? $pathMap[''];
    $title = \App\Services\ConfigService::get($config['title'], '專業醫藥服務');
    $descHtml = '';
    if (!empty($config['desc'])) {
        $desc = \App\Services\ConfigService::get($config['desc']);
        if ($desc) {
            /*
             * 後台富文本：<p>…</p> 只補 class；無 <p> 且含 <br> 時依換行拆成多個 <p class="head-desc">；
             * 其餘整段包一個 <p class="head-desc">。
             */
            if (stripos($desc, '<p') !== false) {
                $descHtml = preg_replace('/<p(\s[^>]*)?>/iu', '<p class="head-desc"$1>', $desc);
                if ($descHtml === null) {
                    $descHtml = $desc;
                }
            } elseif (preg_match('#<br\s*/?>#i', $desc)) {
                $parts = preg_split('#<br\s*/?>#i', $desc);
                $blocks = [];
                foreach ($parts as $part) {
                    $part = trim($part);
                    if ($part !== '') {
                        $blocks[] = '<p class="head-desc">' . $part . '</p>';
                    }
                }
                $descHtml = $blocks !== [] ? implode('', $blocks) : '<p class="head-desc">' . trim($desc) . '</p>';
            } else {
                $descHtml = '<p class="head-desc">' . $desc . '</p>';
            }
        }
    }
@endphp
    <section class="head-banner" {!! $bannerStyle ? 'style="' . $bannerStyle . '"' : '' !!}>
    <h1 class="head-title">{!! $title !!}</h1>
    {!! $descHtml !!}
</section>
