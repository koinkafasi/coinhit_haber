@extends('front.layouts.app')

@section('title', $title.' '.__('ui.news_suffix').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', $description ?: ($title.' '.__('ui.news_suffix')))

@section('content')
    <div class="channel-page">
        <div class="section-title bht-page-band">
            <h1 class="font-unna font-bold">{{ $title }}</h1>
        </div>

        @if ($description)
            <p class="channel-intro">{{ $description }}</p>
        @endif

        @if ($grid->isNotEmpty())
            <div class="news-card-grid cols-2">
                @foreach ($grid as $i => $item)
                    <x-news-card :news="$item" :type="$i === 0 ? 'type2' : 'type1'" :showSpot="$i === 0" :eager="$i === 0" />
                @endforeach
            </div>
        @endif

        @if ($author)
            <section class="author-corner">
                <div class="author-corner-avatar">
                    @if ($author->avatar)
                        <img src="{{ asset($author->avatar) }}" alt="{{ $author->name }}" loading="lazy">
                    @else
                        <span class="author-avatar">{{ mb_substr($author->name, 0, 1) }}</span>
                    @endif
                </div>
                <div class="author-corner-body">
                    <a class="author-corner-name font-unna font-bold" href="{{ \App\Http\Controllers\AuthorController::archiveUrl($author) }}">{{ $author->name }}</a>
                    @if ($author->title)
                        <div class="author-corner-title">{{ $author->title }}</div>
                    @endif
                    @if ($author->bio)
                        <p class="author-corner-summary">{{ $author->bio }}</p>
                    @endif
                    <a class="author-corner-all" href="{{ \App\Http\Controllers\AuthorController::archiveUrl($author) }}">{{ __('ui.all_writings') }} &raquo;</a>
                </div>
            </section>
        @endif

        @if ($videos->isNotEmpty())
            <section class="channel-videos">
                <x-section-head :title="__('ui.video_news')" href="/video" />
                <div class="program-list">
                    @foreach ($videos as $episode)
                        <article class="program-card">
                            <a href="{{ $episode->program?->url ?? '#video' }}">
                                <figure class="program-thumb">
                                    <x-lazy-image :src="asset($episode->thumbnail ?: $episode->program?->thumbnail ?: '/img/og-default.png')"
                                                  alt="" contract="video_thumb" class="img-contract" />
                                    <span class="program-duration">{{ $episode->duration }}</span>
                                </figure>
                                <h3 class="program-title font-unna font-bold line-clamp-2">{{ $episode->display_title }}</h3>
                                @if ($episode->air_at)
                                    <div class="program-date">{{ $episode->air_at->translatedFormat('d F Y, H:i') }}</div>
                                @endif
                            </a>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        @isset($marketWidget)
            @include('partials.market-widget-compact')
        @endisset

        <section class="channel-list-block">
            <x-section-head :title="$title.' '.__('ui.news_suffix')" level="h2" />
            @include('partials.news-list', ['items' => $news, 'showSpot' => true])
            @include('borsa.partials.pagination', ['paginator' => $news])
        </section>
    </div>
@endsection