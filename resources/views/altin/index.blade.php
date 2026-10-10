@extends('front.layouts.app')

@section('title', __('market.gold_prices').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('market.altin_index_desc'))

@section('content')
    @php
        $goldColumns = [
            ['label' => __('market.bid'), 'field' => 'bid', 'class' => 'lastPrice'],
            ['label' => __('market.ask'), 'field' => 'ask', 'class' => 'lastPrice'],
            ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
        ];
        $sonColumns = [
            ['label' => __('market.last'), 'field' => 'last', 'class' => 'lastPrice'],
            ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
        ];
        $goldUrl = fn ($q) => $q->instrument->goldUrl();
        $commodityUrl = fn ($q) => $q->instrument->commodityUrl();
    @endphp

    <div class="borsa-page altin-page">
        <h1 class="page-title font-unna font-bold">{{ __('market.gold') }}</h1>

        @include('altin.partials.quick-links')

        <div class="borsa-layout">
            <div class="borsa-main">
                <x-market-table :title="__('market.gold_prices')" :firstLabel="__('market.gold_prices_heading')" :quotes="$featureQuotes"
                    :columns="$goldColumns" :urlResolver="$goldUrl" id="altin-fiyatlari" />

                @include('altin.partials.serbest', ['quotes' => $serbestQuotes])

                @include('borsa.partials.related-news', ['items' => $relatedNews])
            </div>

            <aside class="borsa-aside">
                <x-ad-zone :zone="1257" :index="1" />
                <x-ad-zone :zone="1258" :index="2" />
            </aside>
        </div>

        @include('home.partials.market-widget')

        <x-market-table :title="__('market.other_commodities')" :firstLabel="__('market.precious_metal_heading')" :quotes="$otherCommodity"
            :href="\App\Support\Locale::url('/emtia')" :columns="$sonColumns" :urlResolver="$commodityUrl" id="diger-emtia" />

        @include('altin.partials.calculator')

        @include('altin.partials.cme-note')
    </div>
@endsection