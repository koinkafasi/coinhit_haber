@extends('front.layouts.app')

@section('title', $podcast->title.' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', \Illuminate\Support\Str::limit(strip_tags((string) $podcast->description), 150))
@section('canonical', url(\App\Support\Locale::url('/podcastler/'.$podcast->slug)))
@section('og_type', 'music.radio_station')

@section('content')
    <div class="podcast-page podcast-show-page">
        <x-breadcrumb :items="[['label' => 'Podcast', 'url' => \App\Support\Locale::url('/podcastler/')], ['label' => $podcast->title]]" />

        <header class="podcast-head">
            <figure class="podcast-head-cover">
                <x-lazy-image :src="asset($podcast->cover ?: '/img/placeholders/placeholder-1.svg')" alt="" contract="news_card_square" class="img-contract" :eager="true" />
            </figure>
            <div class="podcast-head-info">
                <span class="program-detail-kicker">Podcast Serisi</span>
                <h1 class="podcast-head-title font-unna font-bold">{{ $podcast->title }}</h1>
                <div class="podcast-head-meta">
                    @if ($podcast->air_days)
                        <span>{{ $podcast->air_days }}</span>
                    @endif
                    @if ($podcast->air_time)
                        <span>{{ $podcast->air_time }}</span>
                    @endif
                    <span>{{ $episodes->count() }} bölüm</span>
                </div>
                @if ($podcast->description)
                    <p class="podcast-head-desc">{{ $podcast->description }}</p>
                @endif
            </div>
        </header>

        <section class="podcast-episodes" id="bolumler">
            <x-section-head title="Bölümler" />
            <ol class="podcast-episode-list">
                @foreach ($episodes as $episode)
                    <li class="podcast-episode" data-episode-id="{{ $episode->id }}">
                        <div class="podcast-episode-head">
                            <span class="podcast-episode-no">{{ $loop->iteration }}</span>
                            <div class="podcast-episode-info">
                                <h2 class="podcast-episode-title font-unna font-bold">{{ $episode->title }}</h2>
                                <div class="podcast-episode-meta">
                                    <span class="podcast-episode-duration">{{ $episode->duration }}</span>
                                    @if ($episode->published_at)
                                        <time datetime="{{ $episode->published_at->toIso8601String() }}">{{ $episode->published_at->translatedFormat('d F Y') }}</time>
                                    @endif
                                </div>
                                @if ($episode->description)
                                    <p class="podcast-episode-desc">{{ $episode->description }}</p>
                                @endif
                            </div>
                        </div>
                        @if ($episode->audio_url)
                            <audio class="podcast-audio" controls preload="none" src="{{ $episode->audio_url }}"></audio>
                        @endif
                    </li>
                @endforeach
            </ol>
        </section>

        @if ($others->isNotEmpty())
            <section class="podcast-other">
                <x-section-head title="Diğer Seriler" href="/podcastler/" allLabel="TÜMÜ" />
                <div class="podcast-grid">
                    @foreach ($others as $item)
                        <article class="podcast-card">
                            <a href="{{ \App\Support\Locale::url('/podcastler/'.$item->slug) }}">
                                <figure>
                                    <x-lazy-image :src="asset($item->cover ?: '/img/placeholders/placeholder-1.svg')" alt="" contract="news_card_square" class="img-contract" />
                                </figure>
                                <div class="podcast-body">
                                    <h3 class="font-unna font-bold line-clamp-2">{{ $item->title }}</h3>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection