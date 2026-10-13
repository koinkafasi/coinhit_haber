@php
    $settings = $siteSettings ?? [];
    $breakingList = $breakingList ?? collect();
    $mega = config('navigation.mega', []);
    $newWindow = 'target="_blank" rel="noopener noreferrer"';
@endphp

{{-- ============================================================
     CH-24 birebir referans header: stickly üst bar (logo + eylemler).
     Sınıf deseni https://www.bloomberght.com ile birebir.
     ============================================================ --}}
<header class="sticky top-0 md:relative z-600 w-full h-full inset-x-0 bg-[#384552]">
    <div class="relative container mx-auto flex justify-between items-center py-4">
        <a href="{{ \App\Support\Locale::route('home') }}" title="Bloomberght" class="block" aria-label="{{ $settings['logo_alt'] ?? 'Bloomberg HT' }}">
            <img src="{{ asset($settings['logo_white_path'] ?? '/brand/logo-white.svg') }}" alt="{{ $settings['logo_alt'] ?? 'Bloomberg HT' }}"
                 class="w-auto h-8 sm:h-12 md:h-16" width="250" height="64">
        </a>

        <div class="flex items-center gap-4 sm:gap-5">
            {{-- Canlı Yayın --}}
            <a href="{{ \App\Support\Locale::url('/tv') }}" title="{{ __('ui.live') }}"
               class="w-auto h-8 px-3 sm:px-4 py-1 bg-white rounded-3xl flex items-center justify-center gap-2 text-sm sm:text-md">
                <span class="bloomberght-icon-font-play ml-1"></span>
                <div class="flex gap-1">
                    <span>{{ __('ui.live_word') }}</span>
                    <span class="hidden sm:block">{{ __('ui.broadcast_word') }}</span>
                </div>
            </a>

            {{-- Radyo --}}
            <a href="https://www.bloomberghtradyo.com" target="_blank" rel="noopener noreferrer" title="Bloomberg HT Radyo"
               class="sm:w-auto w-8 h-8 sm:px-4 py-1 text-white text-sm sm:text-md bg-[#384552] border border-white rounded-full sm:rounded-3xl hidden sm:flex items-center justify-center gap-2">
                <span class="bloomberght-icon-font-radio"></span>
                <span class="hidden sm:block">{{ __('ui.radio') }}</span>
            </a>

            {{-- Arama --}}
            <button type="button" aria-label="Search" class="search-widget-trigger bloomberght-icon-font-search text-lg text-white cursor-pointer"></button>

            {{-- Hamburger / Mega menü --}}
            <button type="button" aria-label="Mega-menu" class="menu-button bloomberght-icon-font-menu text-lg text-white cursor-pointer"></button>
        </div>
    </div>
</header>

{{-- ============================================================
     MEGA NAV (CH-24: referans nav-sub-menu, desktop lg+ görünür;
     mobilde hamurger menü içine taşınır)
     ============================================================ --}}
<div data-widget-type="nav-sub-menu" class="bg-[#384552] h-12 hidden lg:block">
    <div class="nav-sub-menu container h-full mx-auto flex items-center justify-between z-90 whitespace-nowrap">
        @foreach ($mega as $item)
            @php($hasChildren = ! empty($item['children']))
            <div class="sub-menu-wrapper relative text-sm lg:text-base text-white group h-full">
                @if (! empty($item['logo']))
                    <a href="{{ \App\Support\Locale::navUrl($item['url']) }}"
                       title="{{ \App\Support\Locale::navLabel($item['label']) }}"
                       class="h-full flex items-center justify-center font-medium"
                       @if (! empty($item['external'])) {!! $newWindow !!} @endif>
                        <span class="rounded-full px-3 pt-2 pb-2.5 border border-gray-500">
                            <img class="block max-h-3.5" src="{{ asset('/brand/'.$item['logo']) }}" alt="{{ \App\Support\Locale::navLabel($item['label']) }}" loading="lazy" width="107" height="14">
                        </span>
                    </a>
                @else
                    <a href="{{ \App\Support\Locale::navUrl($item['url']) }}"
                       title="{{ \App\Support\Locale::navLabel($item['label']) }}"
                       class="{{ $loop->first ? 'pl-0 ' : '' }}py-2.5 px-1.5 h-full flex items-center justify-center gap-1 font-medium group-hover:bg-[#4D596A] group-hover:font-medium hover:font-medium"
                       @if (! empty($item['external'])) {!! $newWindow !!} @endif>
                        <span>{{ \App\Support\Locale::navLabel($item['label']) }}</span>
                        @if ($hasChildren)
                            <span class="bloomberght-icon-font-chevron-bottom text-[.5rem] text-white"></span>
                        @endif
                    </a>
                @endif
                @if ($hasChildren)
                    <ul class="drop-down absolute top-12 left-0 bg-[#4D596A] z-90 text-sm w-max hidden">
                        @foreach ($item['children'] as $child)
                            <li class="hover:bg-[#384552] border-b border-[#384552]">
                                <a href="{{ \App\Support\Locale::navUrl($child['url']) }}"
                                   title="{{ \App\Support\Locale::navLabel($child['label']) }}"
                                   class="block p-4">{{ \App\Support\Locale::navLabel($child['label']) }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endforeach
    </div>
</div>

{{-- ============================================================
     SON DAKİKA şeridi (CH-24: referans yapısı, marquee korunur)
     ============================================================ --}}
<div data-widget-type="breaking-news" class="w-full bg-[#bd1b2d] relative flex items-center h-16 md:h-14">
    <div class="container mx-auto flex items-center gap-4 text-white leading-none md:leading-normal">
        <a href="{{ \App\Support\Locale::route('breaking.index') }}" title="SON DAKİKA"
           class="w-auto md:whitespace-nowrap text-xl font-unna font-bold leading-none">{{ __('ui.breaking') }}</a>
        <div class="w-4 h-4 relative flex">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-4 w-4 bg-[#a00207]"></span>
        </div>
        <div data-type="swiper" data-name=""
             class="swiper slider-custom relative 2xl:max-w-screen-xl mx-auto max-w-screen-lg flex items-center z-10"
             data-swiper='{"slidesPerView":"auto","autoplay":{"delay":6000,"disableOnInteraction":false},"spaceBetween":10,"navigation":{"nextEl":".breaking-news .bloomberght-icon-font-chevron-right","prevEl":".breaking-news .bloomberght-icon-font-chevron-left"}}'>
            <div class="swiper-wrapper">
                @forelse ($breakingList as $breaking)
                    <div class="swiper-slide md:pr-20 self-center">
                        <a href="{{ \App\Support\Locale::route('breaking.index') }}"
                           class="text-xs md:text-sm font-normal text-ellipsis line-clamp-3 md:line-clamp-1 cursor-pointer">{{ $breaking->title }}</a>
                    </div>
                @empty
                    <div class="swiper-slide self-center">
                        <span class="text-xs md:text-sm text-white/80">{{ __('ui.no_breaking') }}</span>
                    </div>
                @endforelse
            </div>
            <button type="button" aria-label="prev" class="swiper-button-prev after:content-[''] w-8 h-11 bloomberght-icon-font-chevron-left right-14 cursor-pointer text-inherit">
                <svg class="swiper-navigation-icon" width="11" height="20" viewBox="0 0 11 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.38296 20.0762C0.111788 19.805 0.111788 19.3654 0.38296 19.0942L9.19758 10.2796L0.38296 1.46497C0.111788 1.19379 0.111788 0.754138 0.38296 0.482966C0.654131 0.211794 1.09379 0.211794 1.36496 0.482966L10.4341 9.55214C10.8359 9.9539 10.8359 10.6053 10.4341 11.007L1.36496 20.0762C1.09379 20.3474 0.654131 20.3474 0.38296 20.0762Z" fill="currentColor"></path></svg>
            </button>
            <button type="button" aria-label="next" class="swiper-button-next after:content-[''] w-8 h-11 bloomberght-icon-font-chevron-right right-0 cursor-pointer text-inherit">
                <svg class="swiper-navigation-icon" width="11" height="20" viewBox="0 0 11 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.38296 20.0762C0.111788 19.805 0.111788 19.3654 0.38296 19.0942L9.19758 10.2796L0.38296 1.46497C0.111788 1.19379 0.111788 0.754138 0.38296 0.482966C0.654131 0.211794 1.09379 0.211794 1.36496 0.482966L10.4341 9.55214C10.8359 9.9539 10.8359 10.6053 10.4341 11.007L1.36496 20.0762C1.09379 20.3474 0.654131 20.3474 0.38296 20.0762Z" fill="currentColor"></path></svg>
            </button>
        </div>
    </div>
</div>

{{-- Piyasa ticker (Borsa | Döviz | Emtia | Faiz) --}}
@include('front.partials.finance-slider')
