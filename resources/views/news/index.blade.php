@extends('front.layouts.app')

@section('title', __('ui.news_title').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('ui.news_title').' — '.($siteSettings['site_tagline'] ?? ''))
@section('canonical', url(\App\Support\Locale::url('/haberler')))

@section('content')
    <div class="section-title bht-page-band">
        <h1 class="font-unna font-bold">{{ __('ui.news_title') }}</h1>
        <a href="{{ \App\Support\Locale::route('breaking.index') }}">{{ __('ui.breaking_link') }} &raquo;</a>
    </div>

    <div class="news-card-grid cols-2 masonry-grid" data-load-more-container>
        @forelse ($news as $i => $item)
            @if ($i === 0)
                <div class="masonry-lead">
                    <x-news-card :news="$item" type="type5" :showSpot="true" :eager="true" contract="news_card_square" />
                </div>
            @else
                <x-news-card :news="$item" :type="$i % 4 === 0 ? 'type1' : 'type2'" :showSpot="$i % 4 === 1" contract="news_card_square" />
            @endif
        @empty
            <p class="search-empty">{{ __('ui.no_news') }}</p>
        @endforelse
    </div>

    @if ($news->hasMorePages())
        <div class="load-more-wrap">
            <a class="load-more" href="{{ $news->nextPageUrl() }}"
               data-load-more data-next-url="{{ $news->nextPageUrl() }}"
               data-container="[data-load-more-container]">{{ __('ui.list') }}</a>
        </div>
    @endif
@endsection