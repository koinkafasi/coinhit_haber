@extends('front.layouts.app')

@section('title', __('media.tv').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('media.tv_index_desc'))
@section('canonical', url(\App\Support\Locale::url('/tv')))

@section('content')
    <div class="tv-page">
        {{-- Canlı Yayın (h2) — canlı player + kırmızı nokta + şu anki program --}}
        <section class="tv-live" id="canli-yayin">
            <x-section-head :title="__('media.live_broadcast')" />
            <div class="tv-live-player" data-widget-type="live-stream">
                <div class="live-player-frame">
                    <span class="live-dot" aria-hidden="true"></span>
                    <span class="live-badge">{{ __('media.live_badge') }}</span>
                </div>
                @if ($liveProgram)
                    <div class="tv-live-now">
                        <span class="tv-live-label">{{ __('media.now_on_air') }}</span>
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
            <x-section-head :title="__('media.broadcast_schedule')" level="h1" :href="\App\Support\Locale::url('/yayinakisi')" :allLabel="__('ui.trend_all')" />
            @include('tv.partials.schedule', ['days' => $days])
        </section>
    </div>
@endsection