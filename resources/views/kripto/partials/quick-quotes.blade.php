{{-- BTC/ETH/XRP hızlı widget paneli (fiyat + % + sparkline) — spec §15.6 --}}
@php
    $dirClass = fn ($q) => $q->direction() === 'up' ? 'text-green-700' : ($q->direction() === 'down' ? 'text-red-700' : 'text-gray-400');
@endphp

<div class="crypto-quick-cards" id="kripto-hizli-widget">
    @foreach ($quickCards as $card)
        @php
            $q = $card['quote'];
            $instrument = $q->instrument;
            $detailUrl = $instrument->cryptoUrl();
        @endphp
        <article class="mini-quote-card crypto-mini-card" data-market-code="{{ $q->code() }}" data-tab="tab-{{ strtolower($q->code()) }}">
            <header class="mini-quote-head">
                @if ($detailUrl)
                    <a class="mini-quote-name" href="{{ $detailUrl }}">{{ $instrument->name }}</a>
                @else
                    <span class="mini-quote-name">{{ $instrument->name }}</span>
                @endif
                <div class="mini-quote-price">
                    <span class="lastPrice font-bold {{ $dirClass($q) }}" data-market-field="last">{{ $q->formattedUsdPrice() }}</span>
                    <span class="percentChange {{ $dirClass($q) }}" data-market-field="change_percent">{{ $q->formattedChangePercent() }}</span>
                </div>
            </header>

            <div class="mini-quote-chart">
                <div class="mini-quote-sparkline">{!! $card['svg'] !!}</div>
            </div>

            <p class="mini-quote-updated text-xs">{{ __('ui.updated') }}: <span data-market-field="updated">{{ $q->updatedAt->format((string) config('market.timestamp_format', 'Y/m/d H:i:s')) }}</span></p>
        </article>
    @endforeach
</div>