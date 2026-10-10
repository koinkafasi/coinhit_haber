@extends('front.layouts.app')

@section('title', 'Pariteler | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', 'EUR/USD, USD/JPY, GBP/USD ve tüm pariteler ile gelişen piyasa kurları canlı takip.')

@section('content')
    @php
        $columns = [
            ['label' => 'SON', 'field' => 'last', 'class' => 'lastPrice'],
            ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
        ];
        $parityUrl = fn ($q) => $q->instrument->parityUrl();
    @endphp

    <div class="borsa-page doviz-page">
        <h1 class="page-title font-unna font-bold">Pariteler</h1>

        @include('doviz.partials.quick-links')

        <div class="borsa-layout">
            <div class="borsa-main">
                <x-market-table title="Pariteler" firstLabel="PARİTELER" :quotes="$parityQuotes"
                    href="/doviz/pariteler" :columns="$columns" :urlResolver="$parityUrl" id="doviz-pariteler" />

                <x-market-table title="Gelişen Piyasa Kurları" firstLabel="KUR" :quotes="$emergingQuotes"
                    href="/doviz" :columns="$columns" id="doviz-gelisen" />

                @include('borsa.partials.related-news', ['items' => $relatedNews])
            </div>

            <aside class="borsa-aside">
                <x-ad-zone :zone="1257" :index="1" />
                @include('doviz.partials.converter')
            </aside>
        </div>
    </div>
@endsection