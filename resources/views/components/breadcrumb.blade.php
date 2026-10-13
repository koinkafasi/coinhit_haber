@props(['items' => [], 'label' => 'Bloomberg HT'])

@php
    $list = array_merge([['label' => $label, 'url' => '/']], $items);

    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [],
    ];

    foreach ($list as $index => $crumb) {
        $entry = [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => $crumb['label'],
        ];

        if (! empty($crumb['url'])) {
            $entry['item'] = url($crumb['url']);
        }

        $jsonLd['itemListElement'][] = $entry;
    }
@endphp

<nav class="breadcrumb block text-xs text-gray-500" aria-label="Breadcrumb">
    @foreach ($list as $index => $crumb)
        @if ($index > 0)
            <i class="bloomberght-icon-font-chevron-right inline-block align-top mx-1 w-1 h-1 text-xxxs text-black" aria-hidden="true"></i>
        @endif
        @if (! empty($crumb['url']) && $index < count($list) - 1)
            <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
        @else
            <span class="font-bold" aria-current="page">{{ $crumb['label'] }}</span>
        @endif
    @endforeach
</nav>

@push('jsonld')
<script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush