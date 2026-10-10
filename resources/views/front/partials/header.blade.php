@php
    $settings = $siteSettings ?? [];
    $navCategories = $navCategories ?? collect();
    $breakingList = $breakingList ?? collect();
@endphp

<header class="site-header bht-header" data-type="header">
    <div class="topbar bht-topbar">
        <div class="container bht-topbar-inner">
            <a class="logo bht-logo" href="{{ route('home') }}" aria-label="{{ $settings['logo_alt'] ?? 'Bloomberg HT' }}">
                <img src="{{ asset($settings['logo_path'] ?? '/brand/logo.svg') }}" alt="{{ $settings['logo_alt'] ?? 'Bloomberg HT' }}" width="200" height="58">
            </a>

            <nav class="main-nav bht-mega-nav" aria-label="Ana navigasyon" data-type="nav-sub-menu">
                <a href="{{ route('home') }}">Ana Sayfa</a>
                @foreach ($navCategories as $category)
                    <div class="nav-item sub-menu-wrapper">
                        <a href="#">{{ $category->name }}</a>
                        @if ($category->children->isNotEmpty())
                            <div class="dropdown drop-down">
                                @foreach ($category->children as $child)
                                    <a href="#">{{ $child->name }}</a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
                <a href="{{ route('news.index') }}">Haberler</a>
                <a href="{{ route('breaking.index') }}">Son Dakika</a>
            </nav>

            <div class="top-actions bht-top-actions">
                <a class="live-link" href="#"><x-icon name="live" /> Canlı Yayın</a>
                <a href="#"><x-icon name="radio" /> Radyo</a>
                <a class="search-toggle" href="#" aria-label="Ara"><x-icon name="search" title="Ara" /></a>
            </div>
        </div>
    </div>

    <div class="breaking-bar bht-breaking" data-type="breaking-news">
        <div class="container breaking-inner">
            <span class="breaking-label bht-breaking-label">
                <span class="live-dot" aria-hidden="true"></span> SON DAKİKA
            </span>
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