@extends('front.layouts.app')

@section('title', 'Altın Fiyatları | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', 'Güncel ve anlık serbest piyasa altın fiyatları: gram altın, çeyrek altın, cumhuriyet altını, ons altın alış/satış ve altın hesaplama.')

@section('content')
    @php
        $goldColumns = [
            ['label' => 'ALIŞ', 'field' => 'bid', 'class' => 'lastPrice'],
            ['label' => 'SATIŞ', 'field' => 'ask', 'class' => 'lastPrice'],
            ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
        ];
        $sonColumns = [
            ['label' => 'SON', 'field' => 'last', 'class' => 'lastPrice'],
            ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
        ];
        $goldUrl = fn ($q) => $q->instrument->goldUrl();
        $commodityUrl = fn ($q) => $q->instrument->commodityUrl();
    @endphp

    <div class="borsa-page altin-page">
        <h1 class="page-title font-unna font-bold">Altın</h1>

        @include('altin.partials.quick-links')

        <div class="borsa-layout">
            <div class="borsa-main">
                <x-market-table title="Altın Fiyatları" firstLabel="ALTIN FİYATLARI" :quotes="$featureQuotes"
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

        <x-market-table title="Diğer Emtia" firstLabel="KIYMETLİ METAL" :quotes="$otherCommodity"
            href="/emtia" :columns="$sonColumns" :urlResolver="$commodityUrl" id="diger-emtia" />

        @include('altin.partials.calculator')

        @include('altin.partials.cme-note')
    </div>
@endsection