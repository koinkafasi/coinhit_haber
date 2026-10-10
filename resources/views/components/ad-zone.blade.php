@props([
    'zone',
    'label' => 'REKLAM',
    'index' => null,
    'variant' => 'inline',
])

@php
    $zoneId = 'zone_'.$zone;
    $uid = $zoneId.($index !== null ? '_'.$index : '');
    $adName = 'advertisement'.($index ?? '');
@endphp

{{-- Genel REKLAM alanı (Bölüm 2.5 / 14.6 / 17.3): masaüstü + mobil kopya. --}}
<div class="wrapper adv-news ad-zone-block ad-zone-{{ $variant }} {{ $adName }}" data-type="advertisement" data-adv="bht" data-zone="{{ $zoneId }}">
    <div class="ad-head">
        <h2 class="ad-label !text-xxs">{{ $label }}</h2>
    </div>
    <div class="ad-body ad-body-desktop" data-device="DESKTOP">
        <div class="ads-zone lazy-init"
             data-adv="bht"
             data-device="DESKTOP"
             data-zone-id="{{ $zoneId }}"
             id="{{ $uid }}"></div>
    </div>
    <div class="ad-body ad-body-mobile" data-device="MOBILE">
        <div class="ads-zone lazy-init"
             data-adv="bht"
             data-device="MOBILE"
             data-zone-id="{{ $zoneId }}"
             id="{{ $uid }}_m"
             aria-hidden="true"></div>
    </div>
</div>