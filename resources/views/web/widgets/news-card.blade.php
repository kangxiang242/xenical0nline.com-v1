
@php
    $__titleTag = $titleTag ?? 'h3';
    $__rootTag = $rootTag ?? 'article';
    $__extra = isset($rootClass) && $rootClass !== '' ? trim((string) $rootClass) : '';
    $__rootClass = trim('news-card' . ($__extra !== '' ? ' ' . $__extra : ''));
    $__href = ($item->cate && $item->cate->uri)
        ? route('news.show', [$item->cate->uri, $item->id])
        : url('news/'.$item->id);
    $__desc = \Illuminate\Support\Str::limit(
        strip_tags($item->brief ?: $item->content),
        60
    );
@endphp
<{{ $__rootTag }} class="{{ $__rootClass }}">
    <div class="card-img">
        @if($item->cate)
            <p class="card-badge">{{ $item->cate->name }}</p>
        @endif
        @if($item->img)
            <img
                src="{{ asset_upload($item->img) }}"
                sizes="(max-width: 768px) 100%, 800px"
                width="800"
                height="400"
                decoding="async"
                loading="lazy"
                fetchpriority="low"
                alt="{{ $item->img_alt ?? $item->title }}">
        @else
            <div class="card-img-placeholder" aria-hidden="true"></div>
        @endif
    </div>
    <div class="card-text">
        <{{ $__titleTag }} class="card-title">{{ $item->title }}</{{ $__titleTag }}>
        <p class="card-description">{{ $__desc }}</p>
        <div class="more-box">
            <div class="views">
                <svg class="viewicon" viewBox="0 0 1024 1024"><use href="#icon-viewicon"></use></svg>
                {{ (int) ($item->read_num ?? 0) }}
            </div>
            <a href="{{ $__href }}" class="morebtn" data-observer="閱讀-{{ $item->title }}" data-track-section="news.card" data-track-name="news.card.read">閱讀全文<svg class="arrowicon" viewBox="0 0 1024 1024"><use href="#icon-arrowicon"></use></svg></a>
        </div>
    </div>
</{{ $__rootTag }}>
