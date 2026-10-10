@extends('front.layouts.app')

@section('title', 'Video | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', 'Bloomberg HT video serileri: ekonomi, piyasa, teknoloji, tarım ve spor programlarının bölümleri. Tüm video serilerini izleyin.')
@section('canonical', url(\App\Support\Locale::url('/video')))

@section('content')
    <div class="video-page">
        {{-- Hero "Video" (arka plan) --}}
        <section class="video-hero">
            <div class="video-hero-inner">
                <h1 class="video-hero-title font-unna font-bold">Video</h1>
                <p class="video-hero-desc">Bloomberg HT ekranlarındaki program ve serilerin bölümleri.</p>
            </div>
        </section>

        {{-- Kategori / filtre sırası --}}
        <nav class="video-filter" aria-label="Video serileri">
            @foreach ($categories as $category)
                <a class="video-filter-link {{ $loop->first ? 'is-active' : '' }}"
                   href="{{ $category['url'] }}">{{ $category['label'] }}</a>
            @endforeach
        </nav>

        {{-- Seri grid (başlık + TÜMÜ) --}}
        <section class="video-series">
            <x-section-head title="Seriler" href="/video" allLabel="TÜMÜ" />
            <div class="series-grid">
                @foreach ($series as $item)
                    <article class="series-card">
                        <a href="{{ \App\Support\Locale::url('/video/'.$item->slug) }}">
                            <figure class="series-card-figure">
                                <x-lazy-image :src="asset($item->thumbnail ?: '/img/placeholders/placeholder-1.svg')" alt="" contract="video_thumb" class="img-contract" />
                                <span class="series-card-count">{{ $item->episodes_count }} bölüm</span>
                            </figure>
                            <h3 class="series-card-name font-unna font-bold">{{ $item->name }}</h3>
                            <p class="series-card-desc line-clamp-2">{{ $item->description }}</p>
                            <span class="series-card-all">TÜMÜ &raquo;</span>
                        </a>
                    </article>
                @endforeach
            </div>
        </section>

        {{-- Son videolar + Listele lazy-load --}}
        <section class="video-latest">
            <x-section-head title="Son Videolar" />
            <div class="video-list" data-load-more-container>
                @forelse ($videos as $episode)
                    @include('video.partials.card', ['episode' => $episode])
                @empty
                    <p class="tv-empty">Henüz video bulunmuyor.</p>
                @endforelse
            </div>

            @if ($videos->hasMorePages())
                <div class="load-more-wrap">
                    <a class="load-more" href="{{ $videos->nextPageUrl() }}"
                       data-load-more data-next-url="{{ $videos->nextPageUrl() }}"
                       data-container="[data-load-more-container]">{{ __('ui.list') }}</a>
                </div>
            @endif
        </section>
    </div>
@endsection