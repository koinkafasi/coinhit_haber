@extends('front.layouts.app')

@section('title', __('media.podcast').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('media.podcast_index_desc'))
@section('canonical', url(\App\Support\Locale::url('/podcastler/')))

@section('content')
    <div class="podcast-page">
        <h1 class="page-title font-unna font-bold">{{ __('media.podcast') }}</h1>

        <div class="podcast-grid">
            @foreach ($podcasts as $podcast)
                <article class="podcast-card">
                    <a href="{{ \App\Support\Locale::url('/podcastler/'.$podcast->slug) }}">
                        <figure>
                            <x-lazy-image :src="asset($podcast->cover ?: '/img/placeholders/placeholder-1.svg')" alt="" contract="news_card_square" class="img-contract" />
                        </figure>
                        <div class="podcast-body">
                            <h2 class="font-unna font-bold line-clamp-2">{{ $podcast->title }}</h2>
                            <p class="podcast-desc line-clamp-2">{{ $podcast->description }}</p>
                            <span class="podcast-air text-xs">{{ $podcast->air_days }} · {{ $podcast->air_time }}</span>
                        </div>
                    </a>
                </article>
            @endforeach
        </div>
    </div>
@endsection