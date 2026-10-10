@php
    $settings = $siteSettings ?? [];
    $breakingList = $breakingList ?? collect();
    $mega = config('navigation.mega', []);
    $newWindow = 'target="_blank" rel="noopener noreferrer"';
@endphp

<header class="site-header bht-header bg-[#384552]" data-type="header">
    <div class="topbar bht-topbar">
        <div class="container bht-topbar-inner nav-sub-menu">
            <a class="logo bht-logo" href="{{ route('home') }}" aria-label="{{ $settings['logo_alt'] ?? 'Bloomberg HT' }}">
                <img src="{{ asset($settings['logo_path'] ?? '/brand/logo.svg') }}" alt="{{ $settings['logo_alt'] ?? 'Bloomberg HT' }}" width="200" height="58">
            </a>

            <button type="button" class="bht-mobile-toggle" data-menu-toggle aria-controls="bht-main-nav" aria-expanded="false" aria-label="Menü">
                <x-icon name="menu" title="Menü" />
            </button>

            <nav id="bht-main-nav" class="main-nav bht-mega-nav bht-nav" aria-label="Ana navigasyon" data-type="nav-sub-menu">
                <ul class="bht-nav-list">
                    @foreach ($mega as $item)
                        @php($hasChildren = ! empty($item['children']))
                        <li class="sub-menu-wrapper bht-nav-item {{ $hasChildren ? 'relative group has-children' : '' }}">
                            <a class="bht-nav-link" href="{{ $item['url'] }}"
                               @if (! empty($item['external'])) {!! $newWindow !!} @endif>
                                <span>{{ $item['label'] }}</span>
                                @if ($hasChildren)
                                    <x-icon name="chevron-down" class="bht-nav-chevron" />
                                @endif
                            </a>
                            @if ($hasChildren)
                                <ul class="drop-down bht-dropdown">
                                    @foreach ($item['children'] as $child)
                                        <li class="bht-dropdown-item">
                                            <a href="{{ $child['url'] }}">{{ $child['label'] }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="top-actions bht-top-actions">
                <a class="live-link" href="/tv"><x-icon name="live" /> <span>Canlı Yayın</span></a>
                <a href="https://www.bloomberghtradyo.com" {!! $newWindow !!}><x-icon name="radio" /> <span>Radyo</span></a>
                <a class="search-toggle" href="{{ route('search.index') }}" aria-label="Ara"><x-icon name="search" title="Ara" /></a>
            </div>
        </div>
    </div>

    <div class="breaking-bar bht-breaking bg-[#bd1b2d]" data-type="breaking-news">
        <div class="container breaking-inner">
            <a class="breaking-label bht-breaking-label" href="{{ route('breaking.index') }}">
                <span class="live-dot" aria-hidden="true"></span> SON DAKİKA
            </a>
            <div class="breaking-ticker" data-ticker>
                @if ($breakingList->isNotEmpty())
                    <div class="breaking-track">
                        @foreach ($breakingList as $breaking)
                            <a href="{{ route('breaking.index') }}">
                                <span class="ticker-time">{{ $breaking->published_at?->format('H:i') }}</span>
                                {{ $breaking->title }}
                            </a>
                        @endforeach
                        @foreach ($breakingList as $breaking)
                            <a href="{{ route('breaking.index') }}" aria-hidden="true" tabindex="-1">
                                <span class="ticker-time">{{ $breaking->published_at?->format('H:i') }}</span>
                                {{ $breaking->title }}
                            </a>
                        @endforeach
                    </div>
                @else
                    <span class="ticker-empty">Son dakika haberi bulunmuyor.</span>
                @endif
            </div>
        </div>
    </div>
</header>