{{--
    Trend haber listesi öğesi (spec §8 /trendler): kategori etiketi + başlık + 1 satır özet + thumbnail.
--}}
@props(['item'])

<article class="trend-news-item news-list-item" data-type="news-list">
    <a href="{{ $item->url }}" class="trend-news-link">
        <figure class="trend-news-figure">
            <x-lazy-image :src="asset($item->image ?: '/img/og-default.png')" alt=""
                          contract="news_card_square" class="trend-news-img news-card-img" />
        </figure>
        <div class="trend-news-body">
            @if ($item->category?->name)
                <div class="category text-xs">{{ $item->category->name }}</div>
            @endif
            <h3 class="trend-news-title font-unna font-bold line-clamp-2">{{ $item->title }}</h3>
            @if ($item->spot)
                <p class="trend-news-spot line-clamp-1">{{ \Illuminate\Support\Str::limit(strip_tags($item->spot), 160) }}</p>
            @endif
        </div>
    </a>
</article>