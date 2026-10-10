@extends('front.layouts.app')

@section('title', 'Endeksler | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', 'BIST ve bölgesel endeksler: son değer, günlük, aylık ve yıllık değişim.')

@section('content')
    <div class="borsa-page">
        <h1 class="page-title font-unna font-bold">Endeksler</h1>

        @include('borsa.partials.quick-links')

        @php
            $columns = [
                ['label' => 'SON', 'field' => 'last', 'class' => 'lastPrice'],
                ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
                ['label' => 'AYLIK%', 'field' => 'monthly_percent', 'color' => true],
                ['label' => 'YILLIK%', 'field' => 'yearly_percent', 'color' => true],
            ];
        @endphp

        <x-market-table title="ENDEKSLER" firstLabel="ENDEKSLER" :quotes="$quotes" :columns="$columns"
            :urlResolver="fn ($q) => $q->instrument->indexUrl()" id="endeksler-table" />

        @include('altin.partials.cme-note')
    </div>
@endsection