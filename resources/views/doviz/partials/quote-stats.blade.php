{{-- Döviz/parite fiyat istatistik kutusu (spec §8 detay hero) --}}
@php
    $stats = [
        ['label' => __('market.bid'), 'value' => $quote->formattedBid(), 'field' => 'bid'],
        ['label' => __('market.ask'), 'value' => $quote->formattedAsk(), 'field' => 'ask'],
        ['label' => __('market.change'), 'value' => $quote->formattedChange(), 'field' => 'change', 'color' => true],
        ['label' => __('market.previous_close'), 'value' => $quote->format($quote->previousClose), 'field' => 'previous_close'],
        ['label' => __('market.high_max'), 'value' => $quote->format($quote->high), 'field' => 'high'],
        ['label' => __('market.low_min'), 'value' => $quote->format($quote->low), 'field' => 'low'],
    ];
    $dirClass = fn () => $quote->direction() === 'up' ? 'text-green-700' : ($quote->direction() === 'down' ? 'text-red-700' : 'text-gray-400');
@endphp

<dl class="quote-stats" data-market-code="{{ $quote->code() }}">
    @foreach ($stats as $stat)
        <div class="quote-stat">
            <dt>{{ $stat['label'] }}</dt>
            <dd class="{{ $stat['field'] }} {{ ! empty($stat['color']) ? $dirClass() : '' }}"
                data-market-field="{{ $stat['field'] }}">{{ $stat['value'] }}</dd>
        </div>
    @endforeach
</dl>