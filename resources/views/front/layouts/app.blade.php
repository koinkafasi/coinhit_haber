<!doctype html>
<html lang="{{ $siteSettings['language'] ?? 'tr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $siteSettings['site_name'] ?? 'Bloomberg HT')</title>
    <meta name="description" content="@yield('description', $siteSettings['site_tagline'] ?? '')">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" href="{{ asset($siteSettings['favicon_path'] ?? '/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset($siteSettings['apple_touch_icon'] ?? '/apple-touch-icon.png') }}">
    <meta property="og:site_name" content="{{ $siteSettings['site_name'] ?? 'Bloomberg HT' }}">
    <meta property="og:locale" content="{{ $siteSettings['og_locale'] ?? 'tr_TR' }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('head')
</head>
<body class="bht-body @yield('body_class')">
    @include('front.partials.header')

    <main class="container page bht-main" id="main-content">
        @yield('content')
    </main>

    @include('front.partials.footer')

    <script src="{{ asset('js/lazy.js') }}" defer></script>
    @stack('scripts')
</body>
</html>