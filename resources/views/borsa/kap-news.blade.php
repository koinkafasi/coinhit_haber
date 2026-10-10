@extends('front.layouts.app')

@section('title', 'KAP Haberleri | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', 'Kamuyu Aydınlatma Platformu (KAP) bildirimleri: firma açıklamaları ters kronolojik akış.')

@section('content')
    <div class="borsa-page">
        <h1 class="page-title font-unna font-bold">KAP Haberleri</h1>

        @include('borsa.partials.quick-links')

        <div class="borsa-layout">
            <div class="borsa-main">
                @include('borsa.partials.kap-list', ['disclosures' => $disclosures])

                @include('borsa.partials.pagination', ['paginator' => $disclosures])

                <section class="kap-videos">
                    <x-section-head title="Öne Çıkan Videolar" href="/video" />
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
    </div>
@endsection