@extends('front.layouts.app')

@section('title', __('ui.opinions_title').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('ui.opinions_title').' — '.($siteSettings['site_tagline'] ?? ''))
@section('canonical', url(\App\Support\Locale::url('/gorus')))

@section('content')
    <div class="gorus-page">
        <div class="relative">
            {{-- 1. Arka plan (kaynakta bg-image-10) --}}
            <div data-type="background" class="absolute -z-10 left-1/2 top-0 w-[100vw] h-96 -translate-x-1/2 -mt-4 overflow-hidden" aria-hidden="true">
                <picture>
                    <source media="(max-width: 767px)" srcset="{{ asset('/img/bg/gorus-mobile.png') }}">
                    <img src="{{ asset('/img/bg/gorus-desktop.png') }}" alt="" role="presentation"
                         loading="eager" fetchpriority="high" decoding="async"
                         class="absolute inset-0 w-full h-full object-cover object-[center_top] max-md:object-contain max-md:object-[center_top]">
                </picture>
            </div>

            {{-- 2. head-type1 (kaynakta h1 "Görüş", head-type2 DEĞİL) --}}
            <div data-type="head-type1" class=" mb-2 ">
                <div class="relative before:absolute before:top-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black before:!m-0 before:h-[5px]"></div>
                <div class="mb-3 pt-4"><h1 class="font-unna font-bold text-3xl mb-2">{{ __('ui.opinions_title') }}</h1></div>
            </div>

            {{-- 3. Yazar kartlari grid'i — kaynak birebir:
                     grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4
                     Her kart: person-card-type2 (avatar + isim + en son yazi + TÜM YAZILAR). --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($authors as $author)
                    @php
                        // Referans her karta yazarın EN SON yazısını basıyor.
                        $latest = $author->getRelation('latestNews')->first();
                        $archiveUrl = \App\Http\Controllers\AuthorController::archiveUrl($author);
                    @endphp

                    <div data-widget-type="person-card-type2" class="h-[calc(100%-2rem)] md:border-r-2 border-gray-500 md:pr-4">
                        <figure class="person-card-wrapper relative flex items-center gap-4">
                            <div class="person-image w-24 h-full mb-4 aspect-square">
                                <img data-src="{{ $author->avatar ? asset($author->avatar) : asset('/img/placeholders/avatar-placeholder.svg') }}"
                                     src="{{ \App\Support\Media::transparentGif() }}"
                                     alt="" class="rounded-full object-fill h-full w-full " decoding="async">
                            </div>
                            <figcaption class="relative w-9/12 h-auto">
                                <a href="{{ $archiveUrl }}" title="{{ $author->name }}"
                                   class="person-name font-unna font-bold text-2xl text-ellipsis line-clamp-3 mb-2">{{ $author->name }}</a>
                                {{-- Referansta person-biography tüm kartlarda BOŞ. --}}
                                <div class="person-biography mb-2 text-sm text-ellipsis line-clamp-3"></div>
                            </figcaption>
                        </figure>

                        @if ($latest)
                            <a href="{{ $latest->url }}" class="block">
                                <div class="font-unna font-bold text-2xl text-ellipsis line-clamp-2 mb-2 min-h-16 flex flex-col justify-center">{{ $latest->title }}</div>
                                <div class="mb-2 text-base text-ellipsis line-clamp-3">{{ $latest->spot }}</div>
                            </a>
                        @endif

                        <a href="{{ $archiveUrl }}" title="{{ __('ui.all_writings') }}"
                           class="relative block w-full py-1 text-black bg-yellow-50 mt-4">
                            <span class="relative z-10 pr-2 bg-yellow-50">
                                <span class="text-sm mr-2">{{ __('ui.all_writings') }}</span>
                                <span class="bloomberght-icon-font-chevron-right text-xs"></span>
                                <span class="bloomberght-icon-font-chevron-right text-xs"></span>
                                <span class="bloomberght-icon-font-chevron-right text-xs "></span>
                            </span>
                            <span class="absolute top-3 left-0 border-b border-gray-400 w-full"></span>
                        </a>
                    </div>
                @empty
                    <p class="search-empty">{{ __('ui.no_authors') }}</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection