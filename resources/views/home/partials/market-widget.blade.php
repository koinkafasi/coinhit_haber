@php
    $firstTab = true;
@endphp

{{-- PİYASALAR büyük widget (Bölüm 7 modül 7, §17.3): 4 sekme + alt-sekmeler + mini grafikler --}}
<section class="market-widget" data-widget-type="market-graphic-tab" id="piyasalar-widget">
    <x-section-head :title="__('ui.markets')" :href="\App\Support\Locale::url('/piyasalar')" />

    <div class="market-widget-body">
        <div class="market-widget-main">
            <div class="market-widget-tabs tab-menu" role="tablist" aria-label="{{ __('ui.markets_aria') }}">
                @foreach ($marketWidget as $tab)
                    <button type="button" role="tab"
                            class="tab-button market-widget-tab {{ $firstTab ? 'is-active' : '' }}"
                            data-tab="{{ $tab['key'] }}"
                            aria-selected="{{ $firstTab ? 'true' : 'false' }}">{{ $tab['label'] }}</button>
                    @php($firstTab = false)
                @endforeach
            </div>

            <div class="market-widget-panels">
                @php($firstTab = true)
                @foreach ($marketWidget as $tab)
                    <div class="tab-content market-widget-panel {{ $firstTab ? '' : 'hidden' }}" data-id="{{ $tab['key'] }}">
                        <div class="market-widget-subtabs">
                            @foreach ($tab['items'] as $i => $item)
                                <button type="button"
                                        class="tab-button market-subtab {{ $i === 0 ? 'is-active' : '' }}"
                                        data-subtab="{{ $item['quote']->code() }}">{{ $item['quote']->instrument->name }}</button>
                            @endforeach
                        </div>

                        <div class="market-widget-charts">
                            @foreach ($tab['items'] as $i => $item)
                                @php($quote = $item['quote'])
                                <div class="market-widget-chart {{ $i === 0 ? '' : 'hidden' }}" data-subcontent="{{ $quote->code() }}">
                                    <div class="mw-chart-head">
                                        <span class="mw-chart-name">{{ $quote->instrument->name }}</span>
                                        <span class="mw-chart-price lastPrice font-bold {{ $quote->direction() === 'up' ? 'text-green-700' : ($quote->direction() === 'down' ? 'text-red-700' : 'text-gray-400') }}">{{ $quote->formattedLast() }}</span>
                                        <span class="mw-chart-change percentChange {{ $quote->direction() === 'up' ? 'text-green-700' : ($quote->direction() === 'down' ? 'text-red-700' : 'text-gray-400') }}">{{ $quote->formattedChangePercent() }}</span>
                                    </div>
                                    <div class="mw-chart-canvas highchart-container">{!! $item['svg'] !!}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @php($firstTab = false)
                @endforeach
            </div>
        </div>

        <aside class="market-widget-aside">
            {{-- Piyasa haberleri --}}
            <div class="market-news">
                <h3 class="mw-aside-title font-unna font-bold">{{ __('ui.market_news') }}</h3>
                <ul class="mw-news-list">
                    @foreach ($marketNews as $item)
                        <li>
                            <a href="{{ $item->url }}">
                                <span class="mw-news-title">{{ $item->title }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <a class="mw-aside-all" href="{{ \App\Support\Locale::url('/piyasalar') }}">{{ __('ui.all') }} &raquo;</a>
            </div>

            {{-- Hızlı Linkler (trend cipsleri) --}}
            <div class="quick-links">
                <h3 class="mw-aside-title font-unna font-bold">{{ __('ui.quick_links') }}</h3>
                <div class="quick-links-chips">
                    @foreach ($trends as $trend)
                        <a class="chip" href="{{ \App\Support\Locale::url('/trendler') }}">{{ $trend->label }}</a>
                    @endforeach
                </div>
                <a class="mw-aside-all" href="{{ \App\Support\Locale::url('/trendler') }}">{{ __('ui.all') }} &raquo;</a>
            </div>

            {{-- Grafik --}}
            <div class="market-charts-mini">
                <h3 class="mw-aside-title font-unna font-bold">{{ __('ui.charts') }}</h3>
                <div class="mw-charts-grid">
                    @foreach ($charts as $chart)
                        <a class="mw-chart-thumb" href="{{ $chart->url }}">
                            <x-lazy-image :src="asset($chart->image ?: '/img/og-default.png')" alt="" width="120" height="68" class="img-contract" />
                            <span class="mw-chart-thumb-title line-clamp-2">{{ $chart->title }}</span>
                        </a>
                    @endforeach
                </div>
                <a class="mw-aside-all" href="{{ \App\Support\Locale::url('/grafik-arsivi') }}">{{ __('ui.all') }} &raquo;</a>
            </div>
        </aside>
    </div>
</section>