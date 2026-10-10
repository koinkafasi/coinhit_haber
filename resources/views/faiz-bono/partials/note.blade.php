{{-- Faiz-Bono veri kaynağı gecikme notu (spec §5, §11.6, §15.7) --}}
@php
    $marketData = app(\App\Services\Market\MarketDataService::class);
    $foreksNote = $note ?? $marketData->sourceNote('foreks');
    $foreksLicense = $marketData->licenseNote('foreks');
@endphp

@if (! empty($foreksNote) || ! empty($foreksLicense))
    <section class="market-source-note foreks-note">
        @if (! empty($foreksNote))
            <p>{!! nl2br(e($foreksNote)) !!}</p>
        @endif
        @if (! empty($foreksLicense))
            <p>{!! nl2br(e($foreksLicense)) !!}</p>
        @endif
    </section>
@endif