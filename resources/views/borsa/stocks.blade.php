@extends('front.layouts.app')

@section('title', __('market.stocks').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('market.borsa_stocks_desc'))

@section('content')
    <div class="borsa-page">
        <h1 class="page-title font-unna font-bold">{{ __('market.stocks') }}</h1>

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
                ['label' => __('market.last'), 'field' => 'last', 'class' => 'lastPrice'],
                ['label' => __('market.previous'), 'field' => 'previous_close', 'class' => 'previousClose'],
                ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
                ['label' => __('market.high'), 'field' => 'high', 'class' => 'high'],
                ['label' => __('market.low'), 'field' => 'low', 'class' => 'low'],
                ['label' => __('market.volume_lot'), 'field' => 'volume_lot', 'class' => 'volumeLot'],
                ['label' => __('market.volume_tl'), 'field' => 'volume_tl', 'class' => 'volumeTl'],
            ];
        @endphp

        <div class="borsa-layout">
            <div class="borsa-main">
                <x-market-table :title="__('market.stock_heading')" :firstLabel="__('market.stock_heading')" :quotes="$paginator" :columns="$columns"
                    :urlResolver="fn ($q) => $q->instrument->stockUrl()" firstField="code"
                    :head="false" id="hisse-table" />

                @include('borsa.partials.pagination', ['paginator' => $paginator])
            </div>

            <aside class="borsa-aside">
                <div class="aside-block">
                    <x-section-head :title="__('market.kap_news')" :href="\App\Support\Locale::url('/borsa/hisseler/kap-haberleri')" />
                    @include('borsa.partials.kap-list', ['disclosures' => $kapDisclosures])
                </div>

                <x-ad-zone :zone="1257" :index="1" />
            </aside>
        </div>

        @include('altin.partials.cme-note')
    </div>
@endsection