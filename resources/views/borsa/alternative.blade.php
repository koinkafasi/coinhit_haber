@extends('front.layouts.app')

@section('title', __('market.alternative_markets').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('market.borsa_alternative_desc'))

@section('content')
    <div class="borsa-page">
        <h1 class="page-title font-unna font-bold">{{ __('market.alternative_markets') }}</h1>

        @include('borsa.partials.quick-links')

        @php
            $columns = [
                ['label' => __('market.last'), 'field' => 'last', 'class' => 'lastPrice'],
                ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
            ];
        @endphp

        <div class="borsa-layout">
            <div class="borsa-main">
                <x-market-table :title="__('market.baltic')" :firstLabel="__('market.baltic_heading')" :quotes="$baltic" :columns="$columns"
                    :urlResolver="fn ($q) => $q->instrument->commodityUrl()" id="baltik-table" />

                <x-market-table :title="__('market.alternative')" :firstLabel="__('market.alternative_heading')" :quotes="$alternatives" :columns="$columns"
                    :urlResolver="fn ($q) => $q->instrument->indexUrl()" id="alternatif-table" />

                @include('borsa.partials.related-news', ['items' => $relatedNews])
            </div>

            <aside class="borsa-aside">
                <div class="aside-block">
                    <x-section-head :title="__('market.featured')" :href="null" />
                    <ul class="aside-list">
                        @foreach ($featured as $item)
                            <li><a href="{{ $item->url }}">{{ $item->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <x-ad-zone :zone="1257" :index="1" />
            </aside>
        </div>

        @include('altin.partials.cme-note')
    </div>
@endsection