@php
    $firstTab = true;
@endphp

{{-- PİYASALAR büyük widget (Bölüm 7 modül 7, §17.3): 4 sekme + alt-sekmeler + mini grafikler --}}
<section class="market-widget" data-widget-type="marketGraphData" data-section="markethData" id="piyasalar-widget">
    <div class="mb-4 border-b border-black relative before:absolute before:top-0 before:bottom-0 before:m-auto before:w-8/12 before:h-2.5 before:bg-black"></div>
    <a href="{{ \App\Support\Locale::url('/piyasalar') }}" title="" class="inline-block mb-3">
        <h2 class="font-unna font-bold text-3xl">{{ __('ui.markets') }}</h2>
    </a>

    <div class="flex flex-wrap md:flex-nowrap gap-4 w-full market-widget-body">
        <div class="w-full md:w-1/2 market-widget-main">
            <div class="pb-4">
                <div data-widget-type="marketGraphData" class="text-sm font-bold">
                    <div data-widget-type="search-bar" class="w-full h-11">
                        <div class="relative block w-full h-full">
                            <input type="text" autocomplete="off" placeholder="{{ __('ui.search_stock') }}"
                                   class="autocompleteInput w-full h-full pr-10 outline-none bg-white text-gray-700 border py-2 px-4 text-xs">
                            <i class="bloomberght-icon-font-search search-button absolute right-4 top-1/2 -translate-y-1/2 text-gray-700"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="h-[90%]">
                <div data-widget-type="marketGraphData" data-connection="true">
                    <div class="tab-menu h-full flex flex-col justify-around market-widget-tabs" role="tablist" aria-label="{{ __('ui.markets_aria') }}">
                        <div class="tab-head cursor-pointer border rounded-3xl w-full flex justify-between bg-white mb-4 overflow-x-hidden md:overflow-x-scroll lg:overflow-x-hidden">
                            @foreach ($marketWidget as $tab)
                                <span class="tab-button market-widget-tab inline-block 2xl:text-base text-sm px-5 py-2 rounded-3xl {{ $firstTab ? 'bg-gray-200' : '' }}"
                                      role="tab" data-tab="{{ $tab['key'] }}"
                                      aria-selected="{{ $firstTab ? 'true' : 'false' }}">{{ $tab['label'] }}</span>
                                @php($firstTab = false)
                            @endforeach
                        </div>

                        <div class="tab-body market-widget-panels">
                            @php($firstTab = true)
                            @foreach ($marketWidget as $tab)
                                <div class="tab-content market-widget-panel {{ $firstTab ? '' : 'hidden' }}" data-id="{{ $tab['key'] }}">
                                    <div class="tab-menu sub-menu market-widget-charts">
                                        <div class="tab-body">
                                            @foreach ($tab['items'] as $i => $item)
                                                @php($quote = $item['quote'])
                                                @php($dir = $quote->direction())
                                                @php($dcls = $dir === 'up' ? 'text-green-700' : ($dir === 'down' ? 'text-red-700' : 'text-gray-400'))
                                                @php($scls = 'security-' . \\Illuminate\\Support\\Str::slug($quote->instrument->name))
                                                <div class="tab-content market-widget-chart {{ $i === 0 ? 'first-tab' : 'hidden' }}" data-id="tab-graph-{{ $i }}" data-subcontent="{{ $quote->code() }}">
                                                    <div class="flex justify-center 2xl:gap-4 2xl:py-8 2xl:px-4 gap-2 py-4 px-2 {{ $scls }}">
                                                        <span class="{{ $dcls }} font-bold text-3xl 2xl:text-3xl lastPrice" data-market-field="last">{{ $quote->formattedLast() }}</span>
                                                        @if ($dir === 'flat')
                                                            <span class="{{ $dcls }} q-arrow q-arrow-flat text-2xl" aria-hidden="true"></span>
                                                        @else
                                                            <span class="{{ $dcls }} setIcon bloomberght-icon-font-icon-graphic-{{ $dir }} text-2xl" aria-hidden="true"></span>
                                                        @endif
                                                        <span class="text-3xl 2xl:text-2xl percentChange {{ $dcls }}" data-market-field="change_percent">{{ $quote->format($quote->changePercent, 2) }}</span>
                                                    </div>
                                                    <div class="highchart-container mb-4">
                                                        <div class="wrapper border">
                                                            <div class="chart 2xl:max-h-64 max-h-52 bg-white mw-chart-canvas">{!! $item['svg_big'] ?? $item['svg'] !!}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="tab-head market-widget-subtabs">
                                            @foreach ($tab['items'] as $i => $item)
                                                @php($quote = $item['quote'])
                                                <div class="tab-button market-subtab p-2 grid grid-cols-4 2xl:text-sm text-xs {{ $i === 0 ? 'bg-gray-200' : '' }}"
                                                     role="tab" data-tab="tab-graph-{{ $i }}" data-subtab="{{ $quote->code() }}"
                                                     aria-selected="{{ $i === 0 ? 'true' : 'false' }}">
                                                    <span class="text-left">{{ $quote->instrument->name }}</span>
                                                    <span class="text-right" data-market-field="last">{{ $quote->formattedLast() }}</span>
                                                    <span class="text-right" data-market-field="change">{{ $quote->formattedChange() }}</span>
                                                    <span class="text-right" data-market-field="change_percent">{{ $quote->formattedChangePercent() }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                    </div>
                    @php($firstTab = false)
                @endforeach
            </div>
        </div>
                </div>
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

            {{-- Hızlı Linkler (rezerv/hisse/waller/fon ödemeleri vb. trendler) --}}
            <div class="quick-links" data-widget-type="fastLinks">
                <h3 class="mw-aside-title font-unna font-bold">{{ __('ui.quick_links') }}</h3>
                <div class="quick-links-chips">
                    @foreach ($trends as $trend)
                        <a class="chip" href="{{ $trend->url }}"><i class="q-arrow-right" aria-hidden="true"></i>{{ $trend->label }}</a>
                    @endforeach
                </div>
                <a class="mw-aside-all" href="{{ \App\Support\Locale::url('/trendler') }}">{{ __('ui.all') }} &raquo;</a>
            </div>

            {{-- Grafik --}}
            <div class="market-charts-mini" data-widget-type="graphics">
                <h3 class="mw-aside-title font-unna font-bold">{{ __('ui.charts') }}</h3>
                <p class="mw-charts-desc">{{ __('ui.charts_desc') }}</p>
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