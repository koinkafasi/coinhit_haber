@extends('front.layouts.app')

@section('title', 'Döviz | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', 'Dolar, Euro, Sterlin ve diğer döviz kurları, pariteler, gelişen piyasa kurları ve döviz çevirici.')

@section('content')
    @php
        $currencyColumns = [
            ['label' => 'SON', 'field' => 'last', 'class' => 'lastPrice'],
            ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
        ];
        $currencyUrl = fn ($q) => $q->instrument->currencyUrl();
        $parityUrl = fn ($q) => $q->instrument->parityUrl();
    @endphp

    <div class="borsa-page doviz-page">
        <h1 class="page-title font-unna font-bold">Döviz</h1>

        @include('doviz.partials.quick-links')

        <div class="borsa-layout">
            <div class="borsa-main">
                <x-market-table title="Döviz Kurları" firstLabel="DÖVİZ" :quotes="$currencyQuotes"
                    href="/doviz" :columns="$currencyColumns" :urlResolver="$currencyUrl" id="doviz-kurlari" />

                <x-market-table title="Pariteler" firstLabel="PARİTELER" :quotes="$parityQuotes"
                    href="/doviz/pariteler" :columns="$currencyColumns" :urlResolver="$parityUrl" id="doviz-pariteler" />

                <x-market-table title="Gelişen Piyasa Kurları" firstLabel="KUR" :quotes="$emergingQuotes"
                    href="/doviz" :columns="$currencyColumns" id="doviz-gelisen" />

                @include('borsa.partials.related-news', ['items' => $relatedNews])
            </div>

            <aside class="borsa-aside">
                <x-ad-zone :zone="1257" :index="1" />

                @include('doviz.partials.mini-quote-cards')

                <div class="aside-block">
                    <x-section-head title="KAP Haberleri" href="/borsa/hisseler/kap-haberleri" />
                    @include('borsa.partials.kap-list', ['disclosures' => $kapDisclosures])
                </div>

                @include('doviz.partials.converter')
            </aside>
        </div>
    </div>
@endsection