@extends('front.layouts.app')

@section('title', $episode->display_title.' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', \Illuminate\Support\Str::limit(strip_tags((string) ($episode->program?->description ?? $episode->display_title)), 150))
@section('canonical', url(\App\Support\Locale::url('/'.$seriesSlug.'/video/'.$slug.'/'.$episode->id)))
@section('og_type', 'video.other')

@section('content')
    <div class="video-page video-detail-page">
        <x-breadcrumb :items="[
            ['label' => __('media.video'), 'url' => \App\Support\Locale::url('/video')],
            ['label' => $series?->name ?? __('media.videos'), 'url' => $series ? \App\Support\Locale::url('/video/'.$series->slug) : null],
            ['label' => \Illuminate\Support\Str::limit($episode->display_title, 60)],
        ]" />

        <article class="video-detail" data-video-id="{{ $episode->id }}">
            <div class="video-player">
                @if ($episode->video_url)
                    <video class="video-player-media" controls preload="metadata"
                           poster="{{ asset($episode->thumbnail ?: '/img/placeholders/placeholder-1.svg') }}">
                        <source src="{{ $episode->video_url }}" type="video/mp4">
                        {{ __('media.video_not_supported') }}
                    </video>
                @else
                    <div class="video-player-placeholder">
                        <x-lazy-image :src="asset($episode->thumbnail ?: '/img/placeholders/placeholder-1.svg')" alt="" contract="video_thumb" class="img-contract" :eager="true" />
                    </div>
                @endif
            </div>

            <h1 class="video-detail-title font-unna font-bold">{{ $episode->display_title }}</h1>

            <div class="video-detail-meta">
                @if ($series)
                    <a class="video-detail-series" href="{{ \App\Support\Locale::url('/video/'.$series->slug) }}">{{ $series->name }}</a>
                @endif
                <span class="video-detail-duration">{{ $episode->duration }}</span>
                @if ($episode->air_at)
                    <time class="video-detail-date" datetime="{{ $episode->air_at->toIso8601String() }}">{{ $episode->air_at->translatedFormat('d F Y') }}</time>
                @endif
            </div>

            @if ($episode->guest)
                <p class="video-detail-guest">{{ __('media.guest') }}: {{ $episode->guest }}</p>
            @endif

            @if ($episode->program?->description)
                <p class="video-detail-desc">{{ $episode->program->description }}</p>
            @endif
        </article>

        @if ($related->isNotEmpty())
            <section class="video-related" id="ilgili-videolar">
                <x-section-head :title="__('media.related_videos')" />
                <div class="video-list">
                    @foreach ($related as $item)
                        @include('video.partials.card', ['episode' => $item])
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection