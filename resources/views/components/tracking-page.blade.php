@php
    $path = trim(request()->path(), '/');
    $segments = $path === '' ? [] : explode('/', $path);
    $trackingPage = ['page_type' => 'other', 'version' => config('app.asset_version')];

    if ($path === '') {
        $trackingPage = ['page_type' => 'home'];
    } elseif ($path === 'product') {
        $trackingPage = ['page_type' => 'product_list'];
    } elseif (isset($segments[0]) && $segments[0] === 'product' && isset($segments[1]) && is_numeric($segments[1])) {
        $trackingPage = ['page_type' => 'product_detail', 'goods_id' => $segments[1]];
    } elseif (isset($segments[0]) && $segments[0] === 'checkout' && isset($segments[1]) && is_numeric($segments[1])) {
        $trackingPage = ['page_type' => 'checkout', 'goods_id' => $segments[1]];
    } elseif ($path === 'compute') {
        $trackingPage = ['page_type' => 'bmi', 'calc_type' => 'bmi'];
    } elseif ($path === 'news') {
        $trackingPage = ['page_type' => 'news_list'];
    } elseif (isset($segments[0]) && $segments[0] === 'news' && isset($segments[1])) {
        $trackingPage = ['page_type' => 'news_detail', 'article_id' => $segments[1]];
    } elseif (in_array($path, ['about', 'guide', 'payment-delivery', 'after-sales'], true)) {
        $trackingPage = ['page_type' => 'cms', 'cms_uri' => $path];
    } elseif ($path === 'faq') {
        $trackingPage = ['page_type' => 'faq'];
    } elseif ($path === 'message') {
        $trackingPage = ['page_type' => 'message'];
    } elseif ($path === 'check') {
        $trackingPage = ['page_type' => 'order_check'];
    } elseif (isset($segments[0]) && $segments[0] === 'check' && isset($segments[1])) {
        $trackingPage = ['page_type' => 'order_success'];
    }
@endphp
<script>
    window.__TRACKING_PAGE__ = @json($trackingPage);
</script>
