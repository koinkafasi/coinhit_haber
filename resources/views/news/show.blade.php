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
        'inLanguage' => app()->getLocale() === 'en' ? 'en-US' : 'tr-TR',
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
            'url' => url(\App\Support\Locale::url('/kunye')),
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
    {{-- Pageskin sol/sağ (spec §14.6 — makale detay 2126/2127) — yönetilebilir, yoksa gizli --}}
    @php($__adArtL = \App\Models\Advertisement::forZone('2126'))
    @if ($__adArtL)
        <div class="pageskin pageskin-left" id="ciner-pageskin-left" data-device="DESKTOP" data-zone-id="zone_2126">
            @include('partials.ad-asset', ['ad' => $__adArtL])
        </div>
    @endif
    @php($__adArtR = \App\Models\Advertisement::forZone('2127'))
    @if ($__adArtR)
        <div class="pageskin pageskin-right" id="ciner-pageskin-right" data-device="DESKTOP" data-zone-id="zone_2127">
            @include('partials.ad-asset', ['ad' => $__adArtR])
        </div>
    @endif

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



            <div class="article-sentinel" data-infinite-sentinel aria-hidden="true"></div>
        </div>

        <aside class="article-sidebar">
            <x-featured-widget variant="sidebar" :title="__('content.featured_title')" :items="$featured" class="headlines-widget" />
            @isset($mostReadTabs)
                {{-- Anasayfa mostRead bloğundan BAĞIMSIZ yazı sayfası varyantı:
                     referans most-read-slider DOM'u (2 kayıt, sekmesiz, görselsiz). --}}
                <x-most-read-article :items="$mostReadTabs['today'] ?? []" class="mt-8" />
            @endisset
        </aside>
    </div>
@endsection