@extends('front.layouts.app')

@section('title', $instrument->name.' ('.$instrument->code.') | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', $instrument->name.__('market.borsa_stock_detail_desc_suffix'))

@section('content')
    @php
        $dirClass = $quote->direction() === 'up' ? 'text-green-700' : ($quote->direction() === 'down' ? 'text-red-700' : 'text-gray-400');
        $shortColumns = [
            ['label' => __('market.last'), 'field' => 'last', 'class' => 'lastPrice'],
            ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
        ];
    @endphp

    <div class="borsa-page">
        <x-breadcrumb :items="[
            ['label' => __('market.borsa'), 'url' => \App\Support\Locale::url('/borsa')],
            ['label' => __('market.stocks'), 'url' => \App\Support\Locale::url('/borsa/hisseler')],
            ['label' => $instrument->code],
        ]" />

        <div class="borsa-layout">
            <div class="borsa-main">
                <div class="quote-hero">
                    <h1 class="font-unna font-bold">{{ $instrument->name }} <span class="quote-code">({{ $instrument->code }})</span></h1>
                    <div class="quote-hero-price" data-market-code="{{ $quote->code() }}">
                        <span class="lastPrice font-bold {{ $dirClass }}" data-market-field="last">{{ $quote->formattedLast() }}</span>
                        <span class="percentChange {{ $dirClass }}" data-market-field="change_percent">{{ $quote->formattedChangePercent() }}</span>
                    </div>
                </div>

                @include('borsa.partials.quote-stats', ['quote' => $quote])

                @include('borsa.partials.intraday-chart', ['quote' => $quote, 'chart' => $chart, 'ranges' => $ranges])

                @include('borsa.partials.related-news', ['items' => $relatedNews])
            </div>

            <aside class="borsa-aside">
                @include('borsa.partials.endeks-select', [
                    'endeksAction' => '/borsa/hisseler',
                    'indexOptions' => $indexOptions,
                    'selectedIndex' => '',
                ])

                @include('borsa.partials.az-tabs', ['azBase' => '/borsa/hisseler', 'letters' => $letters, 'letter' => null])

                <div class="aside-block">
                    <x-section-head :title="__('market.indices')" :href="\App\Support\Locale::url('/borsa/endeksler')" />
                    <x-market-table :title="__('market.indices_heading')" :firstLabel="__('market.indices_heading')" :quotes="$indicesPreview" :columns="$shortColumns"
                        :urlResolver="fn ($q) => $q->instrument->indexUrl()" :head="false" />
                </div>

                @if ($kapDisclosures->isNotEmpty())
                    <div class="aside-block">
                        <x-section-head :title="__('market.kap_news')" :href="\App\Support\Locale::url('/borsa/hisseler/kap-haberleri')" />
                        @include('borsa.partials.kap-list', ['disclosures' => $kapDisclosures])
                    </div>
                @endif

                <x-ad-zone :zone="1257" :index="1" />
            </aside>
        </div>
    </div>
@endsection