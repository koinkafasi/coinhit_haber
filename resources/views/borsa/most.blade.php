@extends('front.layouts.app')

@section('title', 'Yükselen ve Düşen Hisseler | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', 'BIST yükselenler, düşenler ve en çok işlem gören hisse senedi sıralamaları.')

@section('content')
    <div class="borsa-page">
        <h1 class="page-title font-unna font-bold">Yükselen / Düşen Hisseler</h1>

        @include('borsa.partials.quick-links')

        @php
            $stockUrl = fn ($q) => $q->instrument->stockUrl();
            $rankingColumns = [
                ['label' => 'SON', 'field' => 'last', 'class' => 'lastPrice'],
                ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
            ];
            $activeColumns = [
                ['label' => 'SON', 'field' => 'last', 'class' => 'lastPrice'],
                ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
                ['label' => 'HACİM TL', 'field' => 'volume_tl', 'class' => 'volumeTl'],
            ];
        @endphp

        <div class="borsa-ranking">
            <section>
                <x-section-head title="Yükselenler" :href="null" />
                <x-market-table title="HİSSE ADI" firstLabel="HİSSE ADI" :quotes="$gainers" :columns="$rankingColumns"
                    :urlResolver="$stockUrl" firstField="code" :head="false" id="most-gainers" />
            </section>

            <section>
                <x-section-head title="Düşenler" :href="null" />
                <x-market-table title="HİSSE ADI" firstLabel="HİSSE ADI" :quotes="$losers" :columns="$rankingColumns"
                    :urlResolver="$stockUrl" firstField="code" :head="false" id="most-losers" />
            </section>

            <section>
                <x-section-head title="En Çok İşlem Görenler" :href="null" />
                <x-market-table title="HİSSE ADI" firstLabel="HİSSE ADI" :quotes="$actives" :columns="$activeColumns"
                    :urlResolver="$stockUrl" firstField="code" :head="false" id="most-actives" />
            </section>
        </div>

        @include('altin.partials.cme-note')
    </div>
@endsection