@extends('front.layouts.app')

@section('title', __('market.borsa').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('market.borsa_index_desc'))

@section('content')
    <div class="borsa-page">
        <h1 class="page-title font-unna font-bold">{{ __('market.borsa') }}</h1>

        @include('borsa.partials.quick-links')

        @php
            $bistColumns = [
                ['label' => __('market.last'), 'field' => 'last', 'class' => 'lastPrice'],
                ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
                ['label' => __('market.change'), 'field' => 'change', 'class' => 'change', 'color' => true],
                ['label' => __('market.monthly_percent'), 'field' => 'monthly_percent', 'color' => true],
                ['label' => __('market.yearly_percent'), 'field' => 'yearly_percent', 'color' => true],
            ];
            $stockColumns = [
                ['label' => __('market.last'), 'field' => 'last', 'class' => 'lastPrice'],
                ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
            ];
            $activeColumns = [
                ['label' => __('market.last'), 'field' => 'last', 'class' => 'lastPrice'],
                ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
                ['label' => __('market.volume_tl'), 'field' => 'volume_tl', 'class' => 'volumeTl'],
            ];
            $stockUrl = fn ($q) => $q->instrument->stockUrl();
        @endphp

        <div class="borsa-layout">
            <div class="borsa-main">
                <x-market-table :title="__('market.bist')" :firstLabel="__('market.bist')" :quotes="$bistQuotes" :href="\App\Support\Locale::url('/borsa/endeksler')"
                    :columns="$bistColumns" id="borsa-bist-table" />

                <x-market-table :title="__('market.top_gainers')" :firstLabel="__('market.stock_name')" :quotes="$gainers"
                    :href="\App\Support\Locale::url('/borsa/en-cok')" :columns="$stockColumns" :urlResolver="$stockUrl"
                    firstField="code" id="borsa-gainers" />

                <x-market-table :title="__('market.top_losers')" :firstLabel="__('market.stock_name')" :quotes="$losers"
                    :href="\App\Support\Locale::url('/borsa/en-cok')" :columns="$stockColumns" :urlResolver="$stockUrl"
                    firstField="code" id="borsa-losers" />

                <x-market-table :title="__('market.most_traded')" :firstLabel="__('market.stock_name')" :quotes="$actives"
                    :href="\App\Support\Locale::url('/borsa/en-cok')" :columns="$activeColumns" :urlResolver="$stockUrl"
                    firstField="code" id="borsa-actives" />

                @include('borsa.partials.related-news', ['items' => $relatedNews])
            </div>

            <aside class="borsa-aside">
                <x-ad-zone :zone="1257" :index="1" />

                <div class="aside-block">
                    <x-section-head :title="__('market.featured_news')" :href="null" />
                    <ul class="aside-list">
                        @foreach ($featured as $item)
                            <li><a href="{{ $item->url }}">{{ $item->title }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div class="aside-block">
                    <x-section-head :title="__('market.kap_news')" :href="\App\Support\Locale::url('/borsa/hisseler/kap-haberleri')" />
                    @include('borsa.partials.kap-list', ['disclosures' => $kapDisclosures])
                </div>
            </aside>
        </div>

        @include('altin.partials.cme-note')
    </div>
@endsection