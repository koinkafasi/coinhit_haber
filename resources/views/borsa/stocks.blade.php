@extends('front.layouts.app')

@section('title', 'Hisse Senetleri | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', 'BIST hisse senetleri: son fiyat, dün, yüksek, düşük, hacim lot ve hacim TL verileri.')

@section('content')
    <div class="borsa-page">
        <h1 class="page-title font-unna font-bold">Hisse Senetleri</h1>

        @include('borsa.partials.quick-links')

        @include('borsa.partials.endeks-select', [
            'endeksAction' => $letter ? '/borsa/hisseler/'.$letter : '/borsa/hisseler',
            'indexOptions' => $indexOptions,
            'selectedIndex' => $selectedIndex,
        ])

        @include('borsa.partials.az-tabs', [
            'azBase' => '/borsa/hisseler',
            'letters' => $letters,
            'letter' => $letter,
            'azQuery' => $selectedIndex ? '?endeks='.$selectedIndex : '',
        ])

        @php
            $columns = [
                ['label' => 'SON', 'field' => 'last', 'class' => 'lastPrice'],
                ['label' => 'DÜN', 'field' => 'previous_close', 'class' => 'previousClose'],
                ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
                ['label' => 'YÜKSEK', 'field' => 'high', 'class' => 'high'],
                ['label' => 'DÜŞÜK', 'field' => 'low', 'class' => 'low'],
                ['label' => 'HACİM LOT', 'field' => 'volume_lot', 'class' => 'volumeLot'],
                ['label' => 'HACİM TL', 'field' => 'volume_tl', 'class' => 'volumeTl'],
            ];
        @endphp

        <div class="borsa-layout">
            <div class="borsa-main">
                <x-market-table title="HİSSE" firstLabel="HİSSE" :quotes="$paginator" :columns="$columns"
                    :urlResolver="fn ($q) => $q->instrument->stockUrl()" firstField="code"
                    :head="false" id="hisse-table" />

                @include('borsa.partials.pagination', ['paginator' => $paginator])
            </div>

            <aside class="borsa-aside">
                <div class="aside-block">
                    <x-section-head title="KAP Haberleri" href="/borsa/hisseler/kap-haberleri" />
                    @include('borsa.partials.kap-list', ['disclosures' => $kapDisclosures])
                </div>

                <x-ad-zone :zone="1257" :index="1" />
            </aside>
        </div>

        @include('altin.partials.cme-note')
    </div>
@endsection