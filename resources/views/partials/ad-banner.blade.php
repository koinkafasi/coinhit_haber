@php
    /** @var string|int|null $zone  (zone id) */
    /** @var string $variant  inline | masthead */
    $zone = (string) ($zone ?? '');
    $variant = (string) ($variant ?? 'inline');
    $index = $index ?? null;
    $label = $label ?? null;
    $ad = \App\Models\Advertisement::forZone($zone);
@endphp
@if ($ad)
    @php
        $adLabel = $label ?? __('ui.ad');
        $zoneId = 'zone_'.$zone;
        $adName = 'advertisement'.($index ?? '');
    @endphp
    @if ($variant === 'masthead')
        <div class="masthead-ad">
            <div class="ad-head"><span class="ad-label !text-xxs">{{ $adLabel }}</span></div>
            <div class="ad-body ad-body-desktop" data-device="DESKTOP" data-zone-id="{{ $zoneId }}">
                @include('partials.ad-asset', ['ad' => $ad])
            </div>
            <div class="ad-body ad-body-mobile" data-device="MOBILE" data-zone-id="{{ $zoneId }}">
                @include('partials.ad-asset', ['ad' => $ad])
            </div>
        </div>
    @else
        <div class="wrapper adv-news ad-zone-block ad-zone-{{ $variant }} {{ $adName }}"
             data-type="advertisement"
             data-widget-type="{{ ($index == 1 ? 'advertisement18' : 'advertisement2') }}"
             data-adv="bht"
             data-zone="{{ $zoneId }}">
            <div class="ad-head">
                <h2 class="ad-label !text-xxs">{{ $adLabel }}</h2>
            </div>
            <div class="ad-body ad-body-desktop" data-device="DESKTOP" data-zone-id="{{ $zoneId }}">
                @include('partials.ad-asset', ['ad' => $ad])
            </div>
            <div class="ad-body ad-body-mobile" data-device="MOBILE" data-zone-id="{{ $zoneId }}">
                @include('partials.ad-asset', ['ad' => $ad])
            </div>
        </div>
    @endif
@endif
