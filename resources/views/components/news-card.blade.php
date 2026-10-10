@props([
    'news',
    'type' => 'type1',
    'eager' => false,
    'showSpot' => false,
    'contract' => 'news_card',
])

@php
    $category = $news->category?->name;
@endphp

<div class="news-card-item news-card-{{ $type }}" data-type="news-card-{{ $type }}" data-news-id="{{ $news->news_id }}">
    <a href="{{ $news->url }}" class="news-card-link">
        @if ($type !== 'type1')
            <figure class="news-card-figure">
                <x-lazy-image :src="asset($news->image ?: '/img/og-default.png')" alt="" :contract="$contract" class="news-card-img" :eager="$eager" />
            </figure>
        @endif
        <figcaption class="news-card-caption">
            @if ($category)
                <div class="category text-xs">{{ $category }}</div>
            @endif
            <div class="font-unna font-bold news-card-title line-clamp-2">{{ $news->title }}</div>
            @if ($showSpot && $news->spot)
                <div class="news-card-spot text-base line-clamp-3">{{ \Illuminate\Support\Str::limit(strip_tags($news->spot), 170) }}</div>
            @endif
        </figcaption>
    </a>
</div>