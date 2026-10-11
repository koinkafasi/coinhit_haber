{{-- Ana sayfa "Öne Çıkanlar" ajandası (referans marketAgenda, CH-27).
     2 kolon: sol büyük görsel kart + satır; sağ liste kartları + görsel. --}}
@php
    $lead = $items[0] ?? null;
    $rows = collect($items)->slice(1, 5)->values();
@endphp
<section class="featured-agenda" data-widget-type="marketAgenda" id="home-featured-office">
    <x-section-head :title="__('ui.featured')" :href="\App\Support\Locale::url('/haberler')" />

    @if ($lead)
        <div class="grid grid-cols-1 md:grid-cols-2 md:gap-4">
            <div class="fa-col-left">
                <article class="fa-lead" data-type="news-card-type5">
                    <a href="{{ $lead->url }}" title="{{ $lead->title }}" class="fa-lead-link">
                        <figure class="fa-lead-figure">
                            <div class="fa-lead-img-wrap">
                                <x-lazy-image :src="asset(($lead->image ?? '') ?: '/img/og-default.png')" alt="{{ $lead->title }}" contract="news_card_square" class="fa-lead-img" :eager="false" />
                            </div>
                            <figcaption class="fa-lead-caption">
                                <span class="category text-xs">{{ $lead->category?->name }}</span>
                                <span class="fa-lead-title font-unna font-bold line-clamp-2">{{ $lead->title }}</span>
                            </figcaption>
                        </figure>
                    </a>
                </article>
                @if ($rows->get(0))
                    @php($r0 = $rows->get(0))
                    <article class="fa-row" data-type="news-card-type2">
                        <a href="{{ $r0->url }}" title="{{ $r0->title }}" class="fa-row-link">
                            <span class="fa-row-thumb">
                                <x-lazy-image :src="asset(($r0->image ?? '') ?: '/img/og-default.png')" alt="" contract="news_card_square" class="fa-row-img" />
                            </span>
                            <span class="fa-row-body">
                                <span class="category text-xs">{{ $r0->category?->name }}</span>
                                <span class="fa-row-title font-unna font-bold line-clamp-3">{{ $r0->title }}</span>
                            </span>
                        </a>
                    </article>
                @endif
            </div>

            <div class="fa-col-right">
                @foreach ($rows->slice(1, 2) as $r)
                    <article class="fa-list-item" data-type="news-card-type1">
                        <a href="{{ $r->url }}" title="{{ $r->title }}">
                            <span class="category text-xs">{{ $r->category?->name }}</span>
                            <span class="fa-list-title font-unna font-bold line-clamp-2">{{ $r->title }}</span>
                        </a>
                    </article>
                @endforeach
                @if ($rows->get(3))
                    @php($r3 = $rows->get(3))
                    <article class="fa-lead fa-lead-small" data-type="news-card-type5">
                        <a href="{{ $r3->url }}" title="{{ $r3->title }}" class="fa-lead-link">
                            <figure class="fa-lead-figure">
                                <div class="fa-lead-img-wrap">
                                    <x-lazy-image :src="asset(($r3->image ?? '') ?: '/img/og-default.png')" alt="" contract="news_card_square" class="fa-lead-img" />
                                </div>
                                <figcaption class="fa-lead-caption">
                                    <span class="category text-xs">{{ $r3->category?->name }}</span>
                                    <span class="fa-lead-title fa-lead-title-sm font-unna font-bold line-clamp-2">{{ $r3->title }}</span>
                                </figcaption>
                            </figure>
                        </a>
                    </article>
                @endif
            </div>
        </div>
    @endif
</section>
