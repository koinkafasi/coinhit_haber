@extends('front.layouts.app')

@section('title', $pageTitle)
@section('description', $pageDescription)
@section('canonical', url(\App\Support\Locale::url('/piyasalar')))
@section('og_type', 'website')
@section('og_image', url('/img/manifest-192.png'))
@section('og_image_width', '180')
@section('og_image_height', '180')

@if (\App\Models\Advertisement::forZone('23022'))
    @section('masthead')
        <x-ad-zone :zone="23022" index="1" variant="masthead" />
    @endsection
@endif

@section('content')
    <div class="markets-layout">
        <x-breadcrumb :items="[['label' => __('ui.markets'), 'url' => \App\Support\Locale::url('/piyasalar')]]" />

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

    @php($__adMarL = \App\Models\Advertisement::forZone('2228'))
    @if ($__adMarL)
        <div class="pageskin pageskin-left" id="ciner-pageskin-left" data-device="DESKTOP" data-zone-id="zone_2228">
            @include('partials.ad-asset', ['ad' => $__adMarL])
        </div>
    @endif
    @php($__adMarR = \App\Models\Advertisement::forZone('2229'))
    @if ($__adMarR)
        <div class="pageskin pageskin-right" id="ciner-pageskin-right" data-device="DESKTOP" data-zone-id="zone_2229">
            @include('partials.ad-asset', ['ad' => $__adMarR])
        </div>
    @endif
@endsection