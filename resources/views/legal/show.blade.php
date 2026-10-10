@extends('front.layouts.app')

@section('title', $pageTitle)
@section('description', $pageDescription)
@section('canonical', url('/'.$slug))
@section('og_type', 'website')
@section('og_image', url('/img/manifest-192.png'))
@section('og_image_width', '180')
@section('og_image_height', '180')

@section('masthead')
    <x-ad-zone :zone="1264" index="1" variant="masthead" />
@endsection

@push('jsonld')
@php
    $legalHtml = trim(strip_tags(implode(' ', array_column($page['sections'] ?? [], 'body'))));
    $webPage = [
        '@type' => 'WebPage',
        '@id' => url('/'.$slug),
        'url' => url('/'.$slug),
        'name' => $page['title'],
        'description' => $pageDescription,
        'inLanguage' => 'tr-TR',
        'isPartOf' => [
            '@type' => 'WebSite',
            'name' => $siteSettings['site_name'] ?? 'Bloomberg HT',
            'url' => url('/'),
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => config('legal.company.name', 'C Görsel Yayınlar A.Ş.'),
            'url' => url('/'),
        ],
    ];
@endphp
<script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@graph' => [$webPage]], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    <div class="pageskin pageskin-left" id="ciner-pageskin-left" data-device="DESKTOP">
        <div class="ads-zone lazy-init" data-adv="bht" data-device="DESKTOP" data-zone-id="zone_1253"></div>
    </div>
    <div class="pageskin pageskin-right" id="ciner-pageskin-right" data-device="DESKTOP">
        <div class="ads-zone lazy-init" data-adv="bht" data-device="DESKTOP" data-zone-id="zone_1254"></div>
    </div>

    <div class="static-layout">
        <x-breadcrumb :items="[['label' => $page['title'], 'url' => '/'.$slug]]" />

        <article class="static-page legal-page">
            <h1 class="static-title font-unna font-bold">{{ $page['title'] }}</h1>

            @foreach ($page['sections'] ?? [] as $section)
                <section class="static-section">
                    @if (! empty($section['heading']))
                        <h2>{{ $section['heading'] }}</h2>
                    @endif
                    <p>{!! nl2br(e($section['body'] ?? '')) !!}</p>
                </section>
            @endforeach

            @if (($page['type'] ?? null) === 'contact')
                @include('legal.partials.contact-form', ['subjects' => $contactSubjects])
            @endif
        </article>
    </div>
@endsection