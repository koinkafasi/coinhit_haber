@extends('front.layouts.app')

@section('title', __('market.kap_news').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('market.borsa_kap_news_desc'))

@section('content')
    <div class="borsa-page">
        <h1 class="page-title font-unna font-bold">{{ __('market.kap_news') }}</h1>

        @include('borsa.partials.quick-links')

        <div class="borsa-layout">
            <div class="borsa-main">
                @include('borsa.partials.kap-list', ['disclosures' => $disclosures])

                @include('borsa.partials.pagination', ['paginator' => $disclosures])

                <section class="kap-videos">
                    <x-section-head :title="__('market.featured_videos')" :href="\App\Support\Locale::url('/video')" />
                    <div class="video-list">
                        @foreach ($videos as $episode)
                            <article class="video-card">
                                <a href="{{ $episode->program?->url ?? '/video' }}">
                                    <figure class="video-thumb">
                                        <x-lazy-image :src="asset($episode->thumbnail ?: '/img/og-default.png')" alt="" contract="video_thumb" class="img-contract" />
                                        <span class="program-duration">{{ $episode->duration }}</span>
                                    </figure>
                                    <h3 class="video-title font-unna font-bold line-clamp-2">{{ $episode->display_title }}</h3>
                                </a>
                            </article>
                        @endforeach
                    </div>
                </section>
            </div>

            <aside class="borsa-aside">
                @include('borsa.partials.parities-sidebar', ['parities' => $parities])
                <x-ad-zone :zone="1257" :index="1" />
            </aside>
        </div>

        @include('altin.partials.cme-note')
    </div>
@endsection