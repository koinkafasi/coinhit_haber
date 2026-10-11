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
<div data-widget-type="nav-sub-menu" class="bg-[#384552] h-12 px-4 hidden lg:block">
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
