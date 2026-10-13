@php
    $__widgetTabKeys = ['first', 'second', 'third', 'fourth', 'fifth'];
@endphp

{{-- PİYASALAR büyük widget — referans bloomberght.com marketGraphData DOM'u ile
     birebir (CH-36). 4 sekme (Borsa/Döviz/Emtia/Faiz) + alt-sekmeler + grafikler,
     sağda piyasa haberleri (type2+type1+type3+type1). Sondaki link-type1
     referansta marketGraphData'nın parçası DEĞİLDİR (grafiks modülüne aittir),
     bu yüzden kaldırıldı. --}}
<div class="market-widget h-full" data-widget-type="marketGraphData" id="piyasalar-widget">
    <div class="mb-4 border-b border-black relative before:absolute before:top-0 before:bottom-0 before:m-auto before:w-8/12 before:h-2.5 before:bg-black"></div>
    <a href="{{ \App\Support\Locale::url('/piyasalar') }}" title="" class="inline-block mb-3">
        <h2 class="font-unna font-bold text-3xl">{{ __('ui.markets') }}</h2>
    </a>

    <div class="flex flex-wrap md:flex-nowrap gap-4 w-full">
        <div class="w-full md:w-1/2">
            <div class="pb-4">
                <div data-widget-type="marketGraphData" class="text-sm font-bold">
                    <div data-widget-type="search-bar" class="w-full h-11">
                        <div class="relative block w-full h-full">
                            <input type="text" data-api="ekonomi/borsa/bist/tum/hisseler" data-url="url"
                                   data-value="secLongName" data-name="stockMarket" data-storage="stockMarket"
                                   data-storage-name="allStocks" data-short-value="sec_code" autocomplete="off"
                                   placeholder="{{ __('ui.search_stock') }}"
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
                            @foreach ($marketWidget as $__ti => $tab)
                                <button type="button" class="tab-button inline-block 2xl:text-base text-sm px-5 py-2 rounded-3xl {{ $__ti === 0 ? 'bg-gray-200' : '' }}"
                                      role="tab" data-tab="tab-{{ $__widgetTabKeys[$__ti] ?? 'first' }}"
                                      aria-selected="{{ $__ti === 0 ? 'true' : 'false' }}">{{ $tab['label'] }}</button>
                            @endforeach
                        </div>

                        <div class="tab-body market-widget-panels">
                            @foreach ($marketWidget as $__ti => $tab)
                                <div class="tab-content market-widget-panel {{ $__ti === 0 ? '' : 'hidden' }}" data-id="tab-{{ $__widgetTabKeys[$__ti] ?? 'first' }}">
                                    <div class="tab-menu sub-menu market-widget-charts">
                                        <div class="tab-body">
                                            @foreach ($tab['items'] as $i => $item)
                                                @php($quote = $item['quote'])
                                                @php($dir = $quote->direction())
                                                @php($dcls = $dir === 'up' ? 'text-green-700' : ($dir === 'down' ? 'text-red-700' : 'text-gray-400'))
                                                @php($scls = 'security-' . \Illuminate\Support\Str::slug($quote->instrument->name))
                                                <div class="tab-content market-widget-chart {{ $i === 0 ? 'first-tab' : 'hidden' }}" data-id="tab-graph-{{ $i }}" data-subcontent="{{ $quote->code() }}">
                                                    <div class="flex justify-center 2xl:gap-4 2xl:py-8 2xl:px-4 gap-2 py-4 px-2 {{ $scls }}">
                                                        <span class="setTextColor {{ $dcls }} font-bold text-3xl 2xl:text-3xl lastPrice" data-market-field="last">{{ $quote->formattedLast() }}</span>
                                                        @if ($dir === 'flat')
                                                            <span class="{{ $dcls }} q-arrow q-arrow-flat text-2xl" aria-hidden="true"></span>
                                                        @else
                                                            <span class="setTextColor {{ $dcls }} setIcon bloomberght-icon-font-icon-graphic-{{ $dir }} text-2xl" aria-hidden="true"></span>
                                                        @endif
                                                        <span class="text-3xl 2xl:text-2xl percentChange setTextColor {{ $dcls }}" data-market-field="change_percent">{{ $quote->format($quote->changePercent, 2) }}</span>
                                                    </div>
                                                    <div class="highchart-container mb-4">
                                                        <div class="wrapper border">
                                                            <div id="chart-{{ $tab['sec'] }}-{{ $i }}" class="chart 2xl:max-h-64 max-h-52 bg-white"
                                                                 data-name="marketGraphData"
                                                                 data-name-sec="{{ $tab['sec'] }}"
                                                                 data-response-path="market-graph-data"
                                                                 data-index="{{ $i }}"
                                                                 data-has-items="true"
                                                                 data-key="intraDay"
                                                                 data-tooltip-symbol="₺"
                                                                 data-tooltip-hour="false"
                                                                 data-chart-type="area"
                                                                 data-content-type="mainPage"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="tab-head my-4 cursor-pointer md:2xl:max-h-56 md:max-h-32 overflow-y-auto">
                                            @foreach ($tab['items'] as $i => $item)
                                                @php($quote = $item['quote'])
                                                <div class="tab-button market-subtab p-2 grid grid-cols-4 2xl:text-sm text-xs {{ $i === 0 ? 'bg-gray-200' : '' }}"
                                                     data-tab="tab-graph-{{ $i }}" data-subtab="{{ $quote->code() }}">
                                                    <span class="text-left">{{ $quote->instrument->name }}</span>
                                                    <span class="text-right" data-market-field="last">{{ $quote->formattedLast() }}</span>
                                                    <span class="text-right" data-market-field="change">{{ $quote->formattedChange() }}</span>
                                                    <span class="text-right" data-market-field="change_percent">{{ $quote->formattedChangePercent() }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sağ kolon — referans: piyasa haberleri (type2 + type1 + type3 + type1) --}}
        <div class="w-full md:w-1/2">
            <div class="flex flex-col justify-between h-full md:gap-2">
                @php($__mkt = collect($marketNews)->take(4)->values())
                @if ($__mkt->get(0))
                    <div class="border-b mb-2 pb-2" data-type="news-card-type2" data-news-id="{{ $__mkt->get(0)->news_id }}">
                        <a href="{{ $__mkt->get(0)->url }}" title="{{ $__mkt->get(0)->title }}">
                            <figure class="flex gap-2">
                                <div class="w-4/12 h-full aspect-square bg-gray-50 max-w-40">
                                    <img src="{{ asset($__mkt->get(0)->image ?: '/img/og-default.png') }}" alt="" class="w-full h-full object-fill" loading="lazy" decoding="async">
                                </div>
                                <figcaption class="w-8/12 flex flex-col justify-center space-y-2">
                                    <div class="font-unna font-bold text-base 2xl:text-xl text-ellipsis line-clamp-3">{{ $__mkt->get(0)->title }}</div>
                                </figcaption>
                            </figure>
                        </a>
                    </div>
                @endif
                @if ($__mkt->get(1))
                    <div class="border-b mb-2 pb-2" data-type="news-card-type1" data-news-id="{{ $__mkt->get(1)->news_id }}">
                        <a href="{{ $__mkt->get(1)->url }}" title="{{ $__mkt->get(1)->title }}">
                            <figure><figcaption>
                                <div class="font-unna font-bold text-base 2xl:text-xl text-ellipsis line-clamp-2">{{ $__mkt->get(1)->title }}</div>
                            </figcaption></figure>
                        </a>
                    </div>
                @endif
                @if ($__mkt->get(2))
                    <div class="border-b mb-2 pb-2" data-type="news-card-type3" data-news-id="{{ $__mkt->get(2)->news_id }}">
                        <a href="{{ $__mkt->get(2)->url }}" title="{{ $__mkt->get(2)->title }}">
                            <figure>
                                <div class="w-full h-full aspect-video bg-gray-50 mb-4">
                                    <img src="{{ asset($__mkt->get(2)->image ?: '/img/og-default.png') }}" alt="" class="w-full h-full" loading="lazy" decoding="async">
                                </div>
                                <figcaption class="w-full h-full flex flex-col justify-around">
                                    <div class="font-unna font-bold text-base 2xl:text-xl text-ellipsis line-clamp-2">{{ $__mkt->get(2)->title }}</div>
                                </figcaption>
                            </figure>
                        </a>
                    </div>
                @endif
                @if ($__mkt->get(3))
                    <div class="md:mb-4" data-type="news-card-type1" data-news-id="{{ $__mkt->get(3)->news_id }}">
                        <a href="{{ $__mkt->get(3)->url }}" title="{{ $__mkt->get(3)->title }}">
                            <figure><figcaption>
                                <div class="font-unna font-bold text-base 2xl:text-xl text-ellipsis line-clamp-2">{{ $__mkt->get(3)->title }}</div>
                            </figcaption></figure>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
