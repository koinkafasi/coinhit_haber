@extends('front.layouts.app')

@section('title', 'Borsa | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', 'BIST endeksleri, en çok artan ve azalan hisseler, işlem hacmi sıralamaları ve borsa haberleri.')

@section('content')
    <div class="borsa-page">
        <h1 class="page-title font-unna font-bold">Borsa</h1>

        @include('borsa.partials.quick-links')

        @php
            $bistColumns = [
                ['label' => 'SON', 'field' => 'last', 'class' => 'lastPrice'],
                ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
                ['label' => 'FARK', 'field' => 'change', 'class' => 'change', 'color' => true],
                ['label' => 'AYLIK%', 'field' => 'monthly_percent', 'color' => true],
                ['label' => 'YILLIK%', 'field' => 'yearly_percent', 'color' => true],
            ];
            $stockColumns = [
                ['label' => 'SON', 'field' => 'last', 'class' => 'lastPrice'],
                ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
            ];
            $activeColumns = [
                ['label' => 'SON', 'field' => 'last', 'class' => 'lastPrice'],
                ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
                ['label' => 'HACİM TL', 'field' => 'volume_tl', 'class' => 'volumeTl'],
            ];
            $stockUrl = fn ($q) => $q->instrument->stockUrl();
        @endphp

        <div class="borsa-layout">
            <div class="borsa-main">
                <x-market-table title="BIST" firstLabel="BIST" :quotes="$bistQuotes" href="/borsa/endeksler"
                    :columns="$bistColumns" id="borsa-bist-table" />

                <x-market-table title="En Çok Artanlar" firstLabel="HİSSE ADI" :quotes="$gainers"
                    href="/borsa/en-cok" :columns="$stockColumns" :urlResolver="$stockUrl"
                    firstField="code" id="borsa-gainers" />

                <x-market-table title="En Çok Azalanlar" firstLabel="HİSSE ADI" :quotes="$losers"
                    href="/borsa/en-cok" :columns="$stockColumns" :urlResolver="$stockUrl"
                    firstField="code" id="borsa-losers" />

                <x-market-table title="En Çok İşlem Görenler" firstLabel="HİSSE ADI" :quotes="$actives"
                    href="/borsa/en-cok" :columns="$activeColumns" :urlResolver="$stockUrl"
                    firstField="code" id="borsa-actives" />

                @include('borsa.partials.related-news', ['items' => $relatedNews])
            </div>

            <aside class="borsa-aside">
                <x-ad-zone :zone="1257" :index="1" />

                <div class="aside-block">
                    <x-section-head title="Öne Çıkan Haberler" :href="null" />
                    <ul class="aside-list">
                        @foreach ($featured as $item)
                            <li><a href="{{ $item->url }}">{{ $item->title }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div class="aside-block">
                    <x-section-head title="KAP Haberleri" href="/borsa/hisseler/kap-haberleri" />
                    @include('borsa.partials.kap-list', ['disclosures' => $kapDisclosures])
                </div>
            </aside>
        </div>
    </div>
@endsection