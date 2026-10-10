@extends('front.layouts.app')

@section('title', __('market.indices').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('market.borsa_indices_desc'))

@section('content')
    <div class="borsa-page">
        <h1 class="page-title font-unna font-bold">{{ __('market.indices') }}</h1>

        @include('borsa.partials.quick-links')

        @php
            $columns = [
                ['label' => __('market.last'), 'field' => 'last', 'class' => 'lastPrice'],
                ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
                ['label' => __('market.monthly_percent'), 'field' => 'monthly_percent', 'color' => true],
                ['label' => __('market.yearly_percent'), 'field' => 'yearly_percent', 'color' => true],
            ];
        @endphp

        <x-market-table :title="__('market.indices_heading')" :firstLabel="__('market.indices_heading')" :quotes="$quotes" :columns="$columns"
            :urlResolver="fn ($q) => $q->instrument->indexUrl()" id="endeksler-table" />

        @include('altin.partials.cme-note')
    </div>
@endsection