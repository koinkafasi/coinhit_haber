@extends('front.layouts.app')

@section('title', __('media.video').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('media.video_index_desc'))
@section('canonical', url(\App\Support\Locale::url('/video')))

@section('content')
    <div class="video-page">
        {{-- Hero "Video" (arka plan) --}}
        <section class="video-hero">
            <div class="video-hero-inner">
                <h1 class="video-hero-title font-unna font-bold">{{ __('media.video') }}</h1>
                <p class="video-hero-desc">{{ __('media.video_hero_desc') }}</p>
            </div>
        </section>

        {{-- Kategori / filtre sırası --}}
        <nav class="video-filter" aria-label="{{ __('media.video_series_aria') }}">
            @foreach ($categories as $category)
                <a class="video-filter-link {{ $loop->first ? 'is-active' : '' }}"
                   href="{{ $category['url'] }}">{{ $category['label'] }}</a>
            @endforeach
        </nav>

        {{-- Seri grid (başlık + TÜMÜ) --}}
        <section class="video-series">
            <x-section-head :title="__('media.series')" :href="\App\Support\Locale::url('/video')" :allLabel="__('ui.all')" />
            <div class="series-grid">
                @foreach ($series as $item)
                    <article class="series-card">
                        <a href="{{ \App\Support\Locale::url('/video/'.$item->slug) }}">
                            <figure class="series-card-figure">
                                <x-lazy-image :src="asset($item->thumbnail ?: '/img/placeholders/placeholder-1.svg')" alt="" contract="video_thumb" class="img-contract" />
                                <span class="series-card-count">{{ $item->episodes_count }} {{ __('media.episode_count') }}</span>
                            </figure>
                            <h3 class="series-card-name font-unna font-bold">{{ $item->name }}</h3>
                            <p class="series-card-desc line-clamp-2">{{ $item->description }}</p>
                            <span class="series-card-all">{{ __('ui.all') }} &raquo;</span>
                        </a>
                    </article>
                @endforeach
            </div>
        </section>

        {{-- Son videolar + Listele lazy-load --}}
        <section class="video-latest">
            <x-section-head :title="__('media.latest_videos')" />
            <div class="video-list" data-load-more-container>
                @forelse ($videos as $episode)
                    @include('video.partials.card', ['episode' => $episode])
                @empty
                    <p class="tv-empty">{{ __('media.no_videos') }}</p>
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