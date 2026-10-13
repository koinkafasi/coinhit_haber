@extends('front.layouts.app')

@section('title', ($siteSettings['site_name'] ?? 'Bloomberg HT').' | '.__('ui.site_tagline'))

@push('scripts')
    @php
        $__hc = @filemtime(public_path('js/highchart.js')) ?: 1;
    @endphp
    <script type="module" src="{{ asset('js/highchart.js') }}?v={{ $__hc }}" defer></script>
@endpush

@if (\App\Models\Advertisement::forZone('1264'))
    @section('masthead')
        {{-- Modül 1: Masthead ad — yönetilebilir reklam (zone_1264); aktif reklam yoksa gizli --}}
        @include('partials.ad-banner', ['zone' => '1264', 'variant' => 'masthead'])    @endsection
@endif

@section('content')
<div class="home-page">

    {{-- Modül 2: Hero / manşet slider + sağda Öne Çıkanlar (headline + highlights) --}}
    <section data-section="headline" class="grid grid-cols-12 gap-4 mb-4 md:mb-10">
        <div class="bht-grid col-span-12 md:col-span-6 row-span-1" dataType="headline">
            <div data-widget-type="headline">
                <div data-type="swiper" data-name="" class="swiper slider-custom relative"
                     data-swiper='{"hashMode":true,"lazy":{"loadPrevNext":true,"loadPrevNextAmount":1,"loadOnTransitionStart":true},"loop":true,"pagination":{"el":".swiper-pagination","clickable":true},"navigation":{"nextEl":".main-slider .bloomberght-icon-font-chevron-right","prevEl":".main-slider .bloomberght-icon-font-chevron-left"}}'>
                    <div class="swiper-wrapper">
                        @foreach ($hero as $index => $item)
                            @php
                                $__heroImg = asset($item->image ?: '/img/og-default.png');
                            @endphp
                            <div class="swiper-slide " data-hash="{{ md5((string) $item->news_id) }}" style="">
                                <div class="-z-100">
                                    <figure>
                                        <div class="w-full h-auto aspect-video bg-gray-50">
                                            <a href="{{ $item->url }}" title="{{ $item->title }}">
                                                @if ($index === 0)
                                                    <img
                                                        srcset="{{ \App\Support\Media::srcset($__heroImg, [320, 400, 640, 1280]) }}"
                                                        sizes="(max-width: 640px) 100vw, (max-width: 1024px) 640px, 960px"
                                                        src="{{ $__heroImg }}"
                                                        alt="{{ $item->title }}"
                                                        class="w-full h-full "
                                                        loading="eager"
                                                        fetchpriority="high"
                                                    >
                                                @else
                                                    <img
                                                        src="{{ $__heroImg }}"
                                                        alt="{{ $item->title }}"
                                                        class="swiper-lazy w-full h-full "
                                                        decoding="async"
                                                        loading="lazy"
                                                    >
                                                @endif
                                            </a>
                                        </div>
                                        <figcaption class="pt-10">
                                            <a href="{{ $item->url }}" title="{{ $item->title }}" class="md:min-h-16 inline-flex items-center">
                                                <div class="font-unna font-bold text-2xl text-ellipsis inline-flex">
                                                    <span class="line-clamp-2">{{ $item->title }}</span>
                                                </div>
                                            </a>
                                        </figcaption>
                                    </figure>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="w-full h-20 absolute bottom-0">
                        <div class="swiper-pagination relative type4"></div>
                    </div>
                    <div class="main-slider text-black absolute left-0 top-[170px] 3xl:top-[218px] h-6 w-full z-10 pointer-events-none">
                        <button aria-label="prev" class="swiper-button-prev pointer-events-auto after:content-[''] bg-black bg-opacity-40 text-white border-y border-r border-white w-9 h-9 bloomberght-icon-font-chevron-left left-0 cursor-pointer text-inherit hidden md:flex"></button>
                        <button aria-label="next" class="swiper-button-next pointer-events-auto after:content-[''] bg-black bg-opacity-40 text-white border-y border-l border-white w-9 h-9 bloomberght-icon-font-chevron-right right-0 cursor-pointer text-inherit hidden md:flex"></button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Öne Çıkanlar (highlights): referans birebir —
             sol sütun: type3 (başlıklı kapak) + 2 type1;
             sağ sütun: 2 type1 + type3 (kapak) + type1;
             sağ sütun border-l border-t md:border-t-0; son kartlar md:mb-4. --}}
        <div class="bht-grid col-span-12 md:col-span-6 row-span-1" dataType="highlights" datatype="highlights">
            {{-- Referans: highlights widget üstünde bölüm başlığı çubuğu —
                 siyah çizgi + solda 230x10 siyah kalın belirteç (before). --}}
            <div class="border-b border-black relative before:absolute before:top-0 before:bottom-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black"></div>
            <div data-widget-type="highlights" class="border">
                @php
                    $__hl = $featuredNews->take(7)->values();
                @endphp
                <div class="grid md:grid-cols-2">
                    <div class="flex flex-col justify-between">
                        @if ($__hl->get(0))
                            <div class="border-b mb-2 pb-2" data-type="news-card-type3" data-news-id="{{ $__hl->get(0)->news_id }}">
                                <h2 class="font-unna font-bold text-3xl mb-2 p-2 pt-4">{{ __('ui.featured') }}</h2>
                                <a href="{{ $__hl->get(0)->url }}" title="{{ $__hl->get(0)->title }}">
                                    <figure class="px-2">
                                        <div class="w-full h-full aspect-video bg-gray-50 mb-4">
                                            <img src="{{ asset($__hl->get(0)->image ?: '/img/og-default.png') }}" alt="" class="w-full h-full" loading="lazy" decoding="async">
                                        </div>
                                        <figcaption class="w-full h-full flex flex-col justify-around">
                                            <div class="font-unna font-bold text-base 2xl:text-xl text-ellipsis line-clamp-2">{{ $__hl->get(0)->title }}</div>
                                        </figcaption>
                                    </figure>
                                </a>
                            </div>
                        @endif
                        @if ($__hl->get(1))
                            <div class="border-b mb-2 pb-2" data-type="news-card-type1" data-news-id="{{ $__hl->get(1)->news_id }}">
                                <a href="{{ $__hl->get(1)->url }}" title="{{ $__hl->get(1)->title }}">
                                    <figure class="px-2"><figcaption>
                                        <div class="font-unna font-bold text-base 2xl:text-xl text-ellipsis line-clamp-2">{{ $__hl->get(1)->title }}</div>
                                    </figcaption></figure>
                                </a>
                            </div>
                        @endif
                        @if ($__hl->get(2))
                            <div class="md:mb-4" data-type="news-card-type1" data-news-id="{{ $__hl->get(2)->news_id }}">
                                <a href="{{ $__hl->get(2)->url }}" title="{{ $__hl->get(2)->title }}">
                                    <figure class="px-2"><figcaption>
                                        <div class="font-unna font-bold text-base 2xl:text-xl text-ellipsis line-clamp-2">{{ $__hl->get(2)->title }}</div>
                                    </figcaption></figure>
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-col justify-between border-l border-t md:border-t-0">
                        @if ($__hl->get(3))
                            <div class="border-b mb-2 pb-2" data-type="news-card-type1" data-news-id="{{ $__hl->get(3)->news_id }}">
                                <a href="{{ $__hl->get(3)->url }}" title="{{ $__hl->get(3)->title }}">
                                    <figure class="px-2 pt-2"><figcaption>
                                        <div class="font-unna font-bold text-base 2xl:text-xl text-ellipsis line-clamp-2">{{ $__hl->get(3)->title }}</div>
                                    </figcaption></figure>
                                </a>
                            </div>
                        @endif
                        @if ($__hl->get(4))
                            <div class="border-b mb-2 pb-2" data-type="news-card-type1" data-news-id="{{ $__hl->get(4)->news_id }}">
                                <a href="{{ $__hl->get(4)->url }}" title="{{ $__hl->get(4)->title }}">
                                    <figure class="px-2"><figcaption>
                                        <div class="font-unna font-bold text-base 2xl:text-xl text-ellipsis line-clamp-2">{{ $__hl->get(4)->title }}</div>
                                    </figcaption></figure>
                                </a>
                            </div>
                        @endif
                        @if ($__hl->get(5))
                            <div class="border-b mb-2 pb-2" data-type="news-card-type3" data-news-id="{{ $__hl->get(5)->news_id }}" id="highlightsAdsArea2">
                                <a href="{{ $__hl->get(5)->url }}" title="{{ $__hl->get(5)->title }}">
                                    <figure class="px-2">
                                        <div class="w-full h-full aspect-video bg-gray-50 mb-4">
                                            <img src="{{ asset($__hl->get(5)->image ?: '/img/og-default.png') }}" alt="" class="w-full h-full" loading="lazy" decoding="async">
                                        </div>
                                        <figcaption class="w-full h-full flex flex-col justify-around">
                                            <div class="font-unna font-bold text-base 2xl:text-xl text-ellipsis line-clamp-2">{{ $__hl->get(5)->title }}</div>
                                        </figcaption>
                                    </figure>
                                </a>
                            </div>
                        @endif
                        @if ($__hl->get(6))
                            <div class="md:mb-4" data-type="news-card-type1" data-news-id="{{ $__hl->get(6)->news_id }}">
                                <a href="{{ $__hl->get(6)->url }}" title="{{ $__hl->get(6)->title }}">
                                    <figure class="px-2"><figcaption>
                                        <div class="font-unna font-bold text-base 2xl:text-xl text-ellipsis line-clamp-2">{{ $__hl->get(6)->title }}</div>
                                    </figcaption></figure>
                                </a>
                            </div>
                        @endif
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

    {{-- PİYASALAR büyük widget (referans marketGraphData: col-span-8, row-span-2) --}}
    <section data-section="markethData" class="home-market-row grid grid-cols-12 gap-4 mb-4 md:mb-10">
        <div class="bht-grid col-span-12 md:col-span-8 row-span-2" dataType="marketGraphData" datatype="marketGraphData">
            @include('home.partials.market-widget')
        </div>

        {{-- HIZLI LINKLER (col-span-4) + GRAFİK (col-span-4) yan yana --
             referans fastLinks(4) + graphics(4), marketGraphData'nin altında. --}}
        <div class="bht-grid col-span-12 md:col-span-4 row-span-1" dataType="fastLinks" datatype="fastLinks">
            @include('home.partials.fast-links')
        </div>
        <div class="bht-grid col-span-12 md:col-span-4 row-span-1" dataType="graphics" datatype="graphics">
            @include('home.partials.graphics')
        </div>
    </section>

    {{-- PROGRAMLAR (referans programs: arka-plan + cetvel + swiper + figure kart) --}}
    <section class="home-programs relative" data-widget-type="programs">
        <div data-type="background" class="absolute -z-10 left-1/2 top-0 w-[100vw] h-96 -translate-x-1/2 -mt-4 overflow-hidden bg-gray-100 h-full mt-0" aria-hidden="true">
            <picture>
                <img src="https://im.bloomberght.com/assets/bht/bg/desktop/2x/bg-image-1.png?v=1004" alt="" role="presentation" loading="eager" fetchpriority="high" decoding="async" class="absolute inset-0 w-full h-full object-cover object-[center_top] max-md:object-contain max-md:object-[center_top]">
            </picture>
        </div>
        <div class="mb-4 border-b border-black relative w-full before:absolute before:top-0 before:bottom-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black"></div>
        <a href="{{ \App\Support\Locale::url('/video') }}" title="{{ __('ui.programs') }}" class="absolute top-4 left-0 font-unna font-bold text-3xl mb-2 z-10"><h2>{{ __('ui.programs') }}</h2></a>
        <div>
            <div data-type="swiper" data-name="" class="swiper slider-custom relative mb-4 pt-12 w-full"
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
                <div class="programs text-black bg-white absolute -right-3 top-4 w-20 pl-4 z-90">
                    <button aria-label="prev" class="swiper-button-prev after:content-[''] w-8 h-11 bloomberght-icon-font-chevron-left right-14 cursor-pointer text-inherit"></button>
                    <button aria-label="next" class="swiper-button-next after:content-[''] w-8 h-11 bloomberght-icon-font-chevron-right right-0 cursor-pointer text-inherit"></button>
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
        <div class="bht-grid col-span-12 md:col-span-4" dataType="allNews" datatype="allNews">
            <section class="relative w-full" id="home-all-news" data-widget-type="allNews">
                <div data-type="head-type1" class=" ">
                    <div class="relative before:absolute before:top-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black before:bottom-0 before:h-2.5 border-b border-black mb-4"></div>
                    <a href="{{ \App\Support\Locale::url('/tumhaberler') }}" title="{{ __('ui.all_news') }}" class="inline-block mb-3 ">
                        <h2 class="font-unna font-bold text-3xl ">{{ __('ui.all_news') }}</h2>
                    </a>
                </div>
                <div class="md:2xl:max-h-[630px] md:max-h-[510px] overflow-y-auto">
                    {{-- Referans ölçümü: başlık + 9 kart, overflow-y-auto kapsayıcı. --}}
                    @foreach ($allNews->take(9) as $__an)
                        <x-news-card :news="$__an" type="type1" />
                    @endforeach
                </div>
            </section>

            {{-- Referans link-type1: TÜMÜ + belirteç deseni (çizgi + köşe işareti + chevron x3) --}}
            <a href="{{ \App\Support\Locale::url('/tumhaberler') }}" data-type="link-type1" title="TÜMÜ"
               class="w-full flex gap-2 my-2 items-center text-black bg-transparent">
                <div class="flex-grow border-t border-gray-300 relative before:content-[''] before:absolute before:bottom-0 before:flex before:h-4 before:w-[1px] before:bg-gray-300"></div>
                <span class="ml-2">
                    <span class="text-base mr-2">{{ __('ui.all') }}</span>
                    <span class="bloomberght-icon-font-chevron-right text-sm"></span>
                    <span class="bloomberght-icon-font-chevron-right text-sm"></span>
                    <span class="bloomberght-icon-font-chevron-right text-sm"></span>
                </span>
            </a>
        </div>
    </section>

    {{-- YAYIN AKIŞI --}}
    @include('home.partials.streaming-strip')

    {{-- REKLAM x2 --}}
    <x-ad-zone :zone="1395" :index="4" />
    <x-ad-zone :zone="1400" :index="5" />

    {{-- Businessweek kapak + SON SAYILAR --}}
    @include('home.partials.businessweek', ['bwNews' => $featuredNews])

    {{-- Foto Haber + VİDEOLAR yan yana (referans photoNews + video) --}}
    <section data-section="photonews" class="home-media-row grid grid-cols-12 gap-4 mb-4 md:mb-10">
        <div class="bht-grid col-span-12 md:col-span-8" dataType="photoNews" datatype="photoNews">
<section class="home-gallery h-full" id="home-gallery" data-widget-type="photoNews">
                <div class="mb-4 border-b border-black relative before:absolute before:top-0 before:bottom-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black"></div>
                    @include('home.partials.photo-gallery')
            </section>
        </div>
        <div class="bht-grid col-span-12 md:col-span-4" dataType="video" datatype="video">
            <section class="relative" id="home-videos" data-widget-type="video">
                <div data-type="head-type1" class=" ">
                    <div class="relative before:absolute before:top-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black before:bottom-0 before:h-2.5 border-b border-black mb-4"></div>
                    <a href="{{ \App\Support\Locale::url('/video') }}" title="{{ __('ui.video_news') }}" class="inline-block mb-3 ">
                        <h2 class="font-unna font-bold text-3xl ">{{ __('ui.video_news') }}</h2>
                    </a>
                </div>
                @php
                    $__vid = $videos->values();
                    $__vidLead = $__vid->get(0);
                    $__vidRest = $__vid->slice(1, 2)->values();
                @endphp
                @if ($__vidLead)
                    {{-- Referans video-card-type1 --}}
                    <div data-type="video-card-type1" class="border-b mb-2 2xl:pb-2">
                        <a href="{{ $__vidLead->program?->url ?? \App\Support\Locale::url('/video') }}"
                           title="{{ $__vidLead->display_title }}" class=" ">
                            <figure>
                                <div class="mb-4 relative">
                                    <div class="w-full h-auto aspect-video bg-gray-50">
                                        <x-lazy-image :src="asset($__vidLead->thumbnail ?: '/img/og-default.png')" alt="" contract="video_thumb" class="w-full h-full" :eager="false" />
                                    </div>
                                    <div class="absolute right-0 bottom-0">
                                        <div class="bloomberght-icon-font-play bg-white text-black rounded-tl-lg flex justify-center items-center w-14 h-14 mb-[1px]"></div>
                                        <div class="flex justify-center items-center bg-white text-black w-14 h-7 text-xs">{{ $__vidLead->duration }}</div>
                                    </div>
                                </div>
                                <figcaption>
                                    <div class="font-unna font-bold 2xl:text-2xl text-base text-ellipsis line-clamp-2">{{ $__vidLead->display_title }}</div>
                                    <div class="text-xxs"></div>
                                </figcaption>
                            </figure>
                        </a>
                    </div>
                @endif
                @foreach ($__vidRest as $__v)
                    {{-- Referans video-card-type2 --}}
                    <div data-type="video-card-type2" class="border-b 2xl:mb-3 2xl:pb-3 mb-2 pb-2">
                        <a href="{{ $__v->program?->url ?? \App\Support\Locale::url('/video') }}" title="{{ $__v->display_title }}">
                            <figure class="flex items-center gap-3 ">
                                <div class="w-1/2">
                                    <div class="relative aspect-video bg-gray-50">
                                        <x-lazy-image :src="asset($__v->thumbnail ?: '/img/og-default.png')" alt="" contract="video_thumb" class="w-full h-full object-fill" :eager="false" />
                                        <div class="absolute right-0 bottom-0">
                                            <div class="bloomberght-icon-font-play bg-white text-black rounded-tl-lg flex justify-center items-center w-10 h-10"></div>
                                        </div>
                                    </div>
                                </div>
                                <figcaption class="w-1/2">
                                    <div class="font-unna font-bold text-base 2xl:text-xl text-ellipsis line-clamp-3">{{ $__v->display_title }}</div>
                                    <div class="text-xxs text-gray-400">{{ $__v->duration }}</div>
                                </figcaption>
                            </figure>
                        </a>
                    </div>
                @endforeach
                <a href="{{ \App\Support\Locale::url('/video') }}" data-type="link-type1" title="{{ __('ui.all') }}" class="w-full flex gap-2 my-2 items-center text-black bg-white">
                    <div class="flex-grow border-t border-gray-300 relative before:content-[''] before:absolute before:bottom-0 before:flex before:h-4 before:w-[1px] before:bg-gray-300"></div>
                    <span class="ml-2">
                        <span class="text-base mr-2">{{ __('ui.all') }}</span>
                        <span class="bloomberght-icon-font-chevron-right text-sm"></span>
                        <span class="bloomberght-icon-font-chevron-right text-sm"></span>
                        <span class="bloomberght-icon-font-chevron-right text-sm"></span>
                    </span>
                </a>
            </section>
        </div>
    </section>

    {{-- REKLAM x2 --}}
    <x-ad-zone :zone="2015" :index="6" />
    <x-ad-zone :zone="2026" :index="7" />

    {{-- PODCAST SERİLERİ --}}
    @include('home.partials.podcast-strip')

    {{-- Piyasa Haberleri + KAP HABERLERİ yan yana (referans marketNews + kapNews)
         Referans ölçümü: marketNews col-span-8 (848px), kapNews col-span-4 (416px).
         marketNews sol sütun: type5 (bölüm başlıklı) + type2
                      sağ sütun: type1 + type1 + type5
         kapNews: 9 adet type1, overflow-y-auto kapsayıcı. --}}
    <section class="home-news-row grid grid-cols-12 gap-4 mb-4 md:mb-10">
        <div class="bht-grid col-span-12 md:col-span-8" dataType="marketNews" datatype="marketNews">
            <section class="h-full" id="home-market-news" data-widget-type="marketNews">
                <div class="mb-4 border-b border-black relative before:absolute before:top-0 before:bottom-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black"></div>
                @php
                    $__mn = $marketNews->take(5)->values();
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 md:gap-4">
                    <div class="w-full flex flex-col justify-between">
                        @if ($__mn->get(0))
                            <x-news-card :news="$__mn->get(0)" type="type5" :title="__('ui.market_news')" :href="\App\Support\Locale::url('/piyasalar')" />
                        @endif
                        @if ($__mn->get(1))
                            <x-news-card :news="$__mn->get(1)" type="type2" wrap-class="mb-4" />
                        @endif
                    </div>
                    <div class="w-full flex flex-col justify-between">
                        @if ($__mn->get(2))
                            <x-news-card :news="$__mn->get(2)" type="type1" />
                        @endif
                        @if ($__mn->get(3))
                            <x-news-card :news="$__mn->get(3)" type="type1" />
                        @endif
                        @if ($__mn->get(4))
                            <x-news-card :news="$__mn->get(4)" type="type5" />
                        @endif
                    </div>
                </div>
                <a href="{{ \App\Support\Locale::url('/piyasalar') }}" data-type="link-type1" title="{{ __('ui.all') }}" class="w-full flex gap-2 my-2 items-center text-black bg-white">
                    <div class="flex-grow border-t border-gray-300 relative before:content-[''] before:absolute before:bottom-0 before:flex before:h-4 before:w-[1px] before:bg-gray-300"></div>
                    <span class="ml-2">
                        <span class="text-base mr-2">{{ __('ui.all') }}</span>
                        <span class="bloomberght-icon-font-chevron-right text-sm"></span>
                        <span class="bloomberght-icon-font-chevron-right text-sm"></span>
                        <span class="bloomberght-icon-font-chevron-right text-sm"></span>
                    </span>
                </a>
            </section>
        </div>
        <div class="bht-grid col-span-12 md:col-span-4" dataType="kapNews" datatype="kapNews">
            <section class="relative w-full" id="home-kap" data-widget-type="kapNews">
                <div data-type="head-type1" class=" ">
                    <div class="relative before:absolute before:top-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black before:bottom-0 before:h-2.5 border-b border-black mb-4"></div>
                    <a href="{{ \App\Support\Locale::url('/borsa/hisseler/kap-haberleri') }}" title="{{ __('ui.kap_news') }}" class="inline-block mb-3 ">
                        <h2 class="font-unna font-bold text-3xl ">{{ __('ui.kap_news') }}</h2>
                    </a>
                </div>
                <div class="md:2xl:max-h-[630px] md:max-h-[510px] overflow-y-auto">
                    {{-- Referans ölçümü: 9 kart, kapsayıcı 628px (scrollHeight == height). --}}
                    @foreach ($kapDisclosures->take(9) as $disclosure)
                        <x-news-card :news="$disclosure" type="type1" />
                    @endforeach
                </div>
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
        <div class="bht-grid col-span-12 md:col-span-4" dataType="emtiaNews" datatype="emtiaNews">
            <section class="h-full" id="home-emtia" data-widget-type="emtiaNews">
                <div class="mb-4 border-b border-black relative before:absolute before:top-0 before:bottom-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black"></div>
                @php
                    $__en = $commodityNews->take(2)->values();
                @endphp
                <div class="w-full md:h-full flex flex-col justify-between">
                    @if ($__en->get(0))
                        <x-news-card :news="$__en->get(0)" type="type5" :title="__('ui.commodity_news')" :href="\App\Support\Locale::url('/emtia')" />
                    @endif
                    @if ($__en->get(1))
                        <x-news-card :news="$__en->get(1)" type="type2" />
                    @endif
                </div>
            </section>
        </div>
        <div class="bht-grid col-span-12 md:col-span-4" dataType="sectorNews" datatype="sectorNews">
            <section class="h-full" id="home-sector-news" data-widget-type="sectorNews">
                <div class="mb-4 border-b border-black relative before:absolute before:top-0 before:bottom-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black"></div>
                @php
                    $__sn = $sectorNews->take(3)->values();
                @endphp
                <div class="w-full md:h-full flex flex-col justify-between">
                    @if ($__sn->get(0))
                        <x-news-card :news="$__sn->get(0)" type="type1" :title="__('ui.sector_news')" :href="\App\Support\Locale::url('/haberler/sektor-haberleri')" />
                    @endif
                    @if ($__sn->get(1))
                        <x-news-card :news="$__sn->get(1)" type="type4" />
                    @endif
                    @if ($__sn->get(2))
                        <x-news-card :news="$__sn->get(2)" type="type5" />
                    @endif
                </div>
            </section>
        </div>
        <div class="bht-grid col-span-12 md:col-span-4" dataType="politicsNews" datatype="politicsNews">
            <section class="h-full" id="home-politics" data-widget-type="politicsNews">
                <div class="mb-4 border-b border-black relative before:absolute before:top-0 before:bottom-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black"></div>
                @php
                    $__pn = $politicsNews->take(3)->values();
                @endphp
                <div class="w-full md:h-full flex flex-col justify-between">
                    @if ($__pn->get(0))
                        <x-news-card :news="$__pn->get(0)" type="type5" :title="__('ui.politics_news')" :href="\App\Support\Locale::url('/haberler')" />
                    @endif
                    @if ($__pn->get(1))
                        <x-news-card :news="$__pn->get(1)" type="type2" />
                    @endif
                    @if ($__pn->get(2))
                        <x-news-card :news="$__pn->get(2)" type="type1" />
                    @endif
                </div>
                <a href="{{ \App\Support\Locale::url('/haberler') }}" data-type="link-type1" title="{{ __('ui.all') }}" class="w-full flex gap-2 my-2 items-center text-black bg-white">
                    <div class="flex-grow border-t border-gray-300 relative before:content-[''] before:absolute before:bottom-0 before:flex before:h-4 before:w-[1px] before:bg-gray-300"></div>
                    <span class="ml-2">
                        <span class="text-base mr-2">{{ __('ui.all') }}</span>
                        <span class="bloomberght-icon-font-chevron-right text-sm"></span>
                        <span class="bloomberght-icon-font-chevron-right text-sm"></span>
                        <span class="bloomberght-icon-font-chevron-right text-sm"></span>
                    </span>
                </a>
            </section>
        </div>
    </section>

    {{-- KRİPTO PARA — referans bloomberght.com crypto DOM'u ile birebir (CH-36/CH-37):
         siyah zemin, swiper kart şeridi, stats-bar-type1 kartlar, sağda ok butonları.
         Referans: yalnızca BTC/ETH/XRP detay sayfasına linklenir; diğerleri link'siz. --}}
    <section data-section="crypto" class="grid grid-cols-12 gap-4 mb-4 md:mb-10">
        <div class="bht-grid col-span-12 md:col-span-12 row-span-1" dataType="crypto">
            <div data-widget-type="crypto" class="relative py-8">
                <div data-type="background" class="absolute -z-10 left-1/2 top-0 w-[100vw] h-96 -translate-x-1/2 -mt-4 bg-cover bg-no-repeat bg-black h-full mt-0"></div>
                <a href="{{ \App\Support\Locale::url('/kripto') }}" title="{{ __('ui.crypto') }}" class="absolute top-4 font-unna font-bold text-3xl mb-2 text-white z-10"><h2>{{ __('ui.crypto') }}</h2></a>
                <div>
                    <div data-type="swiper" data-name="" class="swiper slider-custom relative pt-10 w-full"
                         data-swiper='{"slidesPerView":"auto","loop":false,"lazy":true,"navigation":{"nextEl":".crypto .swiper-button-next","prevEl":".crypto .swiper-button-prev"}}'>
                        <div class="swiper-wrapper">
                            @foreach ($cryptoQuotes as $quote)
                                @php
                                    $dir = $quote->direction();
                                    $bar = $dir === 'up' ? 'before:bg-green-700' : ($dir === 'down' ? 'before:bg-red-500' : '');
                                    // Referans: "% -0,36" biçimi — % öneki, eksi işareti, sondaki %-siz TR sayı
                                    $cpct = '% '.($quote->changePercent < 0 ? '-' : '').$quote->format(abs((float) $quote->changePercent), 2);
                                    // Referans: BTC/ETH/XRP → /doviz/<slug>; diğerleri link'siz
                                    $__cryptoTarget = $quote->instrument->cryptoUrl();
                                @endphp
                                <div class="swiper-slide w-auto pr-4">
                                    <div data-type="stats-bar-type1">
                                        @if ($__cryptoTarget)
                                            <a href="{{ $__cryptoTarget }}" title="{{ $quote->instrument->name }}" class="security-{{ \Illuminate\Support\Str::slug($quote->instrument->name) }} block cursor-pointer" data-type="crypto">
                                        @endif
                                        <div class="flex flex-col relative before:absolute before:top-0 before:left-0 before:w-1 before:h-full setColor {{ $bar }} text-white pl-2" data-market-code="{{ $quote->code() }}">
                                            <span class="text-xs text-ellipsis overflow-hidden whitespace-nowrap">{{ $quote->instrument->name }}</span>
                                            <span class="lastPrice font-bold" data-market-field="last">{{ $quote->format($quote->last) }}</span>
                                            <span class="percentChange text-xs" data-market-field="change_percent">{{ $cpct }}</span>
                                        </div>
                                        @if ($__cryptoTarget)
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="crypto text-black bg-white absolute -right-3 top-2 w-20 pl-4 z-90 text-white">
                            <button aria-label="prev" class="swiper-button-prev after:content-[''] w-8 h-11 bloomberght-icon-font-chevron-left right-14 cursor-pointer text-inherit"></button>
                            <button aria-label="next" class="swiper-button-next after:content-[''] w-8 h-11 bloomberght-icon-font-chevron-right right-0 cursor-pointer text-inherit"></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- REKLAM x2 --}}
    <x-ad-zone :zone="22801" :index="10" />
    <x-ad-zone :zone="22802" :index="11" />

    {{-- EN ÇOK OKUNANLAR — referans bloomberght.com mostRead DOM'u ile birebir (CH-37):
         3 sütunlu kart ızgarası (type5+type2), sağ üstte Bugün | Bu Hafta | Bu Ay pill'i. --}}
    <x-most-read variant="home"
                 :tabs="[
                     'today' => $mostReadTabs['today'],
                     'week' => $mostReadTabs['week'],
                     'month' => $mostReadTabs['month'],
                 ]" />

</div>
@endsection
