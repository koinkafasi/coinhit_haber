@extends('front.layouts.app')

@section('title', $pageTitle)
@section('description', $pageDescription)
@section('canonical', url('/piyasalar'))
@section('og_type', 'website')
@section('og_image', url('/img/manifest-192.png'))
@section('og_image_width', '180')
@section('og_image_height', '180')

@section('masthead')
    <x-ad-zone :zone="23022" index="1" variant="masthead" />
@endsection

@section('content')
    <div class="pageskin pageskin-left" id="ciner-pageskin-left" data-device="DESKTOP">
        <div class="ads-zone lazy-init" data-adv="bht" data-device="DESKTOP" data-zone-id="zone_2228"></div>
    </div>
    <div class="pageskin pageskin-right" id="ciner-pageskin-right" data-device="DESKTOP">
        <div class="ads-zone lazy-init" data-adv="bht" data-device="DESKTOP" data-zone-id="zone_2229"></div>
    </div>

    <div class="markets-layout">
        <x-breadcrumb :items="[['label' => __('ui.markets'), 'url' => '/piyasalar']]" />

        <div class="section-title bht-page-band">
            <h1 class="font-unna font-bold">{{ __('ui.markets') }}</h1>
        </div>

        @include('home.partials.market-widget')

        @if ($marketNews->isNotEmpty())
            <section class="markets-news">
                <x-section-head :title="__('ui.market_news')" :href="\App\Support\Locale::url('/haberler')" />
                @include('partials.news-list', ['items' => $marketNews, 'showSpot' => true])
            </section>
        @endif

        @include('home.partials.market-tables')

        @if ($videos->isNotEmpty())
            <section class="markets-videos">
                <x-section-head :title="__('ui.video_news')" :href="\App\Support\Locale::url('/video')" />
                <div class="video-grid">
                    @foreach ($videos as $video)
                        @include('video.partials.card', ['episode' => $video])
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection