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

    {{-- Modül 2: Hero / manşet slider + sağda Öne Çıkanlar (headline + highlights) --}}
    <section data-section="headline" class="grid grid-cols-12 gap-4 mb-4 md:mb-4 home-hero-row">
        <div class="bht-grid col-span-12 md:col-span-6 row-span-1" dataType="headline">
            <div data-widget-type="headline">
                <div data-type="swiper" data-name="" class="swiper slider-custom relative hero-main-slider"
                     data-swiper='{"loop":true,"pagination":{"el":".swiper-pagination","clickable":true},"navigation":{"nextEl":".main-slider .bloomberght-icon-font-chevron-right","prevEl":".main-slider .bloomberght-icon-font-chevron-left"}}'>
                    <div class="swiper-wrapper">
                        @foreach ($hero as $index => $item)
                            <div class="swiper-slide" data-hash="haber-{{ $item->news_id }}" style="">
                                <div class="-z-100">
                                    <figure>
                                        <div class="w-full h-auto aspect-video bg-gray-50">
                                            <a href="{{ $item->url }}" title="{{ $item->title }}">
                                                <x-lazy-image :src="asset($item->image ?: '/img/og-default.png')" alt="{{ $item->title }}" class="w-full h-full swiper-lazy" contract="news_card" :eager="$index === 0" />
                                            </a>
                                        </div>
                                        <figcaption class="pt-10">
                                            <a href="{{ $item->url }}" title="{{ $item->title }}" class="md:min-h-16 inline-flex items-center">
                                                <div class="font-unna font-bold text-2xl text-ellipsis inline-flex">
                                                    <span class="line-clamp-2">{{ $item->title }}</span>
                                                </div>
                                            </a>
                                            @if ($item->spot)
                                                <p class="hero-slide-spot line-clamp-2 mt-2 text-sm text-gray-700">{{ \Illuminate\Support\Str::limit(strip_tags($item->spot), 160) }}</p>
                                            @endif
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="w-full h-20 absolute bottom-0">
                        <div class="swiper-pagination relative type4"></div>
                    </div>
                    <div class="main-slider text-black absolute left-0 top-32 3xl:top-44 h-6 w-full z-10 pointer-events-none">
                        <button type="button" aria-label="prev" class="swiper-button-prev pointer-events-auto after:content-[''] bg-black bg-opacity-40 text-white border-y border-r border-white w-9 h-9 bloomberght-icon-font-chevron-left left-0 cursor-pointer text-inherit hidden md:flex"></button>
                        <button type="button" aria-label="next" class="swiper-button-next pointer-events-auto after:content-[''] bg-black bg-opacity-40 text-white border-y border-l border-white w-9 h-9 bloomberght-icon-font-chevron-right right-0 cursor-pointer text-inherit hidden md:flex"></button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Öne Çıkanlar (highlights): görsel + başlık + özet kartları --}}
        <div class="bht-grid col-span-12 md:col-span-6 row-span-1" dataType="highlights">
            <div class="highlights-list">
                @foreach ($featuredNews as $index => $item)
                    <a href="{{ $item->url }}" class="highlights-item {{ $index === 0 ? 'highlights-item-lead' : 'highlights-item-row' }}">
                        @if ($index === 0)
                            <figure class="highlights-lead-figure">
                                <x-lazy-image :src="asset(($item->image ?? '') ?: '/img/og-default.png')" alt="{{ $item->title }}" class="highlights-lead-img" contract="news_card" :eager="false" />
                            </figure>
                        @endif
                        <div class="highlights-caption">
                            <span class="category text-xs text-[#bd1b2d]">{{ $item->category?->name }}</span>
                            <span class="highlights-title font-unna font-bold line-clamp-2">{{ $item->title }}</span>
                            @if ($index === 0 && $item->spot)
                                <span class="highlights-spot line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($item->spot), 150) }}</span>
                            @elseif ($index > 0 && $item->spot)
                                <span class="highlights-spot highlights-spot-row line-clamp-1">{{ \Illuminate\Support\Str::limit(strip_tags($item->spot), 80) }}</span>
                            @endif
                        </div>
                        @if ($index > 0)
                            <span class="highlights-thumb">
                                <x-lazy-image :src="asset(($item->image ?? '') ?: '/img/og-default.png')" alt="" class="highlights-thumb-img" contract="news_card_square" />
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- finance-slider mobil şerit (md:hidden, border-t) --}}
    <div class="finance-strip-mobile md:hidden" data-type="finance-strip" aria-label="{{ __('ui.market_strip') }}">
        <div class="finance-strip-track">
            @foreach ($financeGroups as $group)
                @foreach ($group['quotes'] as $quote)
                    @php
                        $dir = $quote->direction();
                    @endphp
                    <a class="finance-strip-item" href="{{ $quote->instrument->url }}" data-market-code="{{ $quote->code() }}">
                        <span class="fs-name text-xs">{{ $quote->instrument->name }}</span>
                        <span class="lastPrice font-bold {{ $dir === 'up' ? 'text-green-700' : ($dir === 'down' ? 'text-red-700' : 'text-gray-400') }}" data-market-field="last">{{ $quote->formattedLast() }}</span>
                        <span class="percentChange text-xs {{ $dir === 'up' ? 'text-green-700' : ($dir === 'down' ? 'text-red-700' : 'text-gray-400') }}" data-market-field="change_percent"><i class="q-arrow q-arrow-{{ $dir }}" aria-hidden="true"></i>{{ $quote->formattedChangePercent() }}</span>
                    </a>
                @endforeach
            @endforeach
        </div>
    </div>

    {{-- REKLAM --}}
    <x-ad-zone :zone="1257" :index="1" />

    {{-- PİYASALAR büyük widget --}}
    @include('home.partials.market-widget')

    {{-- PROGRAMLAR --}}
    <section class="home-programs">
        <x-section-head :title="__('ui.programs')" :href="\App\Support\Locale::url('/video')" />
        <div class="program-list">
            @foreach ($programs as $episode)
                <article class="program-card">
                    <a href="{{ $episode->program?->url ?? \App\Support\Locale::url('/video') }}">
                        <figure class="program-thumb">
                            <x-lazy-image :src="asset($episode->thumbnail ?: $episode->program?->thumbnail ?: '/img/og-default.png')" alt="" contract="video_thumb" class="img-contract" />
                            <span class="program-play bloomberght-icon-font-play" aria-hidden="true"></span>
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

    {{-- REKLAM x2 --}}
    <x-ad-zone :zone="2167" :index="2" />
    <x-ad-zone :zone="1265" :index="3" />

    {{-- ÖNE ÇIKANLAR (#2) + TÜM HABERLER yan yana (referans marketAgenda + allNews) --}}
    <section class="home-agenda-row grid grid-cols-12 gap-4 mb-4 md:mb-10">
        <div class="bht-grid col-span-12 md:col-span-8 row-span-2" dataType="marketAgenda">
            @include('home.partials.featured-agenda', ['items' => $featuredCards])
        </div>
        <div class="bht-grid col-span-12 md:col-span-4 row-span-2" dataType="allNews">
            <section class="home-all-news" id="home-all-news">
                <x-section-head :title="__('ui.all_news')" :href="\App\Support\Locale::url('/tumhaberler')" />
                <ol class="headline-list all-news-scroll">
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
        </div>
    </section>

    {{-- YAYIN AKIŞI --}}
    @include('home.partials.streaming-strip')

    {{-- REKLAM x2 --}}
    <x-ad-zone :zone="1395" :index="4" />
    <x-ad-zone :zone="1400" :index="5" />

    {{-- Businessweek kapak + SON SAYILAR --}}
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

    {{-- Foto Haber + VİDEOLAR yan yana (referans photoNews + video) --}}
    <section class="home-media-row grid grid-cols-12 gap-4 mb-4 md:mb-10">
        <div class="bht-grid col-span-12 md:col-span-8" dataType="photoNews">
            <section class="home-gallery" id="home-gallery">
                <x-section-head :title="__('ui.photo_news')" :href="\App\Support\Locale::url('/fotohaber')" level="h3" />
                <div class="gallery-grid">
                    @foreach ($galleries->take(4) as $gallery)
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
        </div>
        <div class="bht-grid col-span-12 md:col-span-4" dataType="video">
            <section class="home-videos" id="home-videos">
                <x-section-head :title="__('ui.video_news')" :href="\App\Support\Locale::url('/video')" />
                <div class="video-list">
                    @foreach ($videos as $episode)
                        <article class="video-card">
                            <a href="{{ $episode->program?->url ?? \App\Support\Locale::url('/video') }}">
                                <figure class="video-thumb">
                                    <x-lazy-image :src="asset($episode->thumbnail ?: '/img/og-default.png')" alt="" contract="video_thumb" class="img-contract" />
                                    <span class="program-play bloomberght-icon-font-play" aria-hidden="true"></span>
                                    <span class="program-duration">{{ $episode->duration }}</span>
                                </figure>
                                <h3 class="video-title font-unna font-bold line-clamp-2">{{ $episode->display_title }}</h3>
                            </a>
                        </article>
                    @endforeach
                </div>
            </section>
        </div>
    </section>

    {{-- REKLAM x2 --}}
    <x-ad-zone :zone="2015" :index="6" />
    <x-ad-zone :zone="2026" :index="7" />

    {{-- PODCAST SERİLERİ --}}
    @include('home.partials.podcast-strip')

    {{-- Piyasa Haberleri + KAP HABERLERİ yan yana (referans marketNews + kapNews) --}}
    <section class="home-news-row grid grid-cols-12 gap-4 mb-4 md:mb-10">
        <div class="bht-grid col-span-12 md:col-span-6" dataType="marketNews">
            <section class="home-market-news" id="home-market-news">
                <x-section-head :title="__('ui.market_news')" :href="\App\Support\Locale::url('/piyasalar')" />
                <ol class="headline-list all-news-scroll">
                    @foreach ($marketNews as $item)
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
        </div>
        <div class="bht-grid col-span-12 md:col-span-6" dataType="kapNews">
            <section class="home-kap" id="home-kap">
                <x-section-head :title="__('ui.kap_news')" :href="\App\Support\Locale::url('/borsa/hisseler/kap-haberleri')" />
                <ul class="kap-list all-news-scroll">
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
        </div>
    </section>

    {{-- Piyasa Verileri widget (tabbed) --}}
    @include('home.partials.market-tables')

    {{-- REKLAM x2 --}}
    <x-ad-zone :zone="2029" :index="8" />
    <x-ad-zone :zone="2129" :index="9" />

    {{-- Sektör / Emtia / Siyaset Haberleri — 3 sütun (referans emtiaNews + sectorNews + politicsNews) --}}
    <section class="home-sector-section grid grid-cols-12 gap-4 mb-4 md:mb-10" id="home-sector">
        <div class="bht-grid col-span-12 md:col-span-4" dataType="sectorNews">
            <x-news-module :title="__('ui.sector_news')"
                           :items="$sectorNews"
                           :href="\App\Support\Locale::url('/haberler/sektor-haberleri')"
                           :columns="1" />
        </div>
        <div class="bht-grid col-span-12 md:col-span-4" dataType="emtiaNews">
            <x-news-module id="home-commodity-news"
                           :title="__('ui.commodity_news')"
                           :items="$commodityNews"
                           :href="\App\Support\Locale::url('/emtia')"
                           :columns="1" />
        </div>
        <div class="bht-grid col-span-12 md:col-span-4" dataType="politicsNews">
            <section class="politics-block" id="home-politics">
                <x-section-head :title="__('ui.politics_news')" :href="\App\Support\Locale::url('/haberler/sektor-haberleri')" level="h3" />
                <ul class="headline-list all-news-scroll">
                    @foreach ($politicsNews as $item)
                        <li>
                            <a href="{{ $item->url }}">
                                <span class="headline-cat">{{ $item->category?->name }}</span>
                                <span class="headline-title font-unna font-bold">{{ $item->title }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        </div>
    </section>

    {{-- KRİPTO PARA --}}
    <section class="home-crypto">
        <x-section-head :title="__('ui.crypto')" :href="\App\Support\Locale::url('/kripto')" />
        <div class="crypto-swiper" data-horizontal-strip>
            @foreach ($cryptoQuotes as $quote)
                @php
                    $dir = $quote->direction();
                @endphp
                <a class="crypto-card" href="{{ $quote->instrument->url }}" data-market-code="{{ $quote->code() }}">
                    <span class="crypto-code">{{ $quote->code() }}</span>
                    <span class="crypto-name text-xs">{{ $quote->instrument->name }}</span>
                    <span class="lastPrice font-bold {{ $dir === 'up' ? 'text-green-700' : ($dir === 'down' ? 'text-red-700' : 'text-gray-400') }}" data-market-field="last">{{ $quote->formattedUsdPrice() }}</span>
                    <span class="percentChange text-xs {{ $dir === 'up' ? 'text-green-700' : ($dir === 'down' ? 'text-red-700' : 'text-gray-400') }}" data-market-field="change_percent"><i class="q-arrow q-arrow-{{ $dir }}" aria-hidden="true"></i>{{ $quote->formattedChangePercent() }}</span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- REKLAM x2 --}}
    <x-ad-zone :zone="22801" :index="10" />
    <x-ad-zone :zone="22802" :index="11" />

    {{-- EN ÇOK OKUNANLAR — Bugün | Bu Hafta | Bu Ay --}}
    <x-most-read class="home-most-read"
                 :tabs="[
                     'today' => $mostReadTabs['today'],
                     'week' => $mostReadTabs['week'],
                     'month' => $mostReadTabs['month'],
                 ]" />

</div>
@endsection
