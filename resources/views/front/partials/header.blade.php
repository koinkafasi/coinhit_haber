@php
    $settings = $siteSettings ?? [];
    $breakingList = $breakingList ?? collect();
    $mega = config('navigation.mega', []);
    $newWindow = 'target="_blank" rel="noopener noreferrer"';
@endphp

<header class="site-header bht-header bg-[#384552]" data-type="header">
    <div class="topbar bht-topbar">
        <div class="container bht-topbar-inner nav-sub-menu">
            <a class="logo bht-logo" href="{{ \App\Support\Locale::route('home') }}" aria-label="{{ $settings['logo_alt'] ?? 'Bloomberg HT' }}">
                <img src="{{ asset($settings['logo_path'] ?? '/brand/logo.svg') }}" alt="{{ $settings['logo_alt'] ?? 'Bloomberg HT' }}" width="200" height="58">
            </a>

            <button type="button" class="bht-mobile-toggle" data-menu-toggle aria-controls="bht-main-nav" aria-expanded="false" aria-label="{{ __('ui.menu') }}">
                <x-icon name="menu" title="{{ __('ui.menu') }}" />
            </button>

            <nav id="bht-main-nav" class="main-nav bht-mega-nav bht-nav" aria-label="{{ __('ui.main_nav') }}" data-type="nav-sub-menu">
                <ul class="bht-nav-list">
                    @foreach ($mega as $item)
                        @php($hasChildren = ! empty($item['children']))
                        <li class="sub-menu-wrapper bht-nav-item {{ $hasChildren ? 'relative group has-children' : '' }}">
                            <a class="bht-nav-link" href="{{ \App\Support\Locale::navUrl($item['url']) }}"
                               @if (! empty($item['external'])) {!! $newWindow !!} @endif>
                                <span>{{ \App\Support\Locale::navLabel($item['label']) }}</span>
                                @if ($hasChildren)
                                    <x-icon name="chevron-down" class="bht-nav-chevron" />
                                @endif
                            </a>
                            @if ($hasChildren)
                                <ul class="drop-down bht-dropdown">
                                    @foreach ($item['children'] as $child)
                                        <li class="bht-dropdown-item">
                                            <a href="{{ \App\Support\Locale::navUrl($child['url']) }}">{{ \App\Support\Locale::navLabel($child['label']) }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="top-actions bht-top-actions">
                @include('front.partials.language-switcher')
                <a class="live-link" href="{{ \App\Support\Locale::url('/tv') }}"><x-icon name="live" /> <span>{{ __('ui.live') }}</span></a>
                <a href="https://www.bloomberghtradyo.com" {!! $newWindow !!}><x-icon name="radio" /> <span>{{ __('ui.radio') }}</span></a>
                <a class="search-toggle" href="{{ \App\Support\Locale::route('search.index') }}" aria-label="{{ __('ui.search') }}"><x-icon name="search" title="{{ __('ui.search') }}" /></a>
            </div>
        </div>
    </div>

    <div class="breaking-bar bht-breaking bg-[#bd1b2d]" data-type="breaking-news">
        <div class="container breaking-inner">
            <a class="breaking-label bht-breaking-label" href="{{ \App\Support\Locale::route('breaking.index') }}">
                <span class="live-dot" aria-hidden="true"></span> {{ __('ui.breaking') }}
            </a>
            <div class="breaking-ticker" data-ticker>
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

    @include('front.partials.finance-slider')
</header>