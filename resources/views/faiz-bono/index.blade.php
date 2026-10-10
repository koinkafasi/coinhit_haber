@extends('front.layouts.app')

@section('title', ($pageTitle ?? __('market.faiz_bono')).' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('market.faiz_index_desc'))

@section('content')
    @php
        $pageTitle = $pageTitle ?? __('market.faiz_bono');
        $sonColumns = [
            ['label' => __('market.last'), 'field' => 'last', 'class' => 'lastPrice'],
            ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
        ];
        $bondUrl = fn ($q) => $q->instrument->bondUrl();
        $repoUrl = fn ($q) => $q->instrument->repoUrl();
        $eurobondUrl = fn ($q) => $q->instrument->eurobondUrl();
        $quickLinks = [
            ['label' => __('market.bond'), 'url' => '/faiz-bono#tahvil'],
            ['label' => __('market.repo'), 'url' => '/faiz-bono#repo'],
            ['label' => __('market.eurobonds'), 'url' => '/faiz-bono#eurobondlar'],
            ['label' => __('market.intl_bonds'), 'url' => '/faiz-bono#uluslararasi-tahviller'],
        ];
    @endphp

    <div class="borsa-page faiz-bono-page">
        <h1 class="page-title font-unna font-bold">{{ $pageTitle }}</h1>

        <nav class="borsa-quick-links faiz-bono-quick-links" aria-label="{{ __('market.faiz_quick_links') }}">
            @foreach ($quickLinks as $link)
                <a href="{{ \App\Support\Locale::url($link['url']) }}">{{ $link['label'] }}</a>
            @endforeach
        </nav>

        <div class="borsa-layout">
            <div class="borsa-main">
                <x-market-table :title="__('market.bond')" :firstLabel="__('market.bond_heading')" :quotes="$bondTr"
                    :href="\App\Support\Locale::url('/faiz-bono')" :columns="$sonColumns" :urlResolver="$bondUrl" id="tahvil" />

                <x-market-table :title="__('market.repo')" :firstLabel="__('market.repo_heading')" :quotes="$repo"
                    :href="\App\Support\Locale::url('/faiz-bono')" :columns="$sonColumns" :urlResolver="$repoUrl" id="repo" />

                <x-market-table :title="__('market.eurobonds')" :firstLabel="__('market.eurobonds_heading')" :quotes="$eurobonds"
                    :href="\App\Support\Locale::url('/faiz-bono')" :columns="$sonColumns" :urlResolver="$eurobondUrl" id="eurobondlar" />

                <x-market-table :title="__('market.intl_bonds')" :firstLabel="__('market.interest')" :quotes="$intlBonds"
                    :href="\App\Support\Locale::url('/faiz-bono')" :columns="$sonColumns" :urlResolver="$bondUrl" id="uluslararasi-tahviller" />

                @include('borsa.partials.related-news', ['items' => $relatedNews])
            </div>

            <aside class="borsa-aside">
                <x-ad-zone :zone="1257" :index="1" />
                <x-ad-zone :zone="1258" :index="2" />
            </aside>
        </div>

        @include('home.partials.market-widget')

        @include('faiz-bono.partials.note')
    </div>
@endsection