@extends('front.layouts.app')

@section('title', __('content.channel_zirve_news').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', $description ?: ($title.' '.__('ui.news_suffix')))
@section('canonical', url(\App\Support\Locale::url('/zirve')))

@section('content')
    <div class="zirve-page">

        {{-- 1. Arka plan görseli + head-type2 "Zirve" başlığı (referans birebir) --}}
        <div class="relative">
            <div data-type="background" class="absolute -z-10 left-1/2 top-0 w-[100vw] h-96 -translate-x-1/2 -mt-4 overflow-hidden" aria-hidden="true">
                <picture>
                    <source media="(max-width: 767px)" srcset="{{ asset('/img/bg/zirve-mobile.png') }}">
                    <img src="{{ asset('/img/bg/zirve-desktop.png') }}" alt="" role="presentation"
                         loading="eager" fetchpriority="high" decoding="async"
                         class="absolute inset-0 w-full h-full object-cover object-[center_top] max-md:object-contain max-md:object-[center_top]">
                </picture>
            </div>
            <div data-type="head-type2">
                <div class="relative before:absolute before:top-0 before:bottom-0 before:m-auto before:w-[230px] before:bg-black before:h-2.5 before:h-[5px] before:!m-0"></div>
                <div class="py-4">
                    <h2 class="font-unna font-bold text-2xl md:text-4xl">{{ $title }}</h2>
                </div>
            </div>
        </div>

        {{-- 2. Hero main-slider + 400px piyasa widget aside (referans birebir) --}}
        <div class="flex flex-wrap md:flex-nowrap gap-4">
            <div class="w-full md:w-[calc(100%-400px)] min-w-0">
                @if ($hero->isNotEmpty())
                    <div data-widget-type="main-slider">
                        <div data-type="swiper" data-name="" class="swiper slider-custom relative mb-4"
                             data-swiper='{"hashMode":true,"lazy":{"loadPrevNext":true,"loadPrevNextAmount":1,"loadOnTransitionStart":true},"loop":true,"pagination":{"el":".swiper-pagination","clickable":true},"navigation":{"nextEl":".main-slider .bloomberght-icon-font-chevron-right","prevEl":".main-slider .bloomberght-icon-font-chevron-left"}}'>
                            <div class="swiper-wrapper">
                                @foreach ($hero as $i => $item)
                                    @php
                                        $__heroImg = asset($item->image ?: '/img/og-default.png');
                                    @endphp
                                    <div class="swiper-slide " data-hash="{{ md5((string) $item->news_id) }}" style="">
                                        <div class="-z-100">
                                            <figure>
                                                <div class="w-full h-auto aspect-video bg-gray-50">
                                                    <a href="{{ $item->url }}" title="{{ $item->title }}">
                                                        @if ($i === 0)
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
                            <div class="main-slider text-black absolute left-0 top-32 3xl:top-44 h-6 w-full z-10 top-40 3xl:top-56 z-10 pointer-events-none">
                                <button aria-label="prev" class="swiper-button-prev pointer-events-auto after:content-[''] bg-black bg-opacity-40 text-white border-y border-r border-white w-9 h-9 bloomberght-icon-font-chevron-left left-0 cursor-pointer text-inherit hidden md:flex"></button>
                                <button aria-label="next" class="swiper-button-next pointer-events-auto after:content-[''] bg-black bg-opacity-40 text-white border-y border-l border-white w-9 h-9 bloomberght-icon-font-chevron-right right-0 cursor-pointer text-inherit hidden md:flex"></button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="w-full md:w-[400px] mb-4 md:mb-0">
                @isset($marketWidget)
                    {{-- Referans aside'de widget başlıksız gelir (doğrudan tab-menu). --}}
                    @include('partials.market-widget-compact', ['head' => false])
                @endisset
            </div>
        </div>

        {{-- 3. Videolar swiper — referans birebir --}}
        @if ($videos->isNotEmpty())
            <div class="relative">
                <div data-type="background" class="absolute -z-10 left-1/2 top-0 w-[100vw] h-96 -translate-x-1/2 -mt-4 bg-cover bg-no-repeat bg-gray-100 h-full mt-0"></div>
                <div class="mb-4 border-b border-black relative w-full before:absolute before:top-0 before:bottom-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black"></div>
                <a href="{{ \App\Support\Locale::url('/video') }}" title="{{ __('ui.video_news') }}"
                   class="absolute top-4 left-0 font-unna font-bold text-3xl mb-2 z-10">{{ __('ui.video_news') }}</a>
                <div data-type="swiper" data-name="" class="swiper slider-custom relative mb-4 pt-12 w-full max-h-96"
                     data-swiper='{"slidesPerView":"1","spaceBetween":0,"autoHeight":true,"breakpoints":{"@0.00":{"slidesPerView":1,"spaceBetween":0},"@0.75":{"slidesPerView":2.2,"spaceBetween":20},"@1.00":{"slidesPerView":3.2,"spaceBetween":20}},"lazy":true,"navigation":{"nextEl":".videos .swiper-button-next","prevEl":".videos .swiper-button-prev"}}'>
                    <div class="swiper-wrapper">
                        @foreach ($videos as $episode)
                            @php
                                $__series = $episode->program?->slug;
                                $__videoUrl = $__series
                                    ? \App\Support\Locale::url('/'.$__series.'/video/'.$episode->slug.'/'.$episode->id)
                                    : \App\Support\Locale::url('/tv');
                            @endphp
                            <div class="swiper-slide " style="">
                                <a href="{{ $__videoUrl }}" title="{{ $episode->display_title }}">
                                    <figure class="mb-4">
                                        <div class="mb-4 relative">
                                            <div class="w-full h-auto">
                                                <x-lazy-image :src="asset($episode->thumbnail ?: $episode->program?->thumbnail ?: '/img/og-default.png')"
                                                              alt="{{ $episode->display_title }}" class="swiper-lazy w-full h-full " />
                                            </div>
                                            <div class="absolute right-0 bottom-0">
                                                <div class="bloomberght-icon-font-play bg-white text-black rounded-tl-lg flex justify-center items-center w-14 h-14 mb-[1px]"></div>
                                                <div class="flex justify-center items-center bg-white text-black w-14 h-7 text-xs">{{ $episode->duration }}</div>
                                            </div>
                                        </div>
                                        <figcaption>
                                            <div class="font-unna font-bold text-xl text-ellipsis line-clamp-3 mb-1">{{ $episode->display_title }}</div>
                                        </figcaption>
                                    </figure>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <div class="videos text-black bg-white absolute -right-3 top-4 w-20 pl-4 z-90">
                        <button aria-label="prev" class="swiper-button-prev after:content-[''] w-8 h-11 bloomberght-icon-font-chevron-left right-14 cursor-pointer text-inherit"></button>
                        <button aria-label="next" class="swiper-button-next after:content-[''] w-8 h-11 bloomberght-icon-font-chevron-right right-0 cursor-pointer text-inherit"></button>
                    </div>
                </div>
            </div>
        @endif

        {{-- 4. Zirve Haberleri — 3 kolon grid, referans birebir.
             Kolon kart tipleri — 1. type5 type2 type1 type4
                                2. type1 type4 type5 type2
                                3. type5 type2 type1 type4 --}}
        <div data-type="head-type1" class="mb-8">
            <div class="relative before:absolute before:top-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black before:bottom-0 before:h-2.5 border-b border-black mb-4"></div>
            <div class="mb-3">
                <h2 class="font-unna font-bold text-3xl mb-8">{{ __('content.channel_zirve_news') }}</h2>
            </div>
        </div>

        @php
            $columnTypes = [
                ['type5', 'type2', 'type1', 'type4'],
                ['type1', 'type4', 'type5', 'type2'],
                ['type5', 'type2', 'type1', 'type4'],
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 md:gap-4 mb-4" data-load-more-container>
            @foreach ($columnTypes as $column => $types)
                <div class="flex flex-col justify-between md:gap-4">
                    @foreach ($types as $row => $cardType)
                        @php
                            $item = $news->get($column * 4 + $row);
                        @endphp
                        @if ($item)
                            {{-- type5 figcaption'ında kategori satırı referansta yok --}}
                            <x-news-card :news="$item" :type="$cardType"
                                         :showSpot="false" :showCategory="$cardType !== 'type5'"
                                         :wrapClass="$cardType === 'type5' ? '' : 'border-b mb-2 pb-2'"
                                         contract="news_card_square" />
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>

        @if ($news->hasMorePages())
            <div class="load-more-wrap">
                <a class="load-more" href="{{ $news->nextPageUrl() }}"
                   data-load-more data-next-url="{{ $news->nextPageUrl() }}"
                   data-container="[data-load-more-container]">{{ __('ui.list') }}</a>
            </div>
        @endif
    </div>
@endsection