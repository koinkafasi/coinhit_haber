@extends('front.layouts.app')

@section('title', __('ui.search_title').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('ui.search_title').': '.$query)
@section('robots', 'noindex,follow')

@section('content')
    <div class="search-layout">
        <div class="search-main">
            <x-breadcrumb :items="[['label' => __('ui.search_heading'), 'url' => \App\Support\Locale::url('/arama')]]" />

            <div class="section-title bht-page-band">
                <h1 class="font-unna font-bold">{{ __('ui.search_heading') }}</h1>
            </div>

            @if ($query === '')
                <p class="search-empty">{{ __('ui.search_prompt') }}</p>
            @else
                <p class="search-summary">
                    <strong>&ldquo;{{ $query }}&rdquo;</strong> {{ __('ui.search_total', ['count' => $results->total()]) }}
                    <span class="search-updated">{{ __('ui.updated') }}: {{ now()->translatedFormat('d.m.Y H:i') }}</span>
                </p>

                @include('partials.news-list', ['items' => $results, 'showSpot' => true, 'showAge' => true])

                @include('borsa.partials.pagination', ['paginator' => $results])

                @include('partials.related-tags', ['tags' => $relatedTags])
            @endif
        </div>

        <aside class="borsa-aside">
            @include('partials.market-widget-compact')
        </aside>
    </div>
@endsection