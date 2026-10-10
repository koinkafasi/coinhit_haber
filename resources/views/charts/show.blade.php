@extends('front.layouts.app')

@section('title', $chart->title.' | '.__('ui.chart_archive'))
@section('description', \Illuminate\Support\Str::limit(strip_tags((string) ($chart->summary ?? $chart->title)), 200))
@section('og_type', 'article')
@section('og_title', $chart->title)
@section('og_image', url($chart->image ?: '/img/og-default.png'))
@section('canonical', url($chart->url))

@section('content')
    <div class="chart-detail">
        <nav class="chart-breadcrumb" aria-label="{{ __('ui.breadcrumb') }}">
            <a href="{{ \App\Support\Locale::url('/grafik-arsivi') }}">{{ __('ui.chart_archive') }}</a>
            <x-icon name="chevron-right" />
            <span>{{ $chart->category_label }}</span>
        </nav>

        <h1 class="font-unna font-bold chart-detail-title">{{ $chart->title }}</h1>

        <div class="chart-detail-category category text-xs">{{ $chart->category_label }}</div>

        <figure class="chart-detail-figure">
            <img src="{{ asset($chart->image ?: '/img/og-default.png') }}"
                 alt="{{ $chart->title }}" class="chart-detail-img" loading="eager" decoding="async">
        </figure>

        @if ($chart->summary)
            <div class="article-wrapper chart-detail-summary">
                <p>{{ $chart->summary }}</p>
            </div>
        @endif

        @if ($chart->published_at)
            <p class="chart-detail-date">{{ $chart->published_at->isoFormat('D MMMM YYYY, dddd') }}</p>
        @endif

        @if ($related->isNotEmpty())
            <section class="chart-related">
                <x-section-head :title="__('ui.related_charts')" :href="\App\Support\Locale::url('/grafik-arsivi')" />
                <div class="chart-grid news-card-grid cols-3">
                    @foreach ($related as $item)
                        @include('charts.partials.card', ['chart' => $item])
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection