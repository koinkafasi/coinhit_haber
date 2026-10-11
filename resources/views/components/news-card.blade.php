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
    <a href="{{ $news->url }}" title="{{ $news->title }}" class="{{ $type === 'type5' ? 'block' : '' }} news-card-link">
        @if ($type === 'type5')
            <figure class="relative w-full border-b mb-2 pb-2 news-card-figure">
                <div class="w-full aspect-square bg-gray-50">
                    <x-lazy-image :src="asset($news->image ?: '/img/og-default.png')" alt="" :contract="$contract" class="w-full h-full news-card-img" :eager="$eager" />
                </div>
                <figcaption class="relative w-11/12 h-auto -mt-12 p-4 pl-0 md:min-h-30 bg-white news-card-caption">
            @if ($category)
                <div class="category text-xs">{{ $category }}</div>
            @endif
                    <div class="font-unna font-bold text-xl 2xl:text-2xl text-ellipsis mb-2 md:min-h-[64px] news-card-title">
                        <span class="line-clamp-2">{{ $news->title }}</span>
                    </div>
                    @if ($showSpot && $news->spot)
                        <div class="news-card-spot text-base line-clamp-3">{{ \Illuminate\Support\Str::limit(strip_tags($news->spot), 170) }}</div>
                    @endif
                </figcaption>
            </figure>
        @elseif ($type === 'type2')
            <figure class="flex gap-2 news-card-figure">
                <div class="w-4/12 h-full aspect-square bg-gray-50 max-w-40">
                    <x-lazy-image :src="asset($news->image ?: '/img/og-default.png')" alt="" :contract="$contract" class="w-full h-full object-fill news-card-img" :eager="$eager" />
                </div>
                <figcaption class="w-8/12 flex flex-col justify-center space-y-2 news-card-caption">
                    @if ($category)
                        <div class="category text-xs">{{ $category }}</div>
                    @endif
                    <div class="font-unna font-bold text-base 2xl:text-xl text-ellipsis line-clamp-3 news-card-title">{{ $news->title }}</div>
                    @if ($showSpot && $news->spot)
                        <div class="news-card-spot text-base line-clamp-3">{{ \Illuminate\Support\Str::limit(strip_tags($news->spot), 170) }}</div>
                    @endif
                </figcaption>
            </figure>
        @elseif ($type === 'type3')
            {{-- Referans type3 (highlights/graphics): aspect-video kapak + baslik. --}}
            <figure class="px-2">
                <div class="w-full h-full aspect-video bg-gray-50 mb-4">
                    <x-lazy-image :src="asset($news->image ?: '/img/og-default.png')" alt="" :contract="$contract" class="w-full h-full news-card-img" :eager="$eager" />
                </div>
                <figcaption class="w-full h-full flex flex-col justify-around">
                    @if ($category)
                        <div class="category text-xs">{{ $category }}</div>
                    @endif
                    <div class="font-unna font-bold text-base 2xl:text-xl text-ellipsis line-clamp-2 news-card-title">{{ $news->title }}</div>
                    @if ($showSpot && $news->spot)
                        <div class="news-card-spot text-base line-clamp-3">{{ \Illuminate\Support\Str::limit(strip_tags($news->spot), 170) }}</div>
                    @endif
                </figcaption>
            </figure>
        @else
            <figure class="px-2">
                <figcaption class="news-card-caption">
                    @if ($category)
                        <div class="category text-xs">{{ $category }}</div>
                    @endif
                    <div class="font-unna font-bold text-base 2xl:text-xl text-ellipsis line-clamp-2 news-card-title">{{ $news->title }}</div>
                    @if ($showSpot && $news->spot)
                        <div class="news-card-spot text-base line-clamp-3">{{ \Illuminate\Support\Str::limit(strip_tags($news->spot), 170) }}</div>
                    @endif
                </figcaption>
            </figure>
        @endif
    </a>
</div>