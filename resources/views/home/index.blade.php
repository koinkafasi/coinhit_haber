@extends('front.layouts.app')

@section('title', ($siteSettings['site_name'] ?? 'Bloomberg HT').' | '.__('ui.site_tagline'))

@section('masthead')
    {{-- Modül 1: Masthead ad — zone_1264 (masaüstü) / zone_1394 (mobil), header ÜSTÜNDE --}}
    <div class="masthead-ad">
        <div class="ad-head"><span class="ad-label !text-xxs">{{ __('ui.ad') }}</span></div>
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

        {{-- Öne Çıkanlar (highlights): referans grid md:grid-cols-2 + iki kolon;
             ilk kart type3 (H2 + aspect-video kapak), devami type1. --}}
        <div class="bht-grid col-span-12 md:col-span-6 row-span-1" dataType="highlights" datatype="highlights">
            <div data-widget-type="highlights" class="border">
                @php
                    $__hl = $featuredNews->take(4)->values();
                @endphp
                <div class="grid md:grid-cols-2">
                    <div class="flex flex-col justify-between">
                        @if ($__hl->get(0))
                            <div data-type="news-card-type3" data-news-id="" class="border-b mb-2 pb-2">
                                <h2 class="font-unna font-bold text-3xl mb-2 p-2 pt-4">{{ __('ui.featured') }}</h2>
                                <x-news-card :news="$__hl->get(0)" type="type3" />
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-col justify-between">
                        @foreach ($__hl->slice(1, 3) as $__item)
                            <x-news-card :news="$__item" type="type1" />
                        @endforeach
                    </div>
                </div>
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

    {{-- PROGRAMLAR (referans programs: arka-plan + cetvel + swiper + figure kart) --}}
    <section class="home-programs" data-widget-type="programs" class="relative">
        <div data-type="background" class="absolute -z-10 left-1/2 top-0 w-[100vw] h-96 -translate-x-1/2 -mt-4 overflow-hidden bg-gray-100 h-full mt-0" aria-hidden="true">
            <picture>
                <img src="https://im.bloomberght.com/assets/bht/bg/desktop/2x/bg-image-1.png?v=1004" alt="" role="presentation" loading="eager" fetchpriority="high" decoding="async" class="absolute inset-0 w-full h-full object-cover object-[center_top] max-md:object-contain max-md:object-[center_top]">
            </picture>
        </div>
        <div class="mb-4 border-b border-black relative w-full before:absolute before:top-0 before:bottom-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black"></div>
        <a href="{{ \App\Support\Locale::url('/video') }}" title="{{ __('ui.programs') }}" class="absolute top-4 left-0 font-unna font-bold text-3xl mb-2 z-10"><h2>{{ __('ui.programs') }}</h2></a>
        <div>
            <div data-type="swiper" data-name="" class="swiper slider-custom relative mb-4 pt-12 w-full programs"
                 data-swiper='{"slidesPerView":"auto","spaceBetween":20,"loop":false,"autoplay":{"delay":4000,"disableOnInteraction":true,"pauseOnMouseEnter":true},"lazy":true,"navigation":{"nextEl":".programs .swiper-button-next","prevEl":".programs .swiper-button-prev"}}'>
                <div class="swiper-wrapper">
                    @foreach ($programs as $episode)
                        <div class="swiper-slide w-72">
                            <a href="{{ $episode->program?->url ?? \App\Support\Locale::url('/video') }}" title="{{ $episode->display_title }}">
                                <figure class="mb-4 program-card">
                                    <div class="mb-4 relative program-thumb">
                                        <div class="w-full h-auto aspect-video bg-gray-50">
                                            <x-lazy-image :src="asset($episode->thumbnail ?: $episode->program?->thumbnail ?: '/img/og-default.png')" alt="{{ $episode->display_title }}" contract="video_thumb" class="swiper-lazy w-full h-full img-contract" />
                                        </div>
                                        <div class="absolute right-0 bottom-0">
                                            <div class="bloomberght-icon-font-play bg-white text-black rounded-tl-lg flex justify-center items-center w-14 h-14 mb-[1px] program-play"></div>
                                            <div class="flex justify-center items-center bg-white text-black w-14 h-7 text-xs program-duration">{{ $episode->duration }}</div>
                                        </div>
                                    </div>
                                    <figcaption>
                                        <div class="font-unna font-bold text-xl text-ellipsis line-clamp-3 mb-1 program-title">{{ $episode->display_title }}</div>
                                    </figcaption>
                                </figure>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
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
        <div class="bht-grid col-span-12 md:col-span-4 row-span-2" dataType="allNews" datatype="allNews">
            <section class="home-all-news" id="home-all-news" data-widget-type="allNews">
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
    <section class="home-businessweek" data-widget-type="businessWeekTurkey">
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
        <div class="bht-grid col-span-12 md:col-span-8" dataType="photoNews" datatype="photoNews">
<section class="home-gallery h-full" id="home-gallery" data-widget-type="photoNews">
                <div class="mb-4 border-b border-black relative before:absolute before:top-0 before:bottom-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black"></div>
                @php
                    $__photoItems = $galleries->take(4)->values();
                    $__photoCols = [
                        array_slice($__photoItems->all(), 0, 2),
                        array_slice($__photoItems->all(), 2, 2),
                    ];
                @endphp
                {{-- Referans photoNews: 2 kolon, ilk kart type5 (kapak+caption),
                     kalanlar type2 (yatay). --}}
                <div class="grid grid-cols-1 md:grid-cols-2 md:gap-4">
                    @foreach ($__photoCols as $__col)
                        <div class="w-full flex flex-col justify-between">
                            @foreach ($__col as $__j => $__gallery)
                                <x-news-card :news="$__gallery" :type="$__j === 0 ? 'type5' : 'type2'" />
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
        <div class="bht-grid col-span-12 md:col-span-4" dataType="video" datatype="video">
            <section class="home-videos" id="home-videos" data-widget-type="video">
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
        <div class="bht-grid col-span-12 md:col-span-6" dataType="marketNews" datatype="marketNews">
            <section class="home-market-news" id="home-market-news" data-widget-type="marketNews">
                <x-section-head :title="__('ui.market_news')" :href="\App\Support\Locale::url('/piyasalar')" level="h3" />
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
        <div class="bht-grid col-span-12 md:col-span-6" dataType="kapNews" datatype="kapNews">
            <section class="home-kap" id="home-kap" data-widget-type="kapNews">
                <div data-type="head-type1">
                    <div class="relative before:absolute before:top-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black before:bottom-0 before:h-2.5 border-b border-black mb-4"></div>
                    <a href="{{ \App\Support\Locale::url('/borsa/hisseler/kap-haberleri') }}" title="{{ __('ui.kap_news') }}" class="inline-block mb-3">
                        <h2 class="font-unna font-bold text-3xl">{{ __('ui.kap_news') }}</h2>
                    </a>
                </div>
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

    {{-- Emtia / Sektör / Siyaset Haberleri — 3 sütun (referans emtiaNews + sectorNews + politicsNews sırası) --}}
    <section class="home-sector-section grid grid-cols-12 gap-4 mb-4 md:mb-10" id="home-sector">
        <div class="bht-grid col-span-12 md:col-span-4" dataType="emtiaNews" datatype="emtiaNews" data-widget-type="emtiaNews">
            <x-news-module id="home-commodity-news"
                           :title="__('ui.commodity_news')"
                           :items="$commodityNews"
                           :href="\App\Support\Locale::url('/emtia')"
                           :columns="1"
                           :level="'h3'" />
        </div>
        <div class="bht-grid col-span-12 md:col-span-4" dataType="sectorNews" datatype="sectorNews" data-widget-type="sectorNews">
            <x-news-module :title="__('ui.sector_news')"
                           :items="$sectorNews"
                           :href="\App\Support\Locale::url('/haberler/sektor-haberleri')"
                           :columns="1" />
        </div>
        <div class="bht-grid col-span-12 md:col-span-4" dataType="politicsNews" datatype="politicsNews">
            <section class="politics-block" id="home-politics" data-widget-type="politicsNews">
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

    {{-- KRİPTO PARA (referans crypto: siyah zemin + swiper + stats-bar kart) --}}
    <section class="home-crypto relative py-8" data-widget-type="crypto" data-section="crypto">
        <div data-type="background" class="absolute -z-10 left-1/2 top-0 w-[100vw] h-96 -translate-x-1/2 -mt-4 bg-cover bg-no-repeat bg-black h-full mt-0"></div>
        <a href="{{ \App\Support\Locale::url('/kripto') }}" title="{{ __('ui.crypto') }}" class="absolute top-4 font-unna font-bold text-3xl mb-2 text-white z-10"><h2>{{ __('ui.crypto') }}</h2></a>
        <div>
            <div data-type="swiper" data-name="" class="crypto-swiper swiper slider-custom relative pt-10 w-full crypto"
                 data-swiper='{"slidesPerView":"auto","loop":false,"lazy":true,"navigation":{"nextEl":".crypto .swiper-button-next","prevEl":".crypto .swiper-button-prev"}}'>
                <div class="swiper-wrapper">
                    @foreach ($cryptoQuotes as $quote)
                        @php
                            $dir = $quote->direction();
                            $bar = $dir === 'up' ? 'before:bg-green-700' : ($dir === 'down' ? 'before:bg-red-500' : 'before:bg-gray-400');
                        @endphp
                        <div class="swiper-slide w-auto pr-4">
                            <div data-type="stats-bar-type1">
                                <a href="{{ $quote->instrument->url }}" title="{{ $quote->instrument->name }}" class="security-{{ \Illuminate\Support\Str::slug($quote->code()) }} block cursor-pointer" data-type="crypto">
                                    <div class="flex flex-col relative before:absolute before:top-0 before:left-0 before:w-1 before:h-full setColor {{ $bar }} text-white pl-2 crypto-card" data-market-code="{{ $quote->code() }}">
                                        <span class="text-xs text-ellipsis overflow-hidden whitespace-nowrap crypto-name">{{ $quote->instrument->name }}</span>
                                        <span class="lastPrice font-bold" data-market-field="last">{{ $quote->formattedUsdPrice() }}</span>
                                        <span class="percentChange text-xs" data-market-field="change_percent">{{ $quote->formattedChangePercent() }}</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
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
