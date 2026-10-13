@extends('front.layouts.app')

@section('title', $title.' '. __('ui.news_suffix').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', $description ?: ($title.' '. __('ui.news_suffix')))
@section('canonical', url(\App\Support\Locale::url('/tarim')))

@section('content')
    <div class="tarim-page">

        {{-- Hero: arka plan görseli + head-type2 "Tarım" (referans birebir) --}}
        <div class="relative">
            <div data-type="background" class="absolute -z-10 left-1/2 top-0 w-[100vw] h-96 -translate-x-1/2 -mt-4 overflow-hidden" aria-hidden="true">
                <picture>
                    <source media="(max-width: 767px)" srcset="{{ asset('img/bg/tarim-mobile.png') }}">
                    <img src="{{ asset('img/bg/tarim-desktop.png') }}" alt="" role="presentation"
                         loading="eager" fetchpriority="high" decoding="async"
                         class="absolute inset-0 w-full h-full object-cover object-[center_top] max-md:object-contain max-md:object-[center_top]">
                </picture>
            </div>
            <div data-type="head-type2">
                <div class="relative before:absolute before:top-0 before:bottom-0 before:m-auto before:w-[230px] before:bg-black before:h-[5px] before:!m-0"></div>
                <div class="py-4">
                    <h2 class="font-unna font-bold text-2xl md:text-4xl">{{ $title }}</h2>
                </div>
            </div>
        </div>

        {{-- 2 kolon: ana (haber hero main-slider) + aside 400px (Tarım Analiz köşesi) --}}
        <div class="flex flex-wrap md:flex-nowrap gap-4">
            <div class="w-full md:w-[calc(100%-400px)] min-w-0">
                @if ($hero->isNotEmpty())
                    <div data-widget-type="main-slider">
                        <div data-type="swiper" data-name="" class="swiper slider-custom relative mb-4"
                             data-swiper='{"hashMode":true,"lazy":{"loadPrevNext":true,"loadPrevNextAmount":1,"loadOnTransitionStart":true},"loop":true,"pagination":{"el":".swiper-pagination","clickable":true},"navigation":{"nextEl":".main-slider .bloomberght-icon-font-chevron-right","prevEl":".main-slider .bloomberght-icon-font-chevron-left"}}'>
                            <div class="swiper-wrapper">
                                @foreach ($hero as $i => $item)
                                    @php($__heroImg = asset($item->image ?: '/img/og-default.png'))
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
                                <button aria-label="prev" class="swiper-button-prev pointer-events-auto after:content-[''] bg-black bg-opacity-40 text-white border-y border-r border-white w-9 h-9 bloomberght-icon-font-chevron-left left-0 cursor-pointer text-inherit hidden md:flex"><svg class="swiper-navigation-icon" width="11" height="20" viewBox="0 0 11 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.38296 20.0762C0.111788 19.805 0.111788 19.3654 0.38296 19.0942L9.19758 10.2796L0.38296 1.46497C0.111788 1.19379 0.111788 0.754138 0.38296 0.482966C0.654131 0.211794 1.09379 0.211794 1.36496 0.482966L10.4341 9.55214C10.8359 9.9539 10.8359 10.6053 10.4341 11.007L1.36496 20.0762C1.09379 20.3474 0.654131 20.3474 0.38296 20.0762Z" fill="currentColor"></path></svg></button>
                                <button aria-label="next" class="swiper-button-next pointer-events-auto after:content-[''] bg-black bg-opacity-40 text-white border-y border-l border-white w-9 h-9 bloomberght-icon-font-chevron-right right-0 cursor-pointer text-inherit hidden md:flex"><svg class="swiper-navigation-icon" width="11" height="20" viewBox="0 0 11 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.38296 20.0762C0.111788 19.805 0.111788 19.3654 0.38296 19.0942L9.19758 10.2796L0.38296 1.46497C0.111788 1.19379 0.111788 0.754138 0.38296 0.482966C0.654131 0.211794 1.09379 0.211794 1.36496 0.482966L10.4341 9.55214C10.8359 9.9539 10.8359 10.6053 10.4341 11.007L1.36496 20.0762C1.09379 20.3474 0.654131 20.3474 0.38296 20.0762Z" fill="currentColor"></path></svg></button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="w-full md:w-[400px] mb-4 md:mb-0">
                <div class="bg-yellow-50 relative">
                    <div class="mb-4 border-b border-black relative w-full before:absolute before:top-0 before:bottom-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black"></div>
                    <h2 class="font-unna font-bold text-3xl px-4">{{ __('content.channel_tarim_analiz') }}</h2>

                    {{-- Kart yapısı referans birebir: yazar kartı (avatar + isim) YALNIZCA
                         ilk kartta görünür; sonraki kartlarda <figure> boş kalır, sadece
                         yazı başlığı listelenir. person-biography referansta BOŞ'tur. --}}
                    @forelse ($authorOpinions as $opIndex => $item)
                        <div data-widget-type="person-card-type2" class="px-4{{ $loop->last ? '' : ' border-b mb-4 pb-2 border-black' }}">
                            <figure class="person-card-wrapper relative flex items-center gap-4">
                                @if ($loop->first)
                                    <div class="person-image w-24 h-full mb-4 aspect-square">
                                        <img data-src="{{ $author?->avatar ? asset($author->avatar) : asset('/img/placeholders/avatar-placeholder.svg') }}"
                                             src="{{ \App\Support\Media::transparentGif() }}"
                                             alt="{{ $item->title }}"
                                             class="rounded-full object-fill h-full w-full " decoding="async">
                                    </div>
                                    <figcaption class="relative w-9/12 h-auto">
                                        <div class="person-name font-unna font-bold text-2xl text-ellipsis line-clamp-3 mb-2">{{ $author?->name }}</div>
                                        <div class="person-biography mb-2 text-sm text-ellipsis line-clamp-3"></div>
                                    </figcaption>
                                @endif
                            </figure>
                            <a href="{{ $item->url }}" class="block">
                                <div class="font-unna font-bold text-2xl text-ellipsis line-clamp-2 mb-2 min-h-16 flex flex-col justify-center">{{ $item->title }}</div>
                                <div class="mb-2 text-base text-ellipsis line-clamp-3">{{ \Illuminate\Support\Str::limit(strip_tags((string) $item->spot), 170) }}</div>
                            </a>
                            {{-- Son kartın sonunda yazar arşivi bağlantısı — referans birebir.
                                 Üç chevron span'i kaynaktaki hover ok animasyonunun parçası. --}}
                            @if ($loop->last && $author)
                                <a href="{{ \App\Http\Controllers\AuthorController::archiveUrl($author) }}"
                                   title="{{ __('ui.all_writings') }}"
                                   class="relative block w-full py-1 text-black bg-yellow-50 mt-4">
                                    <span class="relative z-10 pr-2 bg-yellow-50">
                                        <span class="text-sm mr-2">{{ __('ui.all_writings') }}</span>
                                        <span class="bloomberght-icon-font-chevron-right text-xs"></span>
                                        <span class="bloomberght-icon-font-chevron-right text-xs"></span>
                                        <span class="bloomberght-icon-font-chevron-right text-xs "></span>
                                    </span>
                                    <span class="absolute top-3 left-0 border-b border-gray-400 w-full"></span>
                                </a>
                            @endif
                        </div>
                    @empty
                        <p class="px-4 py-4 text-gray-400">{{ __('ui.no_news') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Tarım Haberleri — 3 kolonlu grid + Listele lazy-load --}}
        <div data-type="head-type1" class="mb-8">
            <div class="relative before:absolute before:top-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black before:bottom-0 before:h-2.5 border-b border-black mb-4"></div>
            <div class="mb-3">
                <h2 class="font-unna font-bold text-3xl">{{ __('content.channel_tarim_news') }}</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 md:gap-4 mb-4" data-load-more-container>
            @forelse ($news as $i => $item)
                @if ($i === 0)
                    <x-news-card :news="$item" type="type5" :showSpot="true" :eager="true" contract="news_card_square" />
                @else
                    <x-news-card :news="$item" :type="$i % 4 === 0 ? 'type1' : 'type2'" :showSpot="$i % 4 === 1" contract="news_card_square" />
                @endif
            @empty
                <p class="search-empty col-span-3">{{ __('ui.no_news') }}</p>
            @endforelse
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
