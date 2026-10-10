@extends('layouts.app')

@section('title', 'Grafik Arşivi | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))

@section('content')
    <div class="section-title">
        <h1>Grafik Arşivi</h1>
    </div>

    <div class="list-grid">
        @forelse ($charts as $chart)
            <article class="news-card">
                <a href="#">
                    <img src="{{ asset($chart->image ?: '/img/og-default.png') }}" alt="" loading="lazy" class="card-img">
                    <div class="card-body">
                        <span class="category-tag">{{ $chart->category }}</span>
                        <h3>{{ $chart->title }}</h3>
                        @if ($chart->summary)
                            <p class="card-spot">{{ $chart->summary }}</p>
                        @endif
                        <p class="card-time">{{ $chart->published_at?->format('d.m.Y') }}</p>
                    </div>
                </a>
            </article>
        @empty
            <p>Grafik bulunmuyor.</p>
        @endforelse
    </div>
@endsection
