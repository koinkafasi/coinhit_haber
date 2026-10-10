@extends('front.layouts.app')

@section('title', 'Emtia Fiyatları | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', 'Kıymetli metaller, tarım emtia, sanayi emtia (3 aylık vadeli), enerji emtia ve serbest piyasa altın fiyatları.')

@section('content')
    @php
        $sonColumns = [
            ['label' => 'SON', 'field' => 'last', 'class' => 'lastPrice'],
            ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
        ];
        $commodityUrl = fn ($q) => $q->instrument->commodityUrl();
    @endphp

    <div class="borsa-page emtia-page">
        <h1 class="page-title font-unna font-bold">Emtia</h1>

        <nav class="borsa-quick-links emtia-quick-links" aria-label="Emtia hızlı bağlantılar">
            @foreach ($quickLinks as $link)
                <a href="{{ \App\Support\Locale::url($link['url']) }}">{{ $link['label'] }}</a>
            @endforeach
        </nav>

        <x-market-table title="Kıymetli Metaller" firstLabel="KIYMETLİ METAL" :quotes="$preciousMetals"
            href="/emtia" :columns="$sonColumns" :urlResolver="$commodityUrl" id="kiymetli-metaller" />

        <x-market-table title="Tarım Emtia" firstLabel="TARIM EMTİA" :quotes="$agriculture"
            href="/emtia" :columns="$sonColumns" :urlResolver="$commodityUrl" id="tarim-emtia" />

        <x-market-table title="Sanayi Emtia" firstLabel="SANAYİ EMTİA" :quotes="$industry"
            href="/emtia" :columns="$sonColumns" :urlResolver="$commodityUrl" id="sanayi-emtia" />

        <x-market-table title="Sanayi Emtia 3 Aylık" firstLabel="SANAYİ EMTİA" :quotes="$industryForward"
            href="/emtia" :columns="$sonColumns" :urlResolver="$commodityUrl" id="sanayi-emtia-3-aylik" />

        <x-market-table title="Enerji Emtia" firstLabel="ENERJİ EMTİA" :quotes="$energy"
            href="/emtia" :columns="$sonColumns" :urlResolver="$commodityUrl" id="enerji-emtia" />

        @include('borsa.partials.related-news', ['items' => $relatedNews])

        @include('home.partials.market-widget')

        @include('altin.partials.serbest', [
            'quotes' => $serbestQuotes,
            'href' => '/altin',
            'allLabel' => 'Altın Listesi',
        ])

        @include('altin.partials.cme-note')
    </div>
@endsection