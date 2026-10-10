@extends('front.layouts.app')

@section('title', __('ui.all_news').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('ui.all_news').' — '.($siteSettings['site_tagline'] ?? ''))
@section('canonical', url(\App\Support\Locale::url('/tumhaberler')))

@section('content')
    <div class="tumhaberler-layout">
        <div class="tumhaberler-main">
            <div class="section-title bht-page-band">
                <h1 class="font-unna font-bold">{{ __('ui.all_news') }}</h1>
            </div>

            @include('partials.news-list', ['items' => $news, 'showSpot' => true, 'leadFirst' => true])

            @include('borsa.partials.pagination', ['paginator' => $news])
        </div>

        <aside class="borsa-aside">
            @include('partials.market-widget-compact')
        </aside>
    </div>
@endsection