{{-- Ana sayfa Piyasa Verileri tabbed widget — referans DOM ile birebir (CH-27).
     Yapı: 5 üst sekme (Döviz/Borsa/Faiz-Bono/Altın-Emtia/Alternatif), her sekme
     swiper içinde alt-tablolar (3 sütun, slidesPerView 1/2/3). --}}
@php
    $pmlGroups = [
        ['key' => 'tab-foreignExchange', 'label' => __('ui.market.doviz'), 'keys' => ['doviz', 'pariteler', 'gelisen-piyasa']],
        ['key' => 'tab-stockExchange', 'label' => __('ui.market.borsa'), 'keys' => ['bist', 'artanlar', 'azalanlar']],
        ['key' => 'tab-interestBonds', 'label' => __('ui.market.faiz_bono'), 'keys' => ['tahvil', 'repo', 'eurobond']],
        ['key' => 'tab-goldCommodities', 'label' => __('ui.market.altin_emtia'), 'keys' => ['altin', 'kiymetli', 'tarim']],
        ['key' => 'tab-alternative', 'label' => __('ui.market.alternatif'), 'keys' => ['alternatif-piyasalar', 'alternatif']],
    ];
    $pmlByKey = collect($marketTables)->keyBy('key');
@endphp

<section data-section="marketDataLists" class="grid grid-cols-12 gap-4 mb-4 md:mb-10 home-piyasa-verileri market-data-widget">
    <div class="bht-grid col-span-12 md:col-span-12 row-span-1" dataType="marketDataLists">
        <div data-widget-type="marketDataLists" class="mdl-root relative">
            <div data-type="background" class="absolute -z-10 left-1/2 top-0 w-[100vw] h-96 -translate-x-1/2 -mt-4 overflow-hidden bg-gray-100 h-full mt-0" aria-hidden="true">
                <picture>
                    <img src="https://im.bloomberght.com/assets/bht/bg/desktop/2x/bg-image-3.png?v=1004" alt="" role="presentation" loading="eager" fetchpriority="high" decoding="async" class="absolute inset-0 w-full h-full object-cover object-[center_top] max-md:object-contain max-md:object-[center_top]">
                </picture>
            </div>
            <div class="mb-4 border-b border-black relative before:absolute before:top-0 before:bottom-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black" aria-hidden="true"></div>
            <a href="{{ \App\Support\Locale::url('/piyasalar') }}" title="{{ __('ui.market_data_title') }}"
               class="md:absolute md:top-4 font-unna font-bold text-3xl mb-2 z-10">{{ __('ui.market_data_title') }}</a>

            <div class="tab-menu md:pt-16">
                <div class="tab-head mdl-tabhead">
                    @foreach ($pmlGroups as $i => $group)
                        <button type="button"
                                class="tab-button mdl-tab {{ $i === 0 ? 'bg-gray-200' : '' }}"
                                data-tab="{{ $group['key'] }}">{{ $group['label'] }}</button>
                    @endforeach
                </div>

                <div class="tab-body">
                    @foreach ($pmlGroups as $i => $group)
                        <div class="tab-content {{ $i === 0 ? '' : 'hidden' }}" data-id="{{ $group['key'] }}">
                            <div data-type="swiper" data-name="" class="swiper slider-custom relative"
                                 data-swiper='{"slidesPerView":1,"autoHeight":true,"spaceBetween":0,"breakpoints":{"0":{"slidesPerView":1,"spaceBetween":0},"768":{"slidesPerView":2,"spaceBetween":10},"1024":{"slidesPerView":3,"spaceBetween":10}}}'>
                                <div class="swiper-wrapper">
                                    @foreach ($group['keys'] as $k)
                                        @php
                                            $t = $pmlByKey[$k] ?? null;
                                        @endphp
                                        @if ($t)
                                            <div class="swiper-slide">
                                                <div data-widget-type="marketDataLists" class="mb-4 md:mb-12 relative overflow-hidden">
                                                    <div data-type="head-type1" class="hidden">
                                                        <div class="relative before:absolute before:top-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black before:bottom-0 before:h-2.5 border-b border-black mb-4"></div>
                                                        <a href="{{ $t['href'] }}" title="{{ $t['title'] }}" class="inline-block mb-3">
                                                            <h2 class="font-unna font-bold text-3xl">{{ $t['title'] }}</h2>
                                                        </a>
                                                    </div>
                                                    <div class="border h-[calc(100%-75px)]">
                                                        @php
                                                            $mcols = $t['columns'] ?? [
                                                                ['label' => __('market.last'), 'field' => 'last', 'class' => 'lastPrice'],
                                                                ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
                                                            ];
                                                            $mquotes = collect($t['quotes'])->take(6)->values();
                                                        @endphp
                                                        <table data-type="table-type1" class="w-full text-center text-sm bg-white mdl-table">
                                                            <thead>
                                                            <tr class="border-b text-sm">
                                                                <th class="sticky left-0 p-4 text-left bg-white">{{ strtoupper($t['title']) }}</th>
                                                                @foreach ($mcols as $mc)
                                                                    <th class="p-4 text-right">{{ $mc['label'] }}</th>
                                                                @endforeach
                                                            </tr>
                                                            </thead>
                                                            <tbody data-connection="true">
                                                            @forelse ($mquotes as $mq)
                                                                @php
                                                                    $mdir = $mq->direction();
                                                                @endphp
                                                                <tr class="border-b text-sm security-{{ $mq->instrument->slug }} {{ $mdir === 'up' ? 'is-up' : ($mdir === 'down' ? 'is-down' : 'is-flat') }}"
                                                                    data-market-code="{{ $mq->code() }}">
                                                                    <td class="py-4 sticky left-0 w-full px-4 bg-white">
                                                                        <a href="{{ $mq->instrument->url }}" class="flex items-center gap-2 text-left cursor-pointer">
                                                                            <i class="q-arrow q-arrow-{{ $mdir }} text-xs" aria-hidden="true"></i>
                                                                            <span class="mdl-cell-name">
                                                                                <span class="font-bold text-base line-clamp-2">{{ $mq->code() }}</span>
                                                                                <span class="text-ellipsis line-clamp-1 text-xs text-gray-500">{{ $mq->instrument->name }}</span>
                                                                            </span>
                                                                        </a>
                                                                    </td>
                                                                    @foreach ($mcols as $mc)
                                                                        @php
                                                                            $mv = $mc['field'] === 'last' ? $mq->formattedLast()
                                                                                : ($mc['field'] === 'change_percent' ? $mq->formattedChangePercent()
                                                                                : ($mc['field'] === 'bid' ? $mq->formattedBid()
                                                                                : ($mc['field'] === 'ask' ? $mq->formattedAsk()
                                                                                : ($mc['field'] === 'change' ? $mq->formattedChange()
                                                                                : $mq->format($mq->last)))));
                                                                            $mcls = $mc['class'] ?? '';
                                                                        @endphp
                                                                        @if (! empty($mc['color']))
                                                                            @php
                                                                                $mcls .= $mdir === 'up' ? ' text-green-700' : ($mdir === 'down' ? ' text-red-700' : ' text-gray-400');
                                                                            @endphp
                                                                        @endif
                                                                        <td class="py-4 px-4 {{ trim($mcls) }}" data-market-field="{{ $mc['field'] }}">
                                                                            <span class="text-right flex justify-end gap-2 whitespace-pre">{{ $mv }} <i class="q-arrow q-arrow-{{ $mdir }}" aria-hidden="true"></i></span>
                                                                        </td>
                                                                    @endforeach
                                                                </tr>
                                                            @empty
                                                                <tr><td colspan="{{ count($mcols) + 1 }}" class="py-4 text-gray-400">{{ __('ui.no_data') }}</td></tr>
                                                            @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
