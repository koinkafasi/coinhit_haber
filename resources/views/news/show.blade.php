@extends('front.layouts.app')

@php
    $ogImage = url($news->image_og ?: $news->image ?: '/img/og-default.png');
    $spotText = trim(strip_tags((string) $news->spot));
@endphp

@section('title', $news->title.' | '.$news->titleSuffix())
@section('description', \Illuminate\Support\Str::limit($spotText, 200))
@section('og_type', 'article')
@section('og_title', $news->title)
@section('og_description', \Illuminate\Support\Str::limit($spotText, 200))
@section('og_image', $ogImage)
@section('canonical', url($news->url))

@section('head')
    <meta name="author" content="Bloomberght">
    <meta name="twitter:creator" content="@bloomberght">
@endsection

@push('jsonld')
@php
    $published = $news->published_at?->toIso8601String();
    $modified = ($news->modified_at ?? $news->published_at)?->toIso8601String();
    $authorName = $news->author?->name ?? 'Bloomberg HT';
    $logo = url($siteSettings['logo_path'] ?? '/brand/logo.png');

    $newsArticle = array_filter([
        '@type' => 'NewsArticle',
        '@id' => url($news->url).'#article',
        'headline' => $news->title,
        'alternativeHeadline' => $news->meta_title,
        'description' => $spotText,
        'articleBody' => trim(strip_tags((string) $news->body)),
        'articleSection' => $news->category?->name,
        'datePublished' => $published,
        'dateModified' => $modified,
        'inLanguage' => 'tr-TR',
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => url($news->url)],
        'image' => [
            '@type' => 'ImageObject',
            'url' => $ogImage,
            'width' => 960,
            'height' => 540,
        ],
        'author' => [
            '@type' => 'Person',
            'name' => $authorName,
            'url' => url('/kunye'),
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => $siteSettings['site_name'] ?? 'Bloomberg HT',
            'logo' => ['@type' => 'ImageObject', 'url' => $logo],
        ],
    ]);

    $webPage = [
        '@type' => 'WebPage',
        '@id' => url($news->url),
        'url' => url($news->url),
        'name' => $news->title,
        'isPartOf' => ['@type' => 'WebSite', 'name' => $siteSettings['site_name'] ?? 'Bloomberg HT', 'url' => url('/')],
        'primaryImageOfPage' => ['@type' => 'ImageObject', 'url' => $ogImage],
        'datePublished' => $published,
        'dateModified' => $modified,
    ];

    $graph = [
        '@context' => 'https://schema.org',
        '@graph' => [$newsArticle, $webPage],
    ];
@endphp
<script type="application/ld+json">{!! json_encode($graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    {{-- Pageskin sol/sağ (spec §14.6 — makale detay 2126/2127) --}}
    <div class="pageskin pageskin-left" id="ciner-pageskin-left" data-device="DESKTOP">
        <div class="ads-zone lazy-init" data-adv="bht" data-device="DESKTOP" data-zone-id="zone_2126"></div>
    </div>
    <div class="pageskin pageskin-right" id="ciner-pageskin-right" data-device="DESKTOP">
        <div class="ads-zone lazy-init" data-adv="bht" data-device="DESKTOP" data-zone-id="zone_2127"></div>
    </div>

    <div class="article-layout">
        <div class="article-main">
            @include('news.partials.article', [
                'news' => $news,
                'categoryUrl' => $categoryUrl,
                'index' => 1,
                'isInfinite' => false,
                'eager' => true,
            ])

            {{-- Gövde sonrası reklam zonları (spec §14.6 makale detay) --}}
            <div class="article-ad-block">
                <x-ad-zone zone="1258" :index="1" />
                <x-ad-zone zone="1393" :index="2" />
            </div>

            @if ($related->isNotEmpty())
                <section class="article-related">
                    <h2 class="article-related-title font-unna font-bold">{{ __('ui.related') }}</h2>
                    <div class="related-slider" data-related-slider>
                        <div class="related-track" data-related-track>
                            @foreach ($related as $item)
                                <article class="related-slide">
                                    <a href="{{ $item->url }}">
                                        <figure class="related-figure">
                                            <x-lazy-image :src="asset($item->image ?: '/img/og-default.png')" alt=""
                                                          contract="news_card" class="related-img" />
                                        </figure>
                                        <h3 class="related-slide-title line-clamp-3">{{ $item->title }}</h3>
                                    </a>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            <div class="article-sentinel" data-infinite-sentinel aria-hidden="true"></div>
        </div>

        <aside class="article-sidebar">
            <section class="headlines-widget" data-widget-type="headlinesOfTheDay">
                <x-section-head title="Öne Çıkanlar" />
                @foreach ($featured as $i => $item)
                    <x-news-card :news="$item" :type="$i < 2 ? 'type1' : 'type2'" />
                @endforeach
            </section>
        </aside>
    </div>
@endsection