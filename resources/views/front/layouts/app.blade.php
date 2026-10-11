<!doctype html>
<html lang="{{ app()->getLocale() }}" class="bht-html">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#384552">

    <title>@yield('title', ($siteSettings['site_name'] ?? 'Bloomberg HT').' | '.__('ui.site_tagline'))</title>
    <meta name="description" content="@yield('description', $siteSettings['site_tagline'] ?? '')">
    @hasSection('robots')
        <meta name="robots" content="@yield('robots')">
    @endif

    <link rel="canonical" href="@yield('canonical', url()->current())">
    @foreach (config('locales.supported', []) as $__code => $__meta)
        <link rel="alternate" hreflang="{{ $__code }}" href="{{ url(\App\Support\Locale::switchUrl($__code)) }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ url(\App\Support\Locale::switchUrl(\App\Support\Locale::default())) }}">
    <link rel="icon" href="{{ asset($siteSettings['favicon_path'] ?? '/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset($siteSettings['apple_touch_icon'] ?? '/apple-touch-icon.png') }}">
    <link rel="alternate" type="application/rss+xml" title="Bloomberg HT RSS" href="{{ url('/rss') }}">

    {{-- Open Graph (OG görseli 960x540, Bölüm 1 / 11.1) --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ $siteSettings['site_name'] ?? 'Bloomberg HT' }}">
    <meta property="og:locale" content="{{ \App\Support\Locale::ogLocale() }}">
    @foreach (\App\Support\Locale::alternates() as $__alt)
        <meta property="og:locale:alternate" content="{{ \App\Support\Locale::ogLocale($__alt) }}">
    @endforeach
    <meta property="og:url" content="@yield('canonical', url()->current())">
    @hasSection('og_title')
        <meta property="og:title" content="@yield('og_title')">
    @else
        <meta property="og:title" content="@yield('title', $siteSettings['site_name'] ?? 'Bloomberg HT')">
    @endif
    @hasSection('og_description')
        <meta property="og:description" content="@yield('og_description')">
    @else
        <meta property="og:description" content="@yield('description', $siteSettings['site_tagline'] ?? '')">
    @endif
    <meta property="og:image" content="@yield('og_image', url($siteSettings['default_og_image'] ?? '/img/og-default.png'))">
    <meta property="og:image:width" content="@yield('og_image_width', '960')">
    <meta property="og:image:height" content="@yield('og_image_height', '540')">

    {{-- Twitter Card (summary_large_image, @bloomberght) --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@bloomberght">
    @hasSection('og_title')
        <meta name="twitter:title" content="@yield('og_title')">
    @else
        <meta name="twitter:title" content="@yield('title', $siteSettings['site_name'] ?? 'Bloomberg HT')">
    @endif
    @hasSection('og_description')
        <meta name="twitter:description" content="@yield('og_description')">
    @else
        <meta name="twitter:description" content="@yield('description', $siteSettings['site_tagline'] ?? '')">
    @endif
    <meta name="twitter:image" content="@yield('og_image', url($siteSettings['default_og_image'] ?? '/img/og-default.png'))">

    @php($__cssVersion = @filemtime(public_path('css/app.css')) ?: 1)
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ $__cssVersion }}">
    @php($__swiperCss = @filemtime(public_path('css/swiper-bundle.min.css')) ?: 1)
    <link rel="stylesheet" href="{{ asset('css/swiper-bundle.min.css') }}?v={{ $__swiperCss }}">
    @include('front.partials.jsonld')
    @stack('jsonld')
    @include('front.partials.analytics')
    @yield('head')
</head>
<body class="bht-body relative w-full min-w-xs text-base overflow-x-hidden @yield('body_class')">
    {{-- Google Tag Manager (noscript) --}}
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5XBP88"
                      height="0" width="0" style="display:none;visibility:hidden" title="GTM"></iframe></noscript>

    @hasSection('masthead')
        <div class="masthead-zone" data-type="masthead">
            @yield('masthead')
        </div>
    @endif

    @include('front.partials.header')

    {{-- CH-24: referans main yapısı — p-4 + master + pageskin sol/sağ --}}
    <main class="p-4">
        <div class="master w-full flex justify-center items-start container mx-auto">
            {{-- Pageskin sol reklam (zone_1252) --}}
            <div data-adv="bht" data-device="DESKTOP" id="page-skin-left-sticky"
                 class="pageskin page-skin-left pageskin-left hidden -ml-[160px] mr-4 justify-end sticky top-[212px] min-w-[160px] h-auto min-h-[600px] text-gray-400 z-[50]">
                <div id="zone_1252" data-zone-id="1252" class="no-init" data-zone-viewtype="banner"></div>
            </div>

            <div class="w-full min-w-full">
                <div class="mainpage-content">
                    @yield('content')
                </div>
            </div>

            {{-- Pageskin sağ reklam (zone_1251) --}}
            <div data-adv="bht" data-device="DESKTOP" id="page-skin-right-sticky"
                 class="pageskin page-skin-right pageskin-right -mr-[160px] ml-4 hidden justify-start sticky top-[212px] min-w-[160px] h-auto min-h-[600px] text-gray-400 z-[50]">
                <div id="zone_1251" data-zone-id="1251" class="no-init" data-zone-viewtype="banner"></div>
            </div>
        </div>
    </main>

    @include('front.partials.footer')
    @include('front.partials.cookie')

    {{-- Mobil sticky alt bant (zone 2280) — Bölüm 2.5 / 14.6 --}}
    <div class="widget-sticky-ads mobile-sticky-ad" data-type="stickyAds" data-device="MOBILE"
         id="zone_auto_refresh_sticky" data-adv="bht" data-zone-id="zone_2280">
        <div class="ads-zone lazy-init" data-adv="bht" data-device="MOBILE" data-zone-id="zone_2280"></div>
    </div>

    {{-- Arama modalı (referans search-modal — body sonunda, mega-menu öncesi) --}}
    <div class="search-widget-overlay" data-widget-type="search-bar" data-search-overlay hidden>
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

    {{-- Hamburger mega menü modalı (referans widgetMegaMenu — body sonunda, bottom-nav öncesi) --}}
    @include('front.partials.mega-menu')

    {{-- Mobil alt navigasyon (referans widgetBottomNavigation, md+ gizli) --}}
    <div data-widget-type="widgetBottomNavigation" class="container fixed bottom-0 z-[450] bg-white px-4 py-2 border inset-x-0 m-auto md:hidden">
        <ul class="flex justify-between items-center w-full">
            <li class="flex">
                <a href="{{ \App\Support\Locale::url('/') }}" title="ANASAYFA">
                    <div class="bloomberght-icon-font-home text-sm mb-2 text-center"></div>
                    <div class="text-xs">ANASAYFA</div>
                </a>
            </li>
            <li class="flex">
                <a href="{{ \App\Support\Locale::url('/piyasalar') }}" title="PİYASA">
                    <div class="bloomberght-icon-font-stats text-sm mb-2 text-center"></div>
                    <div class="text-xs">PİYASA</div>
                </a>
            </li>
            <li class="flex">
                <a href="{{ \App\Support\Locale::url('/tv') }}" title="CANLI">
                    <div class="bloomberght-icon-font-play text-sm mb-2 text-center"></div>
                    <div class="text-xs">CANLI</div>
                </a>
            </li>
            <li class="flex">
                <a href="https://www.bloomberghtradyo.com" target="_blank" rel="noopener noreferrer" title="RADYO">
                    <div class="bloomberght-icon-font-radio text-sm mb-2 text-center"></div>
                    <div class="text-xs">RADYO</div>
                </a>
            </li>
            <li class="flex">
                <a href="{{ \App\Support\Locale::url('/sondakika') }}" title="SON DAKİKA">
                    <div class="bloomberght-icon-font-time text-sm mb-2 text-center"></div>
                    <div class="text-xs">SON DAKİKA</div>
                </a>
            </li>
        </ul>
    </div>

    <button type="button" class="scroll-to-top" data-scroll-top aria-label="{{ __('ui.back_to_top') }}">
        <x-icon name="arrow-up" title="{{ __('ui.back_to_top') }}" />
    </button>

    @php($dimensionNews = ($news ?? null) instanceof \App\Models\News ? $news : null)
    @php($customDimension = app(\App\Services\Analytics\CustomDimensionBuilder::class)->build($dimensionNews, $pageType ?? 'home'))
    <script>window.customdimension = @json($customDimension);</script>
    <script>
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            event: 'pageview',
            pageType: @json($pageType ?? 'home'),
            customdimension: window.customdimension
        });
        document.addEventListener('DOMContentLoaded', function () {
            if (window.cbgAds && typeof window.cbgAds.init === 'function') { window.cbgAds.init(); }
        });
    </script>
    <script src="{{ asset('js/lazy.js') }}" defer></script>
    <script src="{{ asset('js/swiper-bundle.min.js') }}" defer></script>
    @php($__siteJs = @filemtime(public_path('js/site.js')) ?: 1)
    <script src="{{ asset('js/site.js') }}?v={{ $__siteJs }}" defer></script>
    <script src="{{ asset('js/market-live.js') }}" defer></script>
    @stack('scripts')
</body>
</html>