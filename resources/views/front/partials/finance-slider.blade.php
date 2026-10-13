@php
    $groups = $financeGroups ?? [];
    // Referansta finance-slider sırası ve etiketleri (ölçüldü: 8 slayt).
    $tickers = [
        ['code' => 'USDTRY', 'slug' => 'dolar',        'label' => 'USD/TRY'],
        ['code' => 'EURTRY', 'slug' => 'euro',         'label' => 'EUR/TRY'],
        ['code' => 'GAU',    'slug' => 'altin-ons',    'label' => 'ALTIN/ONS'],
        ['code' => 'EURUSD', 'slug' => 'eur-usd',      'label' => 'EUR/USD'],
        ['code' => 'TR2Y',   'slug' => 'faiz',         'label' => 'FAİZ'],
        ['code' => 'BRENT',  'slug' => 'brent-petrol', 'label' => 'BRENT'],
        ['code' => 'BDIY',   'slug' => 'bdiy',         'label' => 'BALTIK KURU YÜK'],
        ['code' => 'XU100',  'slug' => 'bist-100',     'label' => 'BIST 100'],
    ];

    $market = app(\App\Services\Market\MarketDataService::class);

    $tickerSlides = collect($tickers)->map(function (array $t) use ($groups, $market) {
        $quote = collect($groups)->flatMap(fn ($g) => $g['quotes'] ?? collect())
            ->first(fn ($q) => $q->code() === $t['code'] || $q->instrument?->code === $t['code']);
        if (!$quote) {
            return null;
        }
        $dir = $quote->direction();
        $color = $dir === 'up' ? 'text-[#24A278]'
            : ($dir === 'down' ? 'text-[#BD1B2E]' : 'text-[#9D9D9D]');
        $icon = $dir === 'up' ? 'bloomberght-icon-font-icon-graphic-up'
            : ($dir === 'down' ? 'bloomberght-icon-font-icon-graphic-down' : 'bloomberght-icon-font-icon-graphic-fixed');
        $pct = (float) $quote->changePercent;
        $pctStr = ($pct < 0 ? '-' : '') . number_format(abs($pct), 2, ',', '');
        $colDir = $dir === 'up' ? '1' : ($dir === 'down' ? '-1' : '0');
        $spark = $market->sparkline($quote->instrument, 24);
        $sparkAt = \Carbon\CarbonImmutable::now();
        $sparkTimes = collect($spark)->keys()
            ->map(fn ($i) => $sparkAt->subSeconds((count($spark) - 1 - $i) * 900)->format('H:i'))
            ->all();

        return [
            ...$t,
            'quote' => $quote,
            'color' => $color,
            'icon' => $icon,
            'pctStr' => $pctStr,
            'colDir' => $colDir,
            'sparkJson' => json_encode(array_map(fn ($v) => round((float) $v, 2), $spark)),
            'sparkTimesJson' => json_encode($sparkTimes),
        ];
    })->filter()->values();
@endphp

{{-- Piyasa ticker (referans finance-slider): tek satır swiper + en sağda Hisse Ara.
     Grafik = referans gibi <canvas> (data-type="foreignCurrency"), geçiş speed:300. --}}
<div class="finance-slider-wrap hidden md:block relative">
    <div data-widget-type="finance-slider" data-connection="true" class="bg-white border-b min-h-20 max-h-20 overflow-hidden">
        <div data-type="swiper" class="container swiper slider-custom relative min-h-20 max-h-20 mx-auto pr-12 z-10 max-w-[1280px]"
             data-swiper='{"slidesPerView":"auto","spaceBetween":0,"speed":300,"loop":true,"autoplay":{"delay":3000,"disableOnInteraction":false,"pauseOnMouseEnter":true}}'>
            <div class="swiper-wrapper">
                @foreach ($tickerSlides as $slide)
                    <div class="swiper-slide w-auto min-h-20 py-3 border-l px-4 security-{{ $slide['slug'] }}"
                         data-swiper-slide-index="{{ $loop->index }}">
                        <div data-type="stats-bar-type1">
                            <a href="{{ $slide['quote']->instrument->url }}" title="{{ $slide['label'] }}" class="flex gap-4">
                                <div class="flex flex-col relative setColor">
                                    <span class="text-xs text-ellipsis overflow-hidden whitespace-nowrap">{{ $slide['label'] }}</span>
                                    <span class="setTextColor lastPrice font-bold {{ $slide['color'] }}">{{ $slide['quote']->formattedLast() }}</span>
                                    <span class="text-xs">
                                        <span class="setIcon setTextColor {{ $slide['icon'] }} {{ $slide['color'] }}"></span>
                                        <span class="percentChange">{{ $slide['pctStr'] }}</span>
                                    </span>
                                </div>
                                <div data-name="security-{{ $slide['slug'] }}" class="graphChart-container max-w-[6rem] m-auto h-auto relative hidden md:block">
                                    <canvas class="graphChart-wrapper w-full h-full" data-type="foreignCurrency" data-colordirection="{{ $slide['colDir'] }}" data-spark="{{ $slide['sparkJson'] }}" data-spark-times="{{ $slide['sparkTimesJson'] }}" width="96" height="48"></canvas>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="absolute inset-0 z-20 pointer-events-none">
        <div class="container mx-auto h-full relative max-w-[1280px]">
            <div class="finance-slider-search absolute right-0 top-1/2 -translate-y-1/2 w-full pointer-events-auto bg-gradient-to-l from-white pl-10 from-80% to-transparent h-20 max-w-[173px] flex items-center justify-end border-b">
                <div data-widget-type="search-alphabet" class="text-sm font-bold">
                    <form data-widget-type="search-bar" class="w-full h-11" action="{{ \App\Support\Locale::url('/arama') }}" method="get" role="search">
                        <div class="relative block w-full h-full">
                            <input type="search" name="q" autocomplete="off" placeholder="Hisse Ara..."
                                   class="autocompleteInput w-full h-full pr-10 outline-none bg-white text-gray-700 border py-2 px-4 text-xs">
                            <i class="bloomberght-icon-font-search search-button absolute right-4 top-1/2 -translate-y-1/2 text-gray-700"></i>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Mobil finance-slider (referans ikinci finance-slider, md:hidden) --}}
<div class="finance-slider-wrap flex md:hidden border-t">
    <div data-widget-type="finance-slider" data-connection="true" class="bg-white border-b min-h-20 max-h-20 overflow-hidden w-full">
        <div data-type="swiper" class="swiper slider-custom relative min-h-20 max-h-20 w-full"
             data-swiper='{"slidesPerView":"auto","spaceBetween":0,"speed":300,"loop":true,"autoplay":{"delay":3000,"disableOnInteraction":false,"pauseOnMouseEnter":true}}'>
            <div class="swiper-wrapper">
                @foreach ($tickerSlides as $slide)
                    <div class="swiper-slide w-auto py-3 border-l px-4 security-{{ $slide['slug'] }}">
                        <div data-type="stats-bar-type1">
                            <a href="{{ $slide['quote']->instrument->url }}" title="{{ $slide['label'] }}" class="flex gap-4">
                                <div class="flex flex-col relative setColor">
                                    <span class="text-xs text-ellipsis overflow-hidden whitespace-nowrap">{{ $slide['label'] }}</span>
                                    <span class="setTextColor lastPrice font-bold {{ $slide['color'] }}">{{ $slide['quote']->formattedLast() }}</span>
                                    <span class="text-xs">
                                        <span class="setIcon setTextColor {{ $slide['icon'] }} {{ $slide['color'] }}"></span>
                                        <span class="percentChange">{{ $slide['pctStr'] }}</span>
                                    </span>
                                </div>
                                <div data-name="security-{{ $slide['slug'] }}" class="graphChart-container max-w-[6rem] m-auto h-auto relative hidden md:block">
                                    <canvas class="graphChart-wrapper w-full h-full" data-type="foreignCurrency" data-colordirection="{{ $slide['colDir'] }}" data-spark="{{ $slide['sparkJson'] }}" data-spark-times="{{ $slide['sparkTimesJson'] }}" width="96" height="48"></canvas>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
