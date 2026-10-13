{{--
    PİYASALAR kompakt widget (aside/400px) — 4 sekme + enstrüman alt-sekmeleri + mini grafik.
    spec §7 modül 7, §15.12 / §15.13. site.js `market-graphic-tab` seçicileriyle uyumlu.
--}}
<section class="market-widget market-widget-compact" data-widget-type="market-graphic-tab">
    {{-- Kanal sayfalarında (Zirve aside) referans widget'ı başlıksız gelir:
         `data-widget-type="market-graphic-tab"` doğrudan `tab-menu` ile başlar.
         Diğer sayfalarda (arama/altın/haberler) bölüm başlığı gösterilir. --}}
    @isset($head)
        @if ($head)
            <x-section-head :title="__('ui.markets')" :href="\App\Support\Locale::url('/piyasalar')" />
        @endif
    @else
        <x-section-head :title="__('ui.markets')" :href="\App\Support\Locale::url('/piyasalar')" />
    @endisset

    <div class="market-widget-tabs tab-menu" role="tablist" aria-label="{{ __('ui.markets_aria') }}">
        @php($first = true)
        @foreach ($marketWidget as $tab)
            <button type="button" role="tab"
                    class="tab-button market-widget-tab {{ $first ? 'is-active' : '' }}"
                    data-tab="{{ $tab['key'] }}"
                    aria-selected="{{ $first ? 'true' : 'false' }}">{{ $tab['label'] }}</button>
            @php($first = false)
        @endforeach
    </div>

    <div class="market-widget-panels">
        @php($first = true)
        @foreach ($marketWidget as $tab)
            <div class="tab-content market-widget-panel {{ $first ? '' : 'hidden' }}" data-id="{{ $tab['key'] }}">
                <div class="market-widget-subtabs">
                    @foreach ($tab['items'] as $i => $item)
                        <button type="button" class="tab-button market-subtab {{ $i === 0 ? 'is-active' : '' }}"
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
            @php($first = false)
        @endforeach
    </div>
</section>