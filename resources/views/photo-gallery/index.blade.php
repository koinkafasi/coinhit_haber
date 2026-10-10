@extends('front.layouts.app')

@section('title', __('ui.photo_news').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('ui.photo_news').' — '.($siteSettings['site_tagline'] ?? ''))
@section('canonical', url(\App\Support\Locale::url('/fotohaber')))

@section('content')
    <div class="section-title bht-page-band">
        <h1 class="font-unna font-bold">{{ __('ui.photo_news') }}</h1>
    </div>

    <div class="gallery-grid" data-load-more-container>
        @forelse ($galleries as $gallery)
            <article class="gallery-card">
                <a href="{{ $gallery->url }}">
                    <figure>
                        <x-lazy-image :src="asset($gallery->cover ?: '/img/og-default.png')" alt=""
                                      contract="news_card_square" class="img-contract" />
                        @if ($gallery->photo_total_count)
                            <span class="gallery-count">{{ $gallery->photo_total_count }} {{ __('ui.photo_count') }}</span>
                        @endif
                    </figure>
                    <div class="gallery-body">
                        @if ($gallery->category?->name)
                            <div class="category text-xs">{{ $gallery->category->name }}</div>
                        @endif
                        <h4 class="font-unna font-bold line-clamp-2">{{ $gallery->title }}</h4>
                    </div>
                </a>
            </article>
        @empty
            <p class="search-empty">{{ __('ui.no_news') }}</p>
        @endforelse
    </div>

    @if ($galleries->hasMorePages())
        <div class="load-more-wrap">
            <a class="load-more" href="{{ $galleries->nextPageUrl() }}"
               data-load-more data-next-url="{{ $galleries->nextPageUrl() }}"
               data-container="[data-load-more-container]">{{ __('ui.list') }}</a>
        </div>
    @endif
@endsection