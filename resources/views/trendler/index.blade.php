@extends('front.layouts.app')

@php
    $active = $activeSlug ?? 'all';
@endphp

@section('title', __('ui.quick_links').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('ui.quick_links').' — '.($siteSettings['site_tagline'] ?? ''))
@section('canonical', url(\App\Support\Locale::url('/trendler')))

@section('content')
    <div class="trendler-layout">
        <div class="trendler-main">
            <div class="section-title bht-page-band">
                <h1 class="font-unna font-bold"># {{ __('ui.quick_links') }}</h1>
            </div>

            {{-- Trend etiket çipleri: Tümü | Rezerv | Hisse | Waller | ... (dinamik) --}}
            <div class="trend-tabs-wrap" data-widget-type="trend-tabs">
                <div class="trend-tabs tab-menu" role="tablist">
                    <button type="button" role="tab"
                            class="tab-button trend-chip {{ $active === 'all' ? 'is-active' : '' }}"
                            data-tab="all"
                            aria-selected="{{ $active === 'all' ? 'true' : 'false' }}">{{ __('ui.trend_all') }}</button>
                    @foreach ($trends as $trend)
                        <button type="button" role="tab"
                                class="tab-button trend-chip {{ $active === $trend->slug ? 'is-active' : '' }}"
                                data-tab="{{ $trend->slug }}"
                                aria-selected="{{ $active === $trend->slug ? 'true' : 'false' }}">{{ $trend->label }}</button>
                    @endforeach
                </div>

                <div class="trend-panels">
                    <div class="tab-content trend-panel {{ $active === 'all' ? '' : 'hidden' }}" data-id="all">
                        @forelse ($allItems as $item)
                            @include('trendler.partials.news-item', ['item' => $item])
                        @empty
                            <p class="trend-empty">{{ __('ui.no_news') }}</p>
                        @endforelse
                    </div>

                    @foreach ($panels as $slug => $panel)
                        <div class="tab-content trend-panel {{ $active === $slug ? '' : 'hidden' }}" data-id="{{ $slug }}">
                            @if ($panel['trend']->description)
                                <p class="trend-description">{{ $panel['trend']->description }}</p>
                            @endif
                            @forelse ($panel['items'] as $item)
                                @include('trendler.partials.news-item', ['item' => $item])
                            @empty
                                <p class="trend-empty">{{ __('ui.no_news') }}</p>
                            @endforelse
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <aside class="borsa-aside">
            @include('partials.market-widget-compact')
        </aside>
    </div>
@endsection