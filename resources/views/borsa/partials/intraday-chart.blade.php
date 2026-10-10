{{-- Güniçi Verileri grafiği + zaman aralığı seçici (varsayılan "Tümü") — spec §6, §8 --}}
@php($lastUpdated = app(\App\Services\Market\MarketDataService::class)->lastUpdated())
<section class="intraday-chart-block">
    <x-section-head title="Güniçi Verileri" :href="null" />

    <div class="intraday-ranges" role="tablist" aria-label="Zaman aralığı">
        @foreach (($ranges ?? ['1G', '1H', 'Tümü']) as $range)
            <button type="button" role="tab"
                    class="range-btn tab-button {{ $range === 'Tümü' ? 'is-active' : '' }}"
                    data-range="{{ $range }}"
                    aria-selected="{{ $range === 'Tümü' ? 'true' : 'false' }}">{{ $range }}</button>
        @endforeach
    </div>

    <div class="intraday-chart highchart-container" data-market-code="{{ $quote->code() }}">
        {!! $chart !!}
    </div>

    <p class="market-updated text-xs">Güncelleme: <span data-market-updated>{{ $lastUpdated }}</span></p>
</section>