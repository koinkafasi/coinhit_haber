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
<header class="sticky top-0 md:relative z-600 w-full h-full px-4 inset-x-0 bg-[#384552]">
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
<div data-widget-type="nav-sub-menu" class="bg-[#384552] h-12 px-4 !hidden lg:!block">
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
<div data-widget-type="breaking-news" class="w-full bg-[#bd1b2d] relative flex items-center px-4 h-16 md:h-14">
    <div class="flex items-center gap-4 text-white container mx-auto leading-none md:leading-normal">
        <a href="{{ \App\Support\Locale::route('breaking.index') }}" title="SON DAKİKA"
           class="w-auto md:whitespace-nowrap text-xl font-unna font-bold leading-none">{{ __('ui.breaking') }}</a>
        <div class="w-4 h-4 relative flex">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-4 w-4 bg-[#a00207]"></span>
        </div>
        <div class="breaking-ticker bht-breaking-ticker min-w-0 flex-1 overflow-hidden" data-ticker>
            @if ($breakingList->isNotEmpty())
                <div class="breaking-track">
                    @foreach ($breakingList as $breaking)
                        <a href="{{ \App\Support\Locale::route('breaking.index') }}">
                            <span class="ticker-time">{{ $breaking->published_at?->format('H:i') }}</span>
                            {{ $breaking->title }}
                        </a>
                    @endforeach
                    @foreach ($breakingList as $breaking)
                        <a href="{{ \App\Support\Locale::route('breaking.index') }}" aria-hidden="true" tabindex="-1">
                            <span class="ticker-time">{{ $breaking->published_at?->format('H:i') }}</span>
                            {{ $breaking->title }}
                        </a>
                    @endforeach
                </div>
            @else
                <span class="ticker-empty">{{ __('ui.no_breaking') }}</span>
            @endif
        </div>
    </div>
</div>

{{-- Piyasa ticker (Borsa | Döviz | Emtia | Faiz) --}}
@include('front.partials.finance-slider')

{{-- ============================================================
     MOBİL MEGA MENÜ paneli (hamburger açılır; dil değiştirici buraya
     taşındı — CH-24 kuralı)
     ============================================================ --}}
<div class="mega-menu-panel bht-mega-menu-panel" data-mega-menu-panel hidden>
    <div class="mega-menu-panel-inner">
        <div class="mega-menu-head flex items-center justify-between px-4 py-3 bg-[#384552] text-white">
            <span class="font-bold text-base">{{ __('ui.menu') }}</span>
            <button type="button" class="mega-menu-close text-2xl leading-none" data-menu-close aria-label="{{ __('ui.close') }}">&times;</button>
        </div>

        <div class="mega-menu-body">
            <div class="px-4 py-3">
                @include('front.partials.language-switcher')
            </div>

            <form class="mega-menu-search flex items-center gap-2 px-4 py-3 border-b border-[#707070]" action="{{ \App\Support\Locale::route('search.index') }}" method="get" role="search" data-search-form>
                <input class="flex-1 min-w-0 border border-[#707070] rounded-full px-4 py-2 text-sm" type="search" name="q" autocomplete="off" placeholder="{{ __('ui.search_placeholder') }}">
                <button type="submit" class="bg-[#FFE400] text-black rounded-full px-4 py-2 text-sm font-bold">{{ __('ui.list') }}</button>
            </form>

            <nav class="mega-menu-nav" aria-label="{{ __('ui.main_nav') }}">
                @foreach ($mega as $item)
                    @php($hasChildren = ! empty($item['children']))
                    <div class="mega-menu-item border-b border-[#707070]">
                        <div class="mega-menu-link-row flex items-center justify-between">
                            <a href="{{ \App\Support\Locale::navUrl($item['url']) }}" class="mega-menu-link flex-1 block px-4 py-3 text-sm font-medium text-white"
                               @if (! empty($item['external'])) {!! $newWindow !!} @endif>{{ \App\Support\Locale::navLabel($item['label']) }}</a>
                            @if ($hasChildren)
                                <button type="button" class="mega-menu-caret px-4 py-3 text-white bloomberght-icon-font-chevron-bottom" data-menu-caret aria-expanded="false" aria-label="{{ __('ui.menu') }}"></button>
                            @endif
                        </div>
                        @if ($hasChildren)
                            <ul class="mega-menu-children hidden bg-[#4D596A]">
                                @foreach ($item['children'] as $child)
                                    <li>
                                        <a href="{{ \App\Support\Locale::navUrl($child['url']) }}" class="block px-6 py-3 text-sm text-white border-b border-[#384552]">{{ \App\Support\Locale::navLabel($child['label']) }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endforeach
            </nav>
        </div>
    </div>
</div>

{{-- ============================================================
     ARAMA widget overlay (search-widget-trigger açar)
     ============================================================ --}}
<div class="search-widget-overlay" data-search-overlay hidden>
    <div class="search-widget-box">
        <div class="flex items-center justify-between mb-4">
            <span class="font-bold text-lg">{{ __('ui.search') }}</span>
            <button type="button" class="search-widget-close text-2xl leading-none" data-search-close aria-label="{{ __('ui.close') }}">&times;</button>
        </div>
        <form action="{{ \App\Support\Locale::route('search.index') }}" method="get" role="search" data-search-form>
            <label class="sr-only" for="header-search-input">{{ __('ui.search') }}</label>
            <input id="header-search-input" class="w-full border border-gray-300 rounded-full px-4 py-2 text-sm" type="search" name="q" autocomplete="off" placeholder="{{ __('ui.search_placeholder') }}">
            <button type="submit" class="mt-3 w-full bg-[#FFE400] text-black rounded-full px-4 py-2 text-sm font-bold">{{ __('ui.list') }}</button>
        </form>
    </div>
</div>
