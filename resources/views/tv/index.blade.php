@extends('front.layouts.app')

@section('title', 'TV | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', 'Bloomberg HT canlı yayın ve 7 günlük yayın akışı: günün programları, prime kuşak yayınları, tüm programlar ve frekans listesi.')
@section('canonical', url(\App\Support\Locale::url('/tv')))

@section('content')
    <div class="tv-page">
        {{-- Canlı Yayın (h2) — canlı player + kırmızı nokta + şu anki program --}}
        <section class="tv-live" id="canli-yayin">
            <x-section-head title="Canlı Yayın" />
            <div class="tv-live-player" data-widget-type="live-stream">
                <div class="live-player-frame">
                    <span class="live-dot" aria-hidden="true"></span>
                    <span class="live-badge">CANLI</span>
                </div>
                @if ($liveProgram)
                    <div class="tv-live-now">
                        <span class="tv-live-label">Şu An Yayında</span>
                        <a class="tv-live-program font-unna font-bold" href="{{ $liveProgram->url }}">{{ $liveProgram->program_name }}</a>
                        <span class="tv-live-time">{{ $liveProgram->air_time }}</span>
                        @if ($liveProgram->description)
                            <p class="tv-live-desc">{{ $liveProgram->description }}</p>
                        @endif
                    </div>
                @endif
            </div>
        </section>

        {{-- Yayın Akışı (h1) — 7 gün sekmesi --}}
        <section class="tv-schedule-section" id="yayin-akisi">
            <x-section-head title="Yayın Akışı" level="h1" href="/yayinakisi" allLabel="Tümü" />
            @include('tv.partials.schedule', ['days' => $days])
        </section>
    </div>
@endsection