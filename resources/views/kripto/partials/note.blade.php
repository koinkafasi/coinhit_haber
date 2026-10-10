{{-- Kripto veri kaynağı notu (spec §5, §11.6) --}}
@php
    $marketData = app(\App\Services\Market\MarketDataService::class);
    $cryptoNote = $note ?? $marketData->sourceNote('crypto');
@endphp

@if (! empty($cryptoNote))
    <section class="market-source-note crypto-note">
        <p>{!! nl2br(e($cryptoNote)) !!}</p>
    </section>
@endif