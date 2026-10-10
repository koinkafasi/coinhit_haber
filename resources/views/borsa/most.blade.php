@extends('front.layouts.app')

@section('title', __('market.rising_falling_and').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('market.borsa_most_desc'))

@section('content')
    <div class="borsa-page">
        <h1 class="page-title font-unna font-bold">{{ __('market.rising_falling') }}</h1>

        @include('borsa.partials.quick-links')

        @php
            $stockUrl = fn ($q) => $q->instrument->stockUrl();
            $rankingColumns = [
                ['label' => __('market.last'), 'field' => 'last', 'class' => 'lastPrice'],
                ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
            ];
            $activeColumns = [
                ['label' => __('market.last'), 'field' => 'last', 'class' => 'lastPrice'],
                ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
                ['label' => __('market.volume_tl'), 'field' => 'volume_tl', 'class' => 'volumeTl'],
            ];
        @endphp

        <div class="borsa-ranking">
            <section>
                <x-section-head :title="__('market.gainers')" :href="null" />
                <x-market-table :title="__('market.stock_name')" :firstLabel="__('market.stock_name')" :quotes="$gainers" :columns="$rankingColumns"
                    :urlResolver="$stockUrl" firstField="code" :head="false" id="most-gainers" />
            </section>

            <section>
                <x-section-head :title="__('market.losers')" :href="null" />
                <x-market-table :title="__('market.stock_name')" :firstLabel="__('market.stock_name')" :quotes="$losers" :columns="$rankingColumns"
                    :urlResolver="$stockUrl" firstField="code" :head="false" id="most-losers" />
            </section>

            <section>
                <x-section-head :title="__('market.most_traded')" :href="null" />
                <x-market-table :title="__('market.stock_name')" :firstLabel="__('market.stock_name')" :quotes="$actives" :columns="$activeColumns"
                    :urlResolver="$stockUrl" firstField="code" :head="false" id="most-actives" />
            </section>
        </div>

        @include('altin.partials.cme-note')
    </div>
@endsection