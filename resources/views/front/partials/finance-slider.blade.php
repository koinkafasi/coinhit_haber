@php
    $groups = $financeGroups ?? [];
    $tickerCodes = ['XU100', 'USDTRY', 'EURTRY', 'GAU', 'EURUSD', 'TR2Y', 'BRENT'];
    $tickerLabels = [
        'XU100' => 'BIST 100',
        'USDTRY' => 'USD/TRY',
        'EURTRY' => 'EUR/TRY',
        'GAU' => 'ALTIN/ONS',
        'EURUSD' => 'EUR/USD',
        'TR2Y' => 'FAİZ',
        'BRENT' => 'BRENT',
    ];

    $tickerQuotes = collect($tickerCodes)
        ->map(function (string $code) use ($groups) {
            foreach ($groups as $group) {
                $quote = $group['quotes']->first(fn ($q) => $q->code() === $code || $q->instrument?->code === $code);
                if ($quote) {
                    return $quote;
                }
            }
            return null;
        })
        ->filter()
        ->values();
@endphp

{{-- Piyasa ticker (referans finance-slider): tek satır swiper + sağda Hisse Ara (CH-27). --}}
<div class="finance-slider-wrap hidden md:block relative">
    <div data-widget-type="finance-slider" data-connection="true" class="bg-white border-b min-h-20 max-h-20 overflow-hidden">
        <div data-type="swiper" class="container swiper slider-custom relative min-h-20 max-h-20 mx-auto pr-12 z-10"
             data-swiper='{"slidesPerView":"auto","spaceBetween":0,"speed":900,"loop":true,"autoplay":{"delay":3000,"disableOnInteraction":false,"pauseOnMouseEnter":true},"navigation":{"nextEl":".finance-band .bloomberght-icon-font-chevron-right","prevEl":".finance-band .bloomberght-icon-font-chevron-left"},"breakpoints":{"0":{"slidesPerView":"auto"}}}'>
            <div class="swiper-wrapper">
                @foreach ($tickerQuotes as $quote)
                    @php
                        $dir = $quote->direction();
                        $dcls = $dir === 'up' ? 'text-[#24A278]' : ($dir === 'down' ? 'text-[#BD1B2E]' : 'text-[#9D9D9D]');
                        $icon = $dir === 'up' ? 'bloomberght-icon-font-icon-graphic-up'
                            : ($dir === 'down' ? 'bloomberght-icon-font-icon-graphic-down' : 'bloomberght-icon-font-icon-graphic-fixed');
                        $label = $tickerLabels[$quote->code()] ?? $tickerLabels[$quote->instrument?->code] ?? $quote->instrument?->name;
                    @endphp
                    <div class="swiper-slide w-auto min-h-20 py-3 border-l px-4 security-{{ $quote->code() }}"
                         data-swiper-slide-index="{{ $loop->index }}">
                        <div data-type="stats-bar-type1">
                            <a href="{{ $quote->instrument->url }}" title="{{ $label }}" class="flex gap-4">
                                <div class="flex flex-col relative">
                                    <span class="text-xs text-ellipsis overflow-hidden whitespace-nowrap">{{ $label }}</span>
                                    <span class="setTextColor lastPrice font-bold {{ $dcls }}">{{ $quote->formattedLast() }}</span>
                                    <span class="text-xs">
                                        <span class="setIcon setTextColor {{ $icon }} {{ $dcls }}"></span>
                                        <span class="percentChange">{{ $quote->formattedChangePercent() }}</span>
                                    </span>
                                </div>
                                <div class="graphChart-container max-w-[6rem] m-auto h-auto relative hidden md:block">
                                    {!! app(\App\Services\Market\MarketDataService::class)->sparklineSvg($quote->instrument, 24, 96, 44) !!}
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="absolute inset-0 z-20 pointer-events-none">
        <div class="container mx-auto h-full relative">
            {{-- Referans finance-band ok düğmeleri (CH-28; arama kutusunun solunda) --}}
            <div class="finance-band text-black bg-white absolute right-[184px] top-0 bottom-0 my-auto w-20 pl-4 z-90 hidden md:flex items-center justify-between gap-1">
                <button aria-label="prev" class="swiper-button-prev w-9 h-9 bloomberght-icon-font-chevron-left cursor-pointer text-inherit"></button>
                <button aria-label="next" class="swiper-button-next w-9 h-9 bloomberght-icon-font-chevron-right cursor-pointer text-inherit"></button>
            </div>
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
<div class="finance-slider-wrap flex md:hidden -mx-4 border-t">
    <div data-widget-type="finance-slider" data-connection="true" class="bg-white border-b min-h-20 max-h-20 overflow-hidden w-full">
        <div data-type="swiper" class="swiper slider-custom relative min-h-20 max-h-20 w-full"
             data-swiper='{"slidesPerView":"auto","spaceBetween":0,"speed":900,"loop":true,"autoplay":{"delay":3000,"disableOnInteraction":false,"pauseOnMouseEnter":true},"breakpoints":{"0":{"slidesPerView":"auto"}}}'>
            <div class="swiper-wrapper">
                @foreach ($tickerQuotes as $quote)
                    @php
                        $mdir = $quote->direction();
                        $mdcls = $mdir === 'up' ? 'text-[#24A278]' : ($mdir === 'down' ? 'text-[#BD1B2E]' : 'text-[#9D9D9D]');
                        $micon = $mdir === 'up' ? 'bloomberght-icon-font-icon-graphic-up'
                            : ($mdir === 'down' ? 'bloomberght-icon-font-icon-graphic-down' : 'bloomberght-icon-font-icon-graphic-fixed');
                        $mlabel = $tickerLabels[$quote->code()] ?? $tickerLabels[$quote->instrument?->code] ?? $quote->instrument?->name;
                    @endphp
                    <div class="swiper-slide w-auto py-3 border-l px-4 security-{{ $quote->code() }}">
                        <div data-type="stats-bar-type1">
                            <a href="{{ $quote->instrument->url }}" title="{{ $mlabel }}" class="flex gap-4">
                                <div class="flex flex-col relative">
                                    <span class="text-xs text-ellipsis overflow-hidden whitespace-nowrap">{{ $mlabel }}</span>
                                    <span class="setTextColor lastPrice font-bold {{ $mdcls }}">{{ $quote->formattedLast() }}</span>
                                    <span class="text-xs">
                                        <span class="setIcon setTextColor {{ $micon }} {{ $mdcls }}"></span>
                                        <span class="percentChange">{{ $quote->formattedChangePercent() }}</span>
                                    </span>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
