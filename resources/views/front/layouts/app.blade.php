<!doctype html>
<html lang="{{ $siteSettings['language'] ?? 'tr' }}" class="bht-html">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#384552">

    <title>@yield('title', ($siteSettings['site_name'] ?? 'Bloomberg HT').' | Türkiye\'nin Ekonomi Platformu')</title>
    <meta name="description" content="@yield('description', $siteSettings['site_tagline'] ?? '')">
    @hasSection('robots')
        <meta name="robots" content="@yield('robots')">
    @endif

    <link rel="canonical" href="@yield('canonical', url()->current())">
    <link rel="icon" href="{{ asset($siteSettings['favicon_path'] ?? '/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset($siteSettings['apple_touch_icon'] ?? '/apple-touch-icon.png') }}">
    <link rel="alternate" type="application/rss+xml" title="Bloomberg HT RSS" href="{{ url('/rss') }}">

    {{-- Open Graph (OG görseli 960x540, Bölüm 1 / 11.1) --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ $siteSettings['site_name'] ?? 'Bloomberg HT' }}">
    <meta property="og:locale" content="{{ $siteSettings['og_locale'] ?? 'tr_TR' }}">
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
    <meta property="og:image:width" content="960">
    <meta property="og:image:height" content="540">

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

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @include('front.partials.jsonld')
    @stack('jsonld')
    @yield('head')
</head>
<body class="bht-body @yield('body_class')">
    @include('front.partials.header')

    <main class="container page bht-main" id="main-content">
        @yield('content')
    </main>

    @include('front.partials.footer')
    @include('front.partials.cookie')

    <button type="button" class="scroll-to-top" data-scroll-top aria-label="Yukarı dön">
        <x-icon name="arrow-up" title="Yukarı dön" />
    </button>

    <script src="{{ asset('js/lazy.js') }}" defer></script>
    <script src="{{ asset('js/site.js') }}" defer></script>
    @stack('scripts')
</body>
</html>