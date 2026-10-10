@extends('front.layouts.app')

@section('title', 'Tüm Programlar | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', 'Bloomberg HT program rehberi: haber, ekonomi ve piyasa programları ile dijital video serilerinin tümü.')
@section('canonical', url(\App\Support\Locale::url('/tv/programlar')))

@section('content')
    <div class="tv-page tv-programs-page">
        <x-breadcrumb :items="[['label' => 'TV', 'url' => \App\Support\Locale::url('/tv')], ['label' => 'Tüm Programlar']]" />

        <x-section-head title="Tüm Programlar" level="h1" />

        @if ($tvPrograms->isNotEmpty())
            <h2 class="tv-group-title font-unna font-bold">Televizyon Programları</h2>
            <div class="program-grid">
                @foreach ($tvPrograms as $program)
                    <article class="program-grid-card">
                        <a href="{{ \App\Support\Locale::url('/tv/program/'.$program->slug) }}">
                            <figure class="program-thumb">
                                <x-lazy-image :src="asset($program->thumbnail ?: '/img/placeholders/placeholder-1.svg')" alt="" contract="video_thumb" class="img-contract" />
                            </figure>
                            <h3 class="program-grid-name font-unna font-bold">{{ $program->name }}</h3>
                            @if ($program->host)
                                <span class="program-grid-host">{{ $program->host }}</span>
                            @endif
                            @if ($program->air_time)
                                <span class="program-grid-time">{{ $program->air_time }}</span>
                            @endif
                        </a>
                    </article>
                @endforeach
            </div>
        @endif

        @if ($series->isNotEmpty())
            <h2 class="tv-group-title font-unna font-bold">Video Serileri</h2>
            <div class="program-grid">
                @foreach ($series as $program)
                    <article class="program-grid-card">
                        <a href="{{ \App\Support\Locale::url('/video/'.$program->slug) }}">
                            <figure class="program-thumb">
                                <x-lazy-image :src="asset($program->thumbnail ?: '/img/placeholders/placeholder-1.svg')" alt="" contract="video_thumb" class="img-contract" />
                            </figure>
                            <h3 class="program-grid-name font-unna font-bold">{{ $program->name }}</h3>
                            @if ($program->host)
                                <span class="program-grid-host">{{ $program->host }}</span>
                            @endif
                            <span class="program-grid-time">{{ $program->episodes_count }} bölüm</span>
                        </a>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
@endsection