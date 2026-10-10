@php
    $settings = $siteSettings ?? [];
    $social = config('navigation.social', []);

    $sameAs = [];
    foreach ($social as $item) {
        if (! empty($settings[$item['setting']])) {
            $sameAs[] = $settings[$item['setting']];
        } elseif (! empty($item['url']) && str_starts_with($item['url'], 'http')) {
            $sameAs[] = $item['url'];
        }
    }

    $org = array_filter([
        '@type' => 'Organization',
        'name' => $settings['site_name'] ?? 'Bloomberg HT',
        'url' => url('/'),
        'logo' => [
            '@type' => 'ImageObject',
            'url' => url($settings['logo_path'] ?? '/brand/logo.png'),
        ],
        'sameAs' => $sameAs,
    ]);

    $website = [
        '@type' => 'WebSite',
        'name' => $settings['site_name'] ?? 'Bloomberg HT',
        'url' => url('/'),
        'inLanguage' => $settings['language'] ?? 'tr',
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => url('/arama').'/{queryKeyword}',
            ],
            'query-input' => 'required name=queryKeyword',
        ],
    ];

    $graph = [
        '@context' => 'https://schema.org',
        '@graph' => [$org, $website],
    ];
@endphp

<script type="application/ld+json">{!! json_encode($graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>