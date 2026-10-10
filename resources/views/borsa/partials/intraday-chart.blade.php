{{-- Güniçi Verileri grafiği + zaman aralığı seçici (varsayılan "Tümü") — spec §6, §8 --}}
@php($lastUpdated = app(\App\Services\Market\MarketDataService::class)->lastUpdated())
<section class="intraday-chart-block">
    <x-section-head :title="__('market.intraday_data')" :href="null" />

    <div class="intraday-ranges" role="tablist" aria-label="{{ __('market.time_range_aria') }}">
        @foreach (($ranges ?? ['1G', '1H', __('market.all_range')]) as $range)
            <button type="button" role="tab"
                    class="range-btn tab-button {{ $range === __('market.all_range') ? 'is-active' : '' }}"
                    data-range="{{ $range }}"
                    aria-selected="{{ $range === __('market.all_range') ? 'true' : 'false' }}">{{ $range }}</button>
        @endforeach
    </div>

    <div class="intraday-chart highchart-container" data-market-code="{{ $quote->code() }}">
        {!! $chart !!}
    </div>

    <p class="market-updated text-xs">{{ __('ui.updated') }}: <span data-market-updated>{{ $lastUpdated }}</span></p>
</section>