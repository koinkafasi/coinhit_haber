{{--
    Tek sütun haber listesi (spec §8 /haberler, /tumhaberler, kanal sayfaları, arama, etiket).
    Kare thumb + kategori etiketi + başlık + özet/lede + "X önce" rozeti.
--}}
@props([
    'items',
    'showSpot' => true,
    'showAge' => false,
    'leadFirst' => false,
])

<div class="news-list" data-load-more-container>
    @forelse ($items as $item)
        <article class="news-list-item {{ $leadFirst && $loop->first ? 'is-lead' : '' }}" data-type="news-list">
            <a href="{{ $item->url }}" class="news-list-link">
                <figure class="news-list-figure">
                    <x-lazy-image :src="asset($item->image ?: '/img/og-default.png')" alt=""
                                  contract="news_card_square" class="news-list-img news-card-img" />
                </figure>
                <div class="news-list-body">
                    @if ($item->category?->name)
                        <div class="category text-xs">{{ $item->category->name }}</div>
                    @endif
                    <h3 class="news-list-title font-unna font-bold line-clamp-3">{{ $item->title }}</h3>
                    @if ($showSpot && $item->spot)
                        <p class="news-list-spot line-clamp-3">{{ \Illuminate\Support\Str::limit(strip_tags($item->spot), 200) }}</p>
                    @endif
                    @if ($showAge && $item->published_at)
                        @php
                            $minutes = max(0, (int) $item->published_at->diffInMinutes(now()));
                            $age = $minutes < 60
                                ? $minutes.' '.__('ui.minutes_ago')
                                : ($minutes < 1440
                                    ? intdiv($minutes, 60).' '.__('ui.hours_ago')
                                    : intdiv($minutes, 1440).' '.__('ui.days_ago'));
                        @endphp
                        <span class="news-age">- {{ $age }}</span>
                    @endif
                </div>
            </a>
        </article>
    @empty
        <p class="search-empty">{{ __('ui.no_news') }}</p>
    @endforelse
</div>