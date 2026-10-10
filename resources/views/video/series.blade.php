@extends('front.layouts.app')

@section('title', $series->name.' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', \Illuminate\Support\Str::limit(strip_tags((string) $series->description), 150))
@section('canonical', url(\App\Support\Locale::url('/video/'.$series->slug)))

@section('content')
    <div class="video-page video-series-page">
        <x-breadcrumb :items="[['label' => 'Video', 'url' => \App\Support\Locale::url('/video')], ['label' => $series->name]]" />

        <header class="series-head">
            <figure class="series-head-figure">
                <x-lazy-image :src="asset($series->thumbnail ?: '/img/placeholders/placeholder-1.svg')" alt="" contract="video_thumb" class="img-contract" :eager="true" />
            </figure>
            <div class="series-head-info">
                <span class="program-detail-kicker">Video Serisi</span>
                <h1 class="series-head-name font-unna font-bold">{{ $series->name }}</h1>
                @if ($series->host)
                    <span class="series-head-host">{{ $series->host }}</span>
                @endif
                @if ($series->description)
                    <p class="series-head-desc">{{ $series->description }}</p>
                @endif
                <a class="tv-action-link" href="{{ \App\Support\Locale::url('/video') }}">Tüm Seriler</a>
            </div>
        </header>

        <section class="series-episodes">
            <x-section-head title="Bölümler" />
            <div class="video-list" data-load-more-container>
                @forelse ($episodes as $episode)
                    @include('video.partials.card', ['episode' => $episode])
                @empty
                    <p class="tv-empty">Bu seride henüz bölüm bulunmuyor.</p>
                @endforelse
            </div>

            @if ($episodes->hasMorePages())
                <div class="load-more-wrap">
                    <a class="load-more" href="{{ $episodes->nextPageUrl() }}"
                       data-load-more data-next-url="{{ $episodes->nextPageUrl() }}"
                       data-container="[data-load-more-container]">{{ __('ui.list') }}</a>
                </div>
            @endif
        </section>

        @if ($otherSeries->isNotEmpty())
            <section class="series-other">
                <x-section-head title="Diğer Seriler" />
                <div class="series-grid">
                    @foreach ($otherSeries as $item)
                        <article class="series-card">
                            <a href="{{ \App\Support\Locale::url('/video/'.$item->slug) }}">
                                <figure class="series-card-figure">
                                    <x-lazy-image :src="asset($item->thumbnail ?: '/img/placeholders/placeholder-1.svg')" alt="" contract="video_thumb" class="img-contract" />
                                </figure>
                                <h3 class="series-card-name font-unna font-bold">{{ $item->name }}</h3>
                            </a>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection