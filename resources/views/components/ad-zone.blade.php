@props([
    'zone',
    'label' => null,
    'index' => null,
    'variant' => 'inline',
])

@include('partials.ad-banner', [
    'zone' => $zone,
    'variant' => $variant,
    'index' => $index,
    'label' => $label,
])
