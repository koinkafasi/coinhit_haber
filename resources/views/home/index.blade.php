@extends('front.layouts.app')

@section('title', ($siteSettings['site_name'] ?? 'Bloomberg HT').' | '.__('ui.site_tagline'))

@section('masthead')
    {{-- Modül 1: Masthead ad — zone_1264 (masaüstü) / zone_1394 (mobil), header ÜSTÜNDE --}}
    <div class="masthead-ad">
        <div class="ad-head"><h2 class="ad-label !text-xxs">{{ __('ui.ad') }}</h2></div>
        <div class="ad-body ad-body-desktop" data-device="DESKTOP">
            <div class="ads-zone lazy-init" data-adv="bht" data-device="DESKTOP" data-zone-id="zone_1264" id="zone_1264"></div>
        </div>
        <div class="ad-body ad-body-mobile" data-device="MOBILE">
            <div class="ads-zone lazy-init" data-adv="bht" data-device="MOBILE" data-zone-id="zone_1394" id="zone_1394" aria-hidden="true"></div>
        </div>
    </div>
@endsection

@section('content')
<div class="home-page">

    {{-- Modül 2: Hero / main-slider (grid-cols-12 swiper, dataType=headline) --}}
    <section class="hero-main-slider" data-widget-type="headline">
        <div class="main-slider slider-custom" data-hero-slider>
            <div class="swiper-wrapper hero-slides" data-hero-track>
                @foreach ($hero as $index => $item)
                    <article class="swiper-slide hero-slide grid-cols-12" data-hash="haber-{{ $item->news_id }}" data-type="headline">
                        <a href="{{ $item->url }}" class="hero-slide-link">
                            <figure class="aspect-video bg-gray-50 hero-slide-figure">
                                <x-lazy-image :src="asset($item->image ?: '/img/og-default.png')" alt="" contract="news_card" class="hero-slide-img" :eager="$index === 0" />
                            </figure>
                            <figcaption class="hero-slide-caption">
                                <div class="category text-xs">{{ $item->category?->name }}</div>
                                <h2 class="font-unna font-bold hero-slide-title line-clamp-2">{{ $item->title }}</h2>
                                @if ($item->spot)
                                    <p class="hero-slide-spot line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($item->spot), 160) }}</p>
                                @endif
                            </figcaption>
                        </a>
                    </article>
                @endforeach
            </div>
            <button type="button" class="main-slider-nav main-slider-prev" data-hero-prev aria-label="{{ __('ui.previous') }}">
                <span class="bloomberght-icon-font-icon-chevron-left" aria-hidden="true">&lsaquo;</span>
            </button>
            <button type="button" class="main-slider-nav main-slider-next" data-hero-next aria-label="{{ __('ui.next') }}">
                <span class="bloomberght-icon-font-icon-chevron-right" aria-hidden="true">&rsaquo;</span>
            </button>
            <div class="main-slider-pagination type4" data-hero-pagination></div>
        </div>
    </section>

    {{-- Modül 3: finance-slider mobil şerit (md:hidden, border-t) --}}
    <div class="finance-strip-mobile md:hidden" data-type="finance-strip" aria-label="{{ __('ui.market_strip') }}">
        <div class="finance-strip-track">
            @foreach ($financeGroups as $group)
                @foreach ($group['quotes'] as $quote)
                    <a class="finance-strip-item" href="{{ $quote->instrument->url }}" data-market-code="{{ $quote->code() }}">
                        <span class="fs-name text-xs">{{ $quote->instrument->name }}</span>
                        <span class="lastPrice font-bold {{ $quote->direction() === 'up' ? 'text-green-700' : ($quote->direction() === 'down' ? 'text-red-700' : 'text-gray-400') }}" data-market-field="last">{{ $quote->formattedLast() }}</span>
                    </a>
                @endforeach
            @endforeach
        </div>
    </div>

    {{-- Modül 5: ÖNE ÇIKANLAR — yatay haber başlık şeridi --}}
    <section class="home-featured-strip" id="one-cikanlar">
        <x-section-head :title="__('ui.featured')" :href="null" />
        <div class="featured-strip" data-horizontal-strip>
            @foreach ($featuredStrip as $item)
                <a class="featured-strip-item" href="{{ $item->url }}">
                    <span class="category text-xs">{{ $item->category?->name }}</span>
                    <span class="featured-strip-title font-unna font-bold line-clamp-2">{{ $item->title }}</span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Modül 6: REKLAM --}}
    <x-ad-zone :zone="1257" :index="1" />

    {{-- Modül 7: PİYASALAR büyük widget --}}
    @include('home.partials.market-widget')

    {{-- Modül 8: PROGRAMLAR --}}
    <section class="home-programs">
        <x-section-head :title="__('ui.programs')" :href="\App\Support\Locale::url('/video')" />
        <div class="program-list">
            @foreach ($programs as $episode)
                <article class="program-card">
                    <a href="{{ $episode->program?->url ?? \App\Support\Locale::url('/video') }}">
                        <figure class="program-thumb">
                            <x-lazy-image :src="asset($episode->thumbnail ?: $episode->program?->thumbnail ?: '/img/og-default.png')" alt="" contract="video_thumb" class="img-contract" />
                            <span class="program-duration">{{ $episode->duration }}</span>
                        </figure>
                        <div class="program-body">
                            <h3 class="program-title font-unna font-bold line-clamp-2">{{ $episode->display_title }}</h3>
                            <time class="program-date">{{ $episode->air_at?->format('d.m.Y H:i') }}</time>
                        </div>
                    </a>
                </article>
            @endforeach
        </div>
    </section>

    {{-- Modül 9: REKLAM x2 --}}
    <x-ad-zone :zone="2167" :index="2" />
    <x-ad-zone :zone="1265" :index="3" />

    {{-- Modül 10: ÖNE ÇIKANLAR — kategori etiketli haber kartları (global widget) --}}
    <x-featured-widget id="home-featured-cards"
                       :title="__('ui.featured')"
                       :href="\App\Support\Locale::url('/haberler')"
                       :items="$featuredCards"
                       :columns="3"
                       :showSpot="true" />

    {{-- Modül 11: TÜM HABERLER --}}
    <section class="home-all-news">
        <x-section-head :title="__('ui.all_news')" :href="\App\Support\Locale::url('/tumhaberler')" />
        <ol class="headline-list">
            @foreach ($allNews as $item)
                <li>
                    <a href="{{ $item->url }}">
                        <span class="headline-cat">{{ $item->category?->name }}</span>
                        <span class="headline-title font-unna font-bold">{{ $item->title }}</span>
                        <time class="headline-time">{{ $item->published_at?->format('H:i') }}</time>
                    </a>
                </li>
            @endforeach
        </ol>
    </section>

    {{-- Modül 12: YAYIN AKIŞI --}}
    <section class="home-broadcast">
        <x-section-head :title="__('ui.broadcast')" :href="\App\Support\Locale::url('/yayinakisi')" />
        <ol class="broadcast-list">
            @foreach ($broadcast as $row)
                <li>
                    <time class="broadcast-time">{{ $row->air_time }}</time>
                    <a class="broadcast-name" href="{{ \App\Support\Locale::url('/yayinakisi') }}">{{ $row->program_name }}</a>
                    <span class="broadcast-desc">{{ $row->description }}</span>
                </li>
            @endforeach
        </ol>
    </section>

    {{-- Modül 13: REKLAM x2 --}}
    <x-ad-zone :zone="1395" :index="4" />
    <x-ad-zone :zone="1400" :index="5" />

    {{-- Modül 14: Businessweek kapak + SON SAYILAR --}}
    <section class="home-businessweek">
        <div class="bw-cover">
            <a href="https://www.businessweek.com.tr/" target="_blank" rel="noopener noreferrer" class="bw-cover-link">
                <x-lazy-image src="/img/placeholders/placeholder-3.svg" alt="Businessweek" contract="magazine" class="bw-cover-img img-contract" />
            </a>
        </div>
        <div class="bw-issues">
            <x-section-head :title="__('ui.latest_issues')" href="https://www.businessweek.com.tr/" allLabel="Businessweek" />
            <ul class="bw-issue-list">
                @foreach ([0, 1, 2, 3] as $week)
                    <li>
                        <a href="https://www.businessweek.com.tr/" target="_blank" rel="noopener noreferrer">
                            <span class="bw-issue-no">{{ __('ui.issue') }} {{ 2026 - $week - 0 }}</span>
                            <time class="bw-issue-date">{{ now()->subWeeks($week)->startOfWeek()->isoFormat('D MMMM YYYY') }}</time>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- Modül 15: Foto Haber --}}
    <section class="home-gallery">
        <x-section-head :title="__('ui.photo_news')" :href="\App\Support\Locale::url('/fotohaber')" level="h3" />
        <div class="gallery-grid">
            @foreach ($galleries as $gallery)
                <article class="gallery-card">
                    <a href="{{ $gallery->url }}">
                        <figure>
                            <x-lazy-image :src="asset($gallery->cover ?: '/img/og-default.png')" alt="" contract="news_card_square" class="img-contract" />
                            <span class="gallery-count">{{ $gallery->photo_total_count }}</span>
                        </figure>
                        <div class="gallery-body">
                            <span class="category text-xs">{{ $gallery->category?->name ?? __('ui.photo_news_upper') }}</span>
                            <h4 class="font-unna font-bold line-clamp-2">{{ $gallery->title }}</h4>
                        </div>
                    </a>
                </article>
            @endforeach
        </div>
    </section>

    {{-- Modül 16: VİDEOLAR --}}
    <section class="home-videos">
        <x-section-head :title="__('ui.video_news')" :href="\App\Support\Locale::url('/video')" />
        <div class="video-list">
            @foreach ($videos as $episode)
                <article class="video-card">
                    <a href="{{ $episode->program?->url ?? \App\Support\Locale::url('/video') }}">
                        <figure class="video-thumb">
                            <x-lazy-image :src="asset($episode->thumbnail ?: '/img/og-default.png')" alt="" contract="video_thumb" class="img-contract" />
                            <span class="program-duration">{{ $episode->duration }}</span>
                        </figure>
                        <h3 class="video-title font-unna font-bold line-clamp-2">{{ $episode->display_title }}</h3>
                    </a>
                </article>
            @endforeach
        </div>
    </section>

    {{-- Modül 17: REKLAM x2 --}}
    <x-ad-zone :zone="2015" :index="6" />
    <x-ad-zone :zone="2026" :index="7" />

    {{-- Modül 18: PODCAST SERİLERİ --}}
    <section class="home-podcasts">
        <x-section-head :title="__('ui.podcasts')" :href="\App\Support\Locale::url('/podcastler/')" />
        <div class="podcast-grid">
            @foreach ($podcasts as $podcast)
                <article class="podcast-card">
                    <a href="{{ $podcast->url }}">
                        <figure>
                            <x-lazy-image :src="asset($podcast->cover ?: '/img/og-default.png')" alt="" contract="news_card_square" class="img-contract" />
                        </figure>
                        <div class="podcast-body">
                            <h3 class="font-unna font-bold line-clamp-2">{{ $podcast->title }}</h3>
                            <p class="podcast-desc line-clamp-2">{{ $podcast->description }}</p>
                            <span class="podcast-air text-xs">{{ $podcast->air_days }} · {{ $podcast->air_time }}</span>
                        </div>
                    </a>
                </article>
            @endforeach
        </div>
    </section>

    {{-- Modül 19: KAP HABERLERİ --}}
    <section class="home-kap">
        <x-section-head :title="__('ui.kap_news')" :href="\App\Support\Locale::url('/borsa/hisseler/kap-haberleri')" />
        <ul class="kap-list">
            @foreach ($kapDisclosures as $disclosure)
                <li>
                    <a href="{{ $disclosure->url }}">
                        <span class="kap-company">{{ $disclosure->company_code }} · {{ $disclosure->company_name }}</span>
                        <span class="kap-title line-clamp-1">{{ $disclosure->title }}</span>
                        <time class="kap-time">{{ $disclosure->published_at?->format('d.m.Y H:i') }}</time>
                    </a>
                </li>
            @endforeach
        </ul>
    </section>

    {{-- Modül 20: Piyasa verileri widget (her sekme kendi h2 + tablo) --}}
    @include('home.partials.market-tables')

    {{-- Modül 21: REKLAM x2 --}}
    <x-ad-zone :zone="2029" :index="8" />
    <x-ad-zone :zone="2129" :index="9" />

    {{-- Modül 22: SEKTÖR HABERLERİ (+ Siyaset Haberleri) --}}
    <section class="home-sector" id="home-sector">
        <x-news-module :title="__('ui.sector_news')"
                       :items="$sectorNews"
                       :href="\App\Support\Locale::url('/haberler/sektor-haberleri')"
                       :columns="2" />

        <div class="politics-block">
            <x-section-head :title="__('ui.politics_news')" :href="\App\Support\Locale::url('/haberler/sektor-haberleri')" level="h3" />
            <ul class="headline-list">
                @foreach ($politicsNews as $item)
                    <li>
                        <a href="{{ $item->url }}">
                            <span class="headline-cat">{{ $item->category?->name }}</span>
                            <span class="headline-title font-unna font-bold">{{ $item->title }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- Modül 22b: EMTİA HABERLERİ — Reuters/piyasa haber modülü bileşeni --}}
    <x-news-module id="home-commodity-news"
                   :title="__('ui.commodity_news')"
                   :items="$commodityNews"
                   :href="\App\Support\Locale::url('/emtia')"
                   :columns="3" />

    {{-- Modül 23: KRİPTO PARA — fiyat swiper'ı --}}
    <section class="home-crypto">
        <x-section-head :title="__('ui.crypto')" :href="\App\Support\Locale::url('/kripto')" />
        <div class="crypto-swiper" data-horizontal-strip>
            @foreach ($cryptoQuotes as $quote)
                <a class="crypto-card" href="{{ $quote->instrument->url }}" data-market-code="{{ $quote->code() }}">
                    <span class="crypto-code">{{ $quote->code() }}</span>
                    <span class="crypto-name text-xs">{{ $quote->instrument->name }}</span>
                    <span class="lastPrice font-bold {{ $quote->direction() === 'up' ? 'text-green-700' : ($quote->direction() === 'down' ? 'text-red-700' : 'text-gray-400') }}" data-market-field="last">{{ $quote->formattedUsdPrice() }}</span>
                    <span class="percentChange text-xs {{ $quote->direction() === 'up' ? 'text-green-700' : ($quote->direction() === 'down' ? 'text-red-700' : 'text-gray-400') }}" data-market-field="change_percent">{{ $quote->formattedChangePercent() }}</span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Modül 24: REKLAM x2 --}}
    <x-ad-zone :zone="22801" :index="10" />
    <x-ad-zone :zone="22802" :index="11" />

    {{-- Modül 25: EN ÇOK OKUNANLAR — Bugün | Bu Hafta | Bu Ay (global widget) --}}
    <x-most-read class="home-most-read"
                 :tabs="[
                     'today' => $mostReadTabs['today'],
                     'week' => $mostReadTabs['week'],
                     'month' => $mostReadTabs['month'],
                 ]" />

</div>
@endsection