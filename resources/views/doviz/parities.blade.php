@extends('front.layouts.app')

@section('title', __('market.parities').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('market.parities_desc'))

@section('content')
    @php
        $columns = [
            ['label' => __('market.last'), 'field' => 'last', 'class' => 'lastPrice'],
            ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
        ];
        $parityUrl = fn ($q) => $q->instrument->parityUrl();
    @endphp

    <div class="borsa-page doviz-page">
        <h1 class="page-title font-unna font-bold">{{ __('market.parities') }}</h1>

        @include('doviz.partials.quick-links')

        <div class="borsa-layout">
            <div class="borsa-main">
                <x-market-table :title="__('market.parities')" :firstLabel="__('market.parities_heading')" :quotes="$parityQuotes"
                    :href="\App\Support\Locale::url('/doviz/pariteler')" :columns="$columns" :urlResolver="$parityUrl" id="doviz-pariteler" />

                <x-market-table :title="__('market.emerging_rates')" :firstLabel="__('market.rate_heading')" :quotes="$emergingQuotes"
                    :href="\App\Support\Locale::url('/doviz')" :columns="$columns" id="doviz-gelisen" />

                @include('borsa.partials.related-news', ['items' => $relatedNews])
            </div>

            <aside class="borsa-aside">
                <x-ad-zone :zone="1257" :index="1" />
                @include('doviz.partials.converter')
            </aside>
        </div>
    </div>
@endsection