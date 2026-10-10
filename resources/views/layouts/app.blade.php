<!doctype html>
<html lang="{{ $siteSettings['language'] ?? 'tr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $siteSettings['site_name'] ?? 'Bloomberg HT')</title>
    <meta name="description" content="@yield('description', $siteSettings['site_tagline'] ?? '')">
    <link rel="icon" href="{{ asset($siteSettings['favicon_path'] ?? '/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset($siteSettings['apple_touch_icon'] ?? '/apple-touch-icon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('head')
</head>
<body>
    <header class="site-header">
        <div class="container topbar">
            <a class="logo" href="{{ route('home') }}" aria-label="{{ $siteSettings['logo_alt'] ?? 'Bloomberg HT' }}">
                <img src="{{ asset($siteSettings['logo_path'] ?? '/brand/logo.svg') }}" alt="{{ $siteSettings['logo_alt'] ?? 'Bloomberg HT' }}" width="200" height="58">
            </a>
            <nav class="main-nav" aria-label="Ana navigasyon">
                <a href="{{ route('home') }}">Ana Sayfa</a>
                @foreach ($navCategories as $category)
                    <div class="nav-item">
                        <a href="#">{{ $category->name }}</a>
                        @if ($category->children->isNotEmpty())
                            <div class="dropdown">
                                @foreach ($category->children as $child)
                                    <a href="#">{{ $child->name }}</a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
                <a href="{{ route('news.index') }}">Haberler</a>
                <a href="{{ route('breaking.index') }}">Son Dakika</a>
                <a href="{{ route('charts.index') }}">Grafik Arşivi</a>
                <a href="{{ route('authors.index') }}">Yazarlar</a>
            </nav>
            <div class="top-actions">
                <a href="#">Canlı Yayın</a>
                <span>·</span>
                <a href="#">Radyo</a>
            </div>
        </div>

        <div class="breaking-bar">
            <div class="container breaking-inner">
                <span class="breaking-label">SON DAKİKA</span>
                <div class="breaking-ticker">
                    @forelse ($breakingList as $breaking)
                        <a href="{{ $breaking->news?->url ?? '#' }}">
                            <span class="ticker-time">{{ $breaking->published_at?->format('H:i') }}</span>
                            {{ $breaking->title }}
                        </a>
                    @empty
                        <span>Son dakika haberi bulunmuyor.</span>
                    @endforelse
                </div>
            </div>
        </div>
    </header>

    <main class="container page">
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="container">
            <div class="footer-top">
                <a class="logo" href="{{ route('home') }}">
                    <img src="{{ asset($siteSettings['logo_path'] ?? '/brand/logo.svg') }}" alt="" width="150" height="44">
                </a>
                <p class="tagline">{{ $siteSettings['site_tagline'] ?? '' }}</p>
            </div>
            <div class="footer-menu">
                <a href="{{ route('home') }}">Ana Sayfa</a>
                <a href="{{ route('news.index') }}">Haberler</a>
                <a href="{{ route('breaking.index') }}">Son Dakika</a>
                <a href="{{ route('charts.index') }}">Grafik Arşivi</a>
                <a href="{{ route('authors.index') }}">Yazarlar</a>
                <a href="#">Künye</a>
                <a href="#">İletişim</a>
                <a href="#">Çerez Politikası</a>
                <a href="#">Aydınlatma Metni</a>
                <a href="#">Kullanım Koşulları</a>
            </div>
            <div class="brand-strip">
                {{ $siteSettings['brand_list'] ?? 'Habertürk · Habertürk TV · HT Spor · Bloomberg HT' }}
            </div>
            <p class="legal-notice">{{ $siteSettings['legal_notice'] ?? '' }}</p>
            <p class="copyright">{{ $siteSettings['copyright'] ?? 'YASAL UYARI © 2026' }} · KEP: {{ $siteSettings['kep_address'] ?? '' }}</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
