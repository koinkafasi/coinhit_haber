@extends('front.layouts.app')

@section('title', __('market.world_markets').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('market.borsa_world_desc'))

@section('content')
    <div class="borsa-page">
        <h1 class="page-title font-unna font-bold">{{ __('market.world_markets') }}</h1>

        @include('borsa.partials.quick-links')

        @php
            $columns = [
                ['label' => __('market.last'), 'field' => 'last', 'class' => 'lastPrice'],
                ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
            ];
            $indexUrl = fn ($q) => $q->instrument->indexUrl();
        @endphp

        <div class="world-markets">
            @foreach ($tables as $table)
                <x-market-table :title="$table['title']" :firstLabel="$table['title']" :quotes="$table['quotes']"
                    :columns="$columns" :urlResolver="$indexUrl" :id="'world-'.$table['key']" />
            @endforeach
        </div>

        @include('altin.partials.cme-note', ['cmeNote' => $cmeNote])
    </div>
@endsection