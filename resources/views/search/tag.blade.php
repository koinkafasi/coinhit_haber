@extends('front.layouts.app')

@section('title', $label.' — '.__('ui.news_suffix').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', $label.' '.__('ui.news_suffix').' — '.($siteSettings['site_tagline'] ?? ''))
@section('canonical', url(\App\Support\Locale::url('/haberleri/'.$tag)))

@section('content')
    <div class="search-layout">
        <div class="search-main">
            <x-breadcrumb :items="[
                ['label' => __('ui.news_title'), 'url' => \App\Support\Locale::url('/haberler')],
                ['label' => '#'.$tag, 'url' => null],
            ]" />

            <div class="section-title bht-page-band">
                <h1 class="font-unna font-bold">{{ $label }} {{ __('ui.news_suffix') }}</h1>
            </div>

            <p class="search-summary">
                <strong>&ldquo;{{ $label }}&rdquo;</strong> {{ __('ui.search_total', ['count' => $results->total()]) }}
                <span class="search-updated">{{ __('ui.updated') }}: {{ now()->translatedFormat('d.m.Y H:i') }}</span>
            </p>

            @include('partials.news-list', ['items' => $results, 'showSpot' => true, 'showAge' => true])

            @include('borsa.partials.pagination', ['paginator' => $results])

            @include('partials.related-tags', ['tags' => $relatedTags])
        </div>

        <aside class="borsa-aside">
            @include('partials.market-widget-compact')
        </aside>
    </div>
@endsection