{{-- Aside mini quote kartları: USD/TRY, EUR/TRY, EUR/USD (spec §8 / §15.4) --}}
@php
    $directionClass = function ($q) {
        return $q->direction() === 'up' ? 'text-green-700' : ($q->direction() === 'down' ? 'text-red-700' : 'text-gray-400');
    };
@endphp

<div class="mini-quote-cards">
    @foreach ($miniCards as $card)
        @php
            $q = $card['quote'];
            $instrument = $q->instrument;
            $href = $instrument->group === \App\Models\MarketInstrument::GROUP_PARITY
                ? $instrument->parityUrl()
                : $instrument->currencyUrl();
        @endphp
        <article class="mini-quote-card" data-market-code="{{ $q->code() }}">
            <header class="mini-quote-head">
                <a class="mini-quote-name" href="{{ $href }}">{{ $instrument->name }}</a>
                <div class="mini-quote-price">
                    <span class="lastPrice font-bold {{ $directionClass($q) }}" data-market-field="last">{{ $q->formattedLast() }}</span>
                    <span class="percentChange {{ $directionClass($q) }}" data-market-field="change_percent">{{ $q->formattedChangePercent() }}</span>
                </div>
            </header>

            <dl class="mini-quote-stats">
                <div><dt>{{ __('market.bid') }}</dt><dd data-market-field="bid">{{ $q->formattedBid() }}</dd></div>
                <div><dt>{{ __('market.ask') }}</dt><dd data-market-field="ask">{{ $q->formattedAsk() }}</dd></div>
                <div><dt>{{ __('market.change') }}</dt><dd class="{{ $directionClass($q) }}" data-market-field="change">{{ $q->formattedChange() }}</dd></div>
                <div><dt>{{ __('market.previous_close') }}</dt><dd data-market-field="previous_close">{{ $q->format($q->previousClose) }}</dd></div>
                <div><dt>{{ __('market.high_max') }}</dt><dd data-market-field="high">{{ $q->format($q->high) }}</dd></div>
                <div><dt>{{ __('market.low_min') }}</dt><dd data-market-field="low">{{ $q->format($q->low) }}</dd></div>
            </dl>

            <div class="mini-quote-chart">
                <div class="mini-quote-tabs" role="tablist" aria-label="{{ __('market.chart_time_range_aria') }}">
                    <button type="button" class="tab-button" data-tab="tab-0" aria-selected="false">1G</button>
                    <button type="button" class="tab-button" data-tab="tab-1" aria-selected="false">1H</button>
                    <button type="button" class="tab-button is-active" data-tab="tab-2" aria-selected="true">{{ __('market.all_range') }}</button>
                </div>
                <div class="mini-quote-sparkline">
                    {!! $card['svg'] !!}
                </div>
            </div>

            <p class="mini-quote-updated text-xs">{{ __('ui.updated') }}: <span data-market-field="updated">{{ $q->updatedAt->format((string) config('market.timestamp_format', 'Y/m/d H:i:s')) }}</span></p>
        </article>
    @endforeach
</div>