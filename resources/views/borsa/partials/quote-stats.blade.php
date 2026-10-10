{{-- Fiyat istatistik kutusu (spec §8 hisse/endeks detay) --}}
@php
    $stats = [
        ['label' => 'ALIŞ', 'value' => $quote->formattedBid(), 'field' => 'bid'],
        ['label' => 'SATIŞ', 'value' => $quote->formattedAsk(), 'field' => 'ask'],
        ['label' => 'FARK', 'value' => $quote->formattedChange(), 'field' => 'change', 'color' => true],
        ['label' => 'ÖNCEKİ KAPIŞ', 'value' => $quote->format($quote->previousClose), 'field' => 'previous_close'],
        ['label' => 'EN YÜKSEK', 'value' => $quote->format($quote->high), 'field' => 'high'],
        ['label' => 'EN DÜŞÜK', 'value' => $quote->format($quote->low), 'field' => 'low'],
        ['label' => 'HACİM', 'value' => $quote->formattedVolumeTl(), 'field' => 'volume_tl'],
        ['label' => 'HACİM(LOT)', 'value' => $quote->formattedVolumeLot(), 'field' => 'volume_lot'],
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