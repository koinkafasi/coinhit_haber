@extends('front.layouts.app')

@section('title', __('market.doviz').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('market.doviz_index_desc'))

@section('content')
    @php
        $currencyColumns = [
            ['label' => __('market.last'), 'field' => 'last', 'class' => 'lastPrice'],
            ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
        ];
        $currencyUrl = fn ($q) => $q->instrument->currencyUrl();
        $parityUrl = fn ($q) => $q->instrument->parityUrl();
    @endphp

    <div class="borsa-page doviz-page">
        <h1 class="page-title font-unna font-bold">{{ __('market.doviz') }}</h1>

        @include('doviz.partials.quick-links')

        <div class="borsa-layout">
            <div class="borsa-main">
                <x-market-table :title="__('market.doviz_rates')" :firstLabel="__('market.doviz_heading')" :quotes="$currencyQuotes"
                    :href="\App\Support\Locale::url('/doviz')" :columns="$currencyColumns" :urlResolver="$currencyUrl" id="doviz-kurlari" />

                <x-market-table :title="__('market.parities')" :firstLabel="__('market.parities_heading')" :quotes="$parityQuotes"
                    :href="\App\Support\Locale::url('/doviz/pariteler')" :columns="$currencyColumns" :urlResolver="$parityUrl" id="doviz-pariteler" />

                <x-market-table :title="__('market.emerging_rates')" :firstLabel="__('market.rate_heading')" :quotes="$emergingQuotes"
                    :href="\App\Support\Locale::url('/doviz')" :columns="$currencyColumns" id="doviz-gelisen" />

                @include('borsa.partials.related-news', ['items' => $relatedNews])
            </div>

            <aside class="borsa-aside">
                <x-ad-zone :zone="1257" :index="1" />

                @include('doviz.partials.mini-quote-cards')

                <div class="aside-block">
                    <x-section-head :title="__('market.kap_news')" :href="\App\Support\Locale::url('/borsa/hisseler/kap-haberleri')" />
                    @include('borsa.partials.kap-list', ['disclosures' => $kapDisclosures])
                </div>

                @include('doviz.partials.converter')
            </aside>
        </div>
    </div>
@endsection