@extends('front.layouts.app')

@section('title', __('media.fund_title').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('media.fund_index_desc'))

@section('content')
    <div class="fund-page">
        <h1 class="page-title font-unna font-bold">{{ __('media.fund_title') }}</h1>

        {{-- Fon haberleri grid (9 kart, 640x360) — spec §8 --}}
        @if ($fundNews->isNotEmpty())
            <section class="fund-news" id="fon-haberleri">
                <x-section-head :title="__('media.fund_news')" :href="\App\Support\Locale::url('/haberler')" />
                <div class="fund-news-grid">
                    @foreach ($fundNews as $i => $item)
                        <article class="fund-news-card" data-type="news-card-type4" data-news-id="{{ $item->news_id }}">
                            <a class="fund-news-link" href="{{ $item->url }}">
                                <figure class="fund-news-figure">
                                    <x-lazy-image :src="asset($item->image ?: '/img/og-default.png')"
                                                  alt="" contract="news_card" class="fund-news-img" :eager="$i < 3" />
                                </figure>
                                <figcaption class="fund-news-caption">
                                    @if ($item->category)
                                        <div class="category text-xs">{{ $item->category->name }}</div>
                                    @endif
                                    <h3 class="font-unna font-bold fund-news-title line-clamp-2">{{ $item->title }}</h3>
                                </figcaption>
                            </a>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Alt-tool nav (3 buton) --}}
        @include('fund.partials.tool-nav', ['toolLinks' => $toolLinks])

        {{-- Fon Saati — TV program epizot listesi (6 video) --}}
        @if ($fonSaati->isNotEmpty())
            <section class="fund-saati" id="fon-saati">
                <x-section-head :title="__('media.fon_saati')" :href="\App\Support\Locale::url('/tv/program/fon-saati')" />
                <div class="program-list">
                    @foreach ($fonSaati as $episode)
                        <article class="program-card">
                            <a href="{{ $episode->program?->url ?? \App\Support\Locale::url('/tv') }}">
                                <figure class="program-thumb">
                                    <x-lazy-image :src="asset($episode->thumbnail ?: '/img/og-default.png')"
                                                  alt="" contract="video_thumb" class="img-contract" />
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
            </section>
        @endif

        {{-- Günün Özeti — KPI bloğu (canlı sahte veri) --}}
        <section class="fund-kpi" id="gunun-ozeti">
            <x-section-head :title="__('media.today_summary')" />
            <div class="fund-kpi-grid">
                <div class="fund-kpi-card">
                    <span class="fund-kpi-label">{{ __('media.portfolio_value') }}</span>
                    <span class="fund-kpi-value">{{ $kpi['portfolio_value_formatted'] }}</span>
                </div>
                <div class="fund-kpi-card">
                    <span class="fund-kpi-label">{{ __('media.portfolio_pct') }}</span>
                    <span class="fund-kpi-value {{ $kpi['portfolio_change'] > 0 ? 'text-green-700' : ($kpi['portfolio_change'] < 0 ? 'text-red-700' : 'text-gray-400') }}">{{ $kpi['portfolio_change_formatted'] }}</span>
                </div>
                <div class="fund-kpi-card">
                    <span class="fund-kpi-label">{{ __('media.fund_count') }}</span>
                    <span class="fund-kpi-value">{{ $kpi['fund_count_formatted'] }}</span>
                </div>
                <div class="fund-kpi-card">
                    <span class="fund-kpi-label">{{ __('media.investor_count') }}</span>
                    <span class="fund-kpi-value">{{ $kpi['investor_count_formatted'] }}</span>
                </div>
            </div>
        </section>

        {{-- Fon Sınıflarında Bugün — 7 sekme + Yükselen/Düşen toggle + lider tablo --}}
        <section class="fund-classes" id="fon-siniflari" data-widget-type="fund-classes">
            <x-section-head :title="__('media.fund_classes_today')" />

            <div class="fund-class-tabs tab-menu" role="tablist" aria-label="{{ __('media.fund_classes_aria') }}">
                @foreach ($fundClasses as $i => $class)
                    <button type="button" role="tab"
                            class="tab-button fund-class-tab {{ $i === 0 ? 'is-active' : '' }}"
                            data-tab="{{ $class['key'] }}"
                            aria-selected="{{ $i === 0 ? 'true' : 'false' }}">{{ $class['label'] }}</button>
                @endforeach
            </div>

            <div class="fund-direction-toggle" role="group" aria-label="{{ __('media.direction_aria') }}">
                <a class="fund-direction-button {{ $direction === 'yukselen' ? 'is-active' : '' }}"
                   href="{{ \App\Support\Locale::url('/yatirim-fonlari?yon=yukselen#fon-siniflari') }}">{{ __('media.rising_funds') }}</a>
                <a class="fund-direction-button {{ $direction === 'dusen' ? 'is-active' : '' }}"
                   href="{{ \App\Support\Locale::url('/yatirim-fonlari?yon=dusen#fon-siniflari') }}">{{ __('media.falling_funds') }}</a>
            </div>

            <div class="fund-class-panels">
                @foreach ($fundClasses as $i => $class)
                    <div class="tab-content fund-class-panel {{ $i === 0 ? '' : 'hidden' }}" data-id="{{ $class['key'] }}">
                        @include('fund.partials.leaderboard-table', ['rows' => $class['rows']])
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Karşılaştırmalı Fon Getirileri — büyük tablo --}}
        <section class="fund-returns" id="fon-getirileri">
            <x-section-head :title="__('media.comparative_returns')" :href="\App\Support\Locale::url('/yatirim-fonlari/fon-karsilastirma')" :allLabel="__('media.compare_funds')" />
            @include('fund.partials.returns-table', [
                'rows' => $returnRows,
                'showAll' => $showAll,
                'totalFundCount' => $totalFundCount,
            ])
        </section>
    </div>
@endsection