@extends('front.layouts.app')

@section('title', __('market.gold_prices').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('market.altin_index_desc'))

@section('content')
    @php
        $goldUrl = fn ($q) => $q->instrument->goldUrl();
        $commodityUrl = fn ($q) => $q->instrument->commodityUrl();
        $goldColumns = [
            ['label' => __('market.bid'), 'field' => 'bid', 'color' => true],
            ['label' => __('market.ask'), 'field' => 'ask', 'color' => true],
            ['label' => '%', 'field' => 'change_percent', 'color' => true, 'pct' => true],
        ];
        $commodityColumns = [
            ['label' => __('market.last'), 'field' => 'last'],
            ['label' => '%', 'field' => 'change_percent', 'color' => true, 'pct' => true],
        ];
    @endphp

    <div class="borsa-page altin-page">
        <h1 class="font-unna font-bold text-2xl md:text-4xl mb-3">{{ __('market.gold') }}</h1>

        @include('altin.partials.quick-links')

        {{-- 2 kolon: ana (Altın Fiyatları + Serbest Piyasa + İlgili Haberler) + aside 400px (reklam + Piyasalar) --}}
        <div class="relative flex flex-wrap md:flex-nowrap gap-7">
            <div class="w-full md:w-[calc(100%-400px)] min-w-0">
                @include('altin.partials.economy-table', [
                    'title' => __('market.gold_prices'),
                    'firstLabel' => __('market.gold_prices_heading'),
                    'columns' => $goldColumns,
                    'quotes' => $featureQuotes,
                    'href' => \App\Support\Locale::url('/altin'),
                    'urlResolver' => $goldUrl,
                    'id' => 'altin-fiyatlari',
                ])

                @include('altin.partials.serbest', ['quotes' => $serbestQuotes])

                @include('altin.partials.related-news', ['items' => $relatedNews])
            </div>

            <aside class="w-full md:w-[400px] relative altin-aside">
                <x-ad-zone zone="22984" :index="1" :label="'REKLAM advertisement1'" />
                @include('partials.market-widget-compact')
            </aside>
        </div>

        {{-- Diğer Emtia (tam genişlik) --}}
        @include('altin.partials.economy-table', [
            'title' => __('market.other_commodities'),
            'firstLabel' => __('market.precious_metal_heading'),
            'columns' => $commodityColumns,
            'quotes' => $otherCommodity,
            'urlResolver' => $commodityUrl,
            'id' => 'diger-emtia',
        ])

        {{-- Altın Hesaplama (araç) --}}
        @include('altin.partials.calculator')
    </div>
@endsection
