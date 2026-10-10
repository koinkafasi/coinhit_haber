@props([
    'src' => null,
    'alt' => '',
    'contract' => null,
    'srcset' => null,
    'sizes' => null,
    'width' => null,
    'height' => null,
    'class' => '',
    'eager' => false,
])

@php
    $c = $contract ? \App\Support\Media::contract($contract) : [];

    $width = $width ?? ($c['width'] ?? null);
    $height = $height ?? ($c['height'] ?? null);

    if ($srcset === null && $src && ! empty($c['srcset'])) {
        $srcset = \App\Support\Media::srcset($src, $c['srcset']);
    }

    $gif = \App\Support\Media::transparentGif();
    $imgClass = trim('b-lazy '.$class);
@endphp

<img
    @if ($eager) src="{{ $src }}" @else src="{{ $gif }}" data-src="{{ $src }}" @endif
    @if ($srcset) data-srcset="{{ $srcset }}" @endif
    @if ($sizes) sizes="{{ $sizes }}" data-sizes="{{ $sizes }}" @endif
    @if ($width) width="{{ $width }}" @endif
    @if ($height) height="{{ $height }}" @endif
    alt="{{ $alt }}"
    loading="{{ $eager ? 'eager' : 'lazy' }}"
    decoding="async"
    class="{{ $imgClass }}"
>
@unless ($eager)
    <noscript>
        <img
            src="{{ $src }}"
            @if ($width) width="{{ $width }}" @endif
            @if ($height) height="{{ $height }}" @endif
            alt="{{ $alt }}"
            class="{{ $class }}"
        >
    </noscript>
@endunless