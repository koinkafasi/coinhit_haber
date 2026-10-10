@extends('front.layouts.app')

@section('title', __('market.commodity_prices').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('market.emtia_index_desc'))

@section('content')
    @php
        $sonColumns = [
            ['label' => __('market.last'), 'field' => 'last', 'class' => 'lastPrice'],
            ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
        ];
        $commodityUrl = fn ($q) => $q->instrument->commodityUrl();
    @endphp

    <div class="borsa-page emtia-page">
        <h1 class="page-title font-unna font-bold">{{ __('market.commodities') }}</h1>

        <nav class="borsa-quick-links emtia-quick-links" aria-label="{{ __('market.emtia_quick_links') }}">
            @foreach ($quickLinks as $link)
                <a href="{{ \App\Support\Locale::url($link['url']) }}">{{ $link['label'] }}</a>
            @endforeach
        </nav>

        <x-market-table :title="__('market.precious_metals')" :firstLabel="__('market.precious_metal_heading')" :quotes="$preciousMetals"
            :href="\App\Support\Locale::url('/emtia')" :columns="$sonColumns" :urlResolver="$commodityUrl" id="kiymetli-metaller" />

        <x-market-table :title="__('market.agricultural_commodities')" :firstLabel="__('market.agricultural_heading')" :quotes="$agriculture"
            :href="\App\Support\Locale::url('/emtia')" :columns="$sonColumns" :urlResolver="$commodityUrl" id="tarim-emtia" />

        <x-market-table :title="__('market.industrial_commodities')" :firstLabel="__('market.industrial_heading')" :quotes="$industry"
            :href="\App\Support\Locale::url('/emtia')" :columns="$sonColumns" :urlResolver="$commodityUrl" id="sanayi-emtia" />

        <x-market-table :title="__('market.industrial_3m')" :firstLabel="__('market.industrial_heading')" :quotes="$industryForward"
            :href="\App\Support\Locale::url('/emtia')" :columns="$sonColumns" :urlResolver="$commodityUrl" id="sanayi-emtia-3-aylik" />

        <x-market-table :title="__('market.energy_commodities')" :firstLabel="__('market.energy_heading')" :quotes="$energy"
            :href="\App\Support\Locale::url('/emtia')" :columns="$sonColumns" :urlResolver="$commodityUrl" id="enerji-emtia" />

        @include('borsa.partials.related-news', ['items' => $relatedNews])

        @include('home.partials.market-widget')

        @include('altin.partials.serbest', [
            'quotes' => $serbestQuotes,
            'href' => \App\Support\Locale::url('/altin'),
            'allLabel' => __('market.gold_list'),
        ])

        @include('altin.partials.cme-note')
    </div>
@endsection