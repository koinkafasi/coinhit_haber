@extends('front.layouts.app')

@section('title', __('media.video').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('media.video_index_desc'))
@section('canonical', url(\App\Support\Locale::url('/video')))

@section('content')
    <div class="video-page">

        {{-- Hero: mutlak konumlu arka plan görseli + head-type2 "Video" (referans birebir) --}}
        <div class="relative">
            <div data-type="background" class="absolute -z-10 left-1/2 top-0 w-[100vw] h-96 -translate-x-1/2 -mt-4 overflow-hidden" aria-hidden="true">
                <picture>
                    <source media="(max-width: 767px)" srcset="{{ asset('img/bg/video-mobile.png') }}">
                    <img src="{{ asset('img/bg/video-desktop.png') }}" alt="" role="presentation"
                         loading="eager" fetchpriority="high" decoding="async"
                         class="absolute inset-0 w-full h-full object-cover object-[center_top] max-md:object-contain max-md:object-[center_top]">
                </picture>
            </div>
            <div data-type="head-type2">
                <div class="relative before:absolute before:top-0 before:bottom-0 before:m-auto before:w-[230px] before:bg-black before:h-[5px] before:!m-0"></div>
                <div class="py-4">
                    <h2 class="font-unna font-bold text-2xl md:text-4xl">{{ __('media.video') }}</h2>
                </div>
            </div>
        </div>

        {{-- Seri filtre hapları — yalnız referanstaki 8 seri, yatay swiper --}}
        <div data-type="swiper" data-name="" class="swiper slider-custom relative mb-4 w-full" data-swiper='{"slidesPerView":"auto","spaceBetween":10}'>
            <div class="swiper-wrapper">
                @foreach ($filter as $item)
                    <div class="swiper-slide w-auto">
                        <a href="{{ $item['url'] }}" title="{{ $item['name'] }}" class="inline-block px-4 py-2 rounded-3xl border bg-white">{{ $item['name'] }}</a>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Seri bölümleri: kapsamlı video + 3 kenar video + TÜMÜ linki --}}
        @foreach ($series as $item)
            @php($__eps = $item['episodes'])
            <div class="relative my-8">
                <div data-type="head-type1">
                    <div class="relative before:absolute before:top-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black before:bottom-0 before:h-2.5 border-b border-black mb-4"></div>
                    <a href="{{ $item['url'] }}" title="{{ $item['name'] }}" class="inline-block mb-3">
                        <h2 class="font-unna font-bold text-3xl">{{ $item['name'] }}</h2>
                    </a>
                </div>

                <div class="w-full flex flex-wrap md:flex-nowrap gap-4 text-black">
                    <div class="w-full md:w-3/5">
                        @if ($__eps->get(0))
                            @include('video.partials.card-type1', ['episode' => $__eps[0]])
                        @endif
                    </div>
                    <div class="w-full md:w-2/5">
                        @foreach ($__eps->slice(1, 3) as $__ep)
                            @include('video.partials.card-type2', ['episode' => $__ep, 'last' => $loop->last])
                        @endforeach
                    </div>
                </div>

                {{-- TÜMÜ — link-type1: çizgili ayraç + "TÜMÜ" + 3 chevron (referans: mt yok) --}}
                <a href="{{ $item['url'] }}" data-type="link-type1" title="{{ __('ui.all') }}"
                   class="w-full flex gap-2 items-center text-black bg-transparent">
                    <div class="flex-grow border-t border-gray-300"></div>
                    <span class="ml-2">
                        <span class="text-base mr-2">{{ __('ui.all') }}</span>
                        <span class="bloomberght-icon-font-chevron-right text-sm"></span>
                        <span class="bloomberght-icon-font-chevron-right text-sm"></span>
                        <span class="bloomberght-icon-font-chevron-right text-sm"></span>
                    </span>
                </a>
            </div>
        @endforeach
    </div>
@endsection
