@extends('front.layouts.app')

@section('title', 'Alternatif Piyasalar | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', 'Baltık kuru endeksleri, VIX ve Bloomberg Emtia Endeksi (BCOM) verileri.')

@section('content')
    <div class="borsa-page">
        <h1 class="page-title font-unna font-bold">Alternatif Piyasalar</h1>

        @include('borsa.partials.quick-links')

        @php
            $columns = [
                ['label' => 'SON', 'field' => 'last', 'class' => 'lastPrice'],
                ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
            ];
        @endphp

        <div class="borsa-layout">
            <div class="borsa-main">
                <x-market-table title="Baltık" firstLabel="BALTIK" :quotes="$baltic" :columns="$columns"
                    :urlResolver="fn ($q) => $q->instrument->commodityUrl()" id="baltik-table" />

                <x-market-table title="Alternatif" firstLabel="ALTERNATİF" :quotes="$alternatives" :columns="$columns"
                    :urlResolver="fn ($q) => $q->instrument->indexUrl()" id="alternatif-table" />

                @include('borsa.partials.related-news', ['items' => $relatedNews])
            </div>

            <aside class="borsa-aside">
                <div class="aside-block">
                    <x-section-head title="Öne Çıkanlar" :href="null" />
                    <ul class="aside-list">
                        @foreach ($featured as $item)
                            <li><a href="{{ $item->url }}">{{ $item->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <x-ad-zone :zone="1257" :index="1" />
            </aside>
        </div>
    </div>
@endsection