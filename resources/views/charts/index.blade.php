@extends('front.layouts.app')

@php
    $base = \App\Support\Locale::url('/grafik-arsivi');
    $catQuery = $activeCategory ? '?kategori='.$activeCategory : '';
    $pageUrl = fn (int $p): string => $base.($p > 1 ? '/'.$p : '').$catQuery;
    $tabUrl = fn (string $slug): string => $base.'?kategori='.$slug;
@endphp

@section('title', __('ui.chart_archive').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('ui.chart_archive').' — '.($siteSettings['site_tagline'] ?? ''))
@section('canonical', url($base))

@section('content')
    <div class="charts-page">
        <div class="section-title bht-page-band">
            <h1 class="font-unna font-bold">{{ __('ui.chart_archive') }}</h1>
        </div>

        {{-- Son Eklenen Grafikler (h2) --}}
        <section class="charts-latest">
            <x-section-head :title="__('ui.latest_charts')" />
            <div class="chart-grid chart-grid-latest news-card-grid cols-3">
                @foreach ($latestCharts as $chart)
                    @include('charts.partials.card', ['chart' => $chart])
                @endforeach
            </div>
        </section>

        {{-- Kategoriler sekmesi (Grafik | Piyasa | Makro Ekonomi | Aktüel) --}}
        <div class="data-tab-section chart-categories">
            <div class="data-tab-head chart-tabs-head">
                <div class="chart-tabs-label">{{ __('ui.categories') }}</div>
                <div class="chart-category-tabs tab-menu" role="tablist">
                    @foreach ($categories as $slug => $label)
                        <a class="data-tab-button tab-button chart-category-tab {{ $activeCategory === $slug ? 'is-active' : '' }}"
                           href="{{ $tabUrl($slug) }}"
                           data-tab-id="{{ $slug }}"
                           role="tab"
                           aria-selected="{{ $activeCategory === $slug ? 'true' : 'false' }}">{{ $label }}</a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Grafik kartları grid --}}
        <div class="chart-grid data-grid news-card-grid cols-3">
            @forelse ($charts as $chart)
                @include('charts.partials.card', ['chart' => $chart])
            @empty
                <p class="chart-empty">{{ __('ui.no_charts') }}</p>
            @endforelse
        </div>

        {{-- Businessweek grafik arşivi dış linki --}}
        <a class="charts-businessweek"
           href="https://www.businessweek.com.tr/grafik-arsivi"
           title="Bloomberg Businessweek"
           target="_blank" rel="noopener noreferrer">
            {{ __('ui.businessweek_charts') }} &raquo;
        </a>

        {{-- Sayfalama: 1 2 3 4 5 → Sonraki --}}
        @if ($lastPage > 1)
            <nav class="pagination flex w-full gap-2 text-sm" aria-label="{{ __('ui.pagination') }}">
                @for ($p = 1; $p <= $lastPage; $p++)
                    @if ($p === $currentPage)
                        <span class="page-btn is-active" aria-current="page">{{ $p }}</span>
                    @else
                        <a class="page-btn" href="{{ $pageUrl($p) }}" aria-label="{{ __('ui.page') }} {{ $p }}">{{ $p }}</a>
                    @endif
                @endfor

                @if ($currentPage < $lastPage)
                    <a class="page-btn page-next" href="{{ $pageUrl($currentPage + 1) }}" aria-label="{{ __('ui.next') }}">
                        {{ __('ui.next') }} <x-icon name="chevron-right" />
                    </a>
                @endif
            </nav>
        @endif

        {{-- En Çok Okunanlar — global widget --}}
        <x-most-read class="charts-most-read"
                     :tabs="['today' => $mostReadTabs['today'], 'week' => $mostReadTabs['week'], 'month' => $mostReadTabs['month']]" />
    </div>
@endsection