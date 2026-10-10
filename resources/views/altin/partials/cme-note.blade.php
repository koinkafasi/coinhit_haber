{{-- CME / Türkiye veri lisansı notu (spec §5, §11.6, §15.5) --}}
@php
    $marketData = app(\App\Services\Market\MarketDataService::class);
    $cmeNote = $cmeNote ?? $marketData->sourceNote('cme');
    $cmeLicense = $marketData->licenseNote('cme');
@endphp

@if (! empty($cmeNote) || ! empty($cmeLicense))
    <section class="market-source-note cme-note">
        @if (! empty($cmeNote))
            <p>{!! nl2br(e($cmeNote)) !!}</p>
        @endif
        @if (! empty($cmeLicense))
            <p>{!! nl2br(e($cmeLicense)) !!}</p>
        @endif
    </section>
@endif