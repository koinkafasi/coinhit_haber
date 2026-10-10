@extends('front.layouts.app')

@section('title', $program->name.' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', \Illuminate\Support\Str::limit(strip_tags((string) $program->description), 150))
@section('canonical', url(\App\Support\Locale::url('/tv/program/'.$program->slug)))
@section('og_type', 'video.tv_show')

@section('content')
    <div class="tv-page tv-program-page">
        <x-breadcrumb :items="[['label' => 'TV', 'url' => \App\Support\Locale::url('/tv')], ['label' => $program->name]]" />

        <header class="program-detail-head">
            <figure class="program-detail-figure">
                <x-lazy-image :src="asset($program->thumbnail ?: '/img/placeholders/placeholder-1.svg')" alt="" contract="video_thumb" class="img-contract" :eager="true" />
            </figure>
            <div class="program-detail-info">
                <span class="program-detail-kicker">{{ $program->is_series ? 'Video Serisi' : 'TV Programı' }}</span>
                <h1 class="program-detail-name font-unna font-bold">{{ $program->name }}</h1>
                <div class="program-detail-meta">
                    @if ($program->host)
                        <span class="program-detail-host">{{ $program->host }}</span>
                    @endif
                    @if ($program->air_time)
                        <span class="program-detail-time">Her gün {{ $program->air_time }}</span>
                    @endif
                </div>
                @if ($program->description)
                    <p class="program-detail-desc">{{ $program->description }}</p>
                @endif
                <div class="tv-action-links">
                    <a class="tv-action-link" href="{{ \App\Support\Locale::url('/yayinakisi') }}">Yayın Akışı</a>
                    @if ($program->is_series)
                        <a class="tv-action-link" href="{{ \App\Support\Locale::url('/video/'.$program->slug) }}">Seri Sayfası</a>
                    @endif
                </div>
            </div>
        </header>

        <section class="program-archive" id="bolumler">
            <x-section-head title="Bölümler" />

            @if ($episodes->isEmpty())
                <p class="tv-empty">Bu program için henüz bölüm bulunmuyor.</p>
            @else
                <div class="program-list">
                    @foreach ($episodes as $episode)
                        <article class="program-card">
                            <a href="{{ \App\Support\Locale::url('/'.$program->slug.'/video/'.$episode->slug.'/'.$episode->id) }}">
                                <figure class="program-thumb">
                                    <x-lazy-image :src="asset($episode->thumbnail ?: $program->thumbnail ?: '/img/placeholders/placeholder-1.svg')" alt="" contract="video_thumb" class="img-contract" />
                                    <span class="program-duration">{{ $episode->duration }}</span>
                                </figure>
                                <h3 class="program-title font-unna font-bold line-clamp-2">{{ $episode->display_title }}</h3>
                                @if ($episode->air_at)
                                    <div class="program-date">{{ $episode->air_at->translatedFormat('d F Y, H:i') }}</div>
                                @endif
                            </a>
                        </article>
                    @endforeach
                </div>

                @if ($episodes->hasPages())
                    @include('borsa.partials.pagination', ['paginator' => $episodes])
                @endif
            @endif
        </section>

        @if ($related->isNotEmpty())
            <section class="program-related">
                <x-section-head title="Diğer Programlar" />
                <div class="program-grid">
                    @foreach ($related as $item)
                        <article class="program-grid-card">
                            <a href="{{ \App\Support\Locale::url('/tv/program/'.$item->slug) }}">
                                <figure class="program-thumb">
                                    <x-lazy-image :src="asset($item->thumbnail ?: '/img/placeholders/placeholder-1.svg')" alt="" contract="video_thumb" class="img-contract" />
                                </figure>
                                <h3 class="program-grid-name font-unna font-bold">{{ $item->name }}</h3>
                            </a>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection