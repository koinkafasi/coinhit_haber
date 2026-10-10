@extends('front.layouts.app')

@section('title', $instrument->name.' ('.$instrument->code.') | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', $instrument->name.' hisse senedi canlı fiyatı, alış/satış, yüksek/düşük, hacim ve güniçi grafiği.')

@section('content')
    @php
        $dirClass = $quote->direction() === 'up' ? 'text-green-700' : ($quote->direction() === 'down' ? 'text-red-700' : 'text-gray-400');
        $shortColumns = [
            ['label' => 'SON', 'field' => 'last', 'class' => 'lastPrice'],
            ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
        ];
    @endphp

    <div class="borsa-page">
        <x-breadcrumb :items="[
            ['label' => 'Borsa', 'url' => '/borsa'],
            ['label' => 'Hisse Senetleri', 'url' => '/borsa/hisseler'],
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
                    <x-section-head title="Endeksler" href="/borsa/endeksler" />
                    <x-market-table title="ENDEKSLER" firstLabel="ENDEKSLER" :quotes="$indicesPreview" :columns="$shortColumns"
                        :urlResolver="fn ($q) => $q->instrument->indexUrl()" :head="false" />
                </div>

                @if ($kapDisclosures->isNotEmpty())
                    <div class="aside-block">
                        <x-section-head title="KAP Haberleri" href="/borsa/hisseler/kap-haberleri" />
                        @include('borsa.partials.kap-list', ['disclosures' => $kapDisclosures])
                    </div>
                @endif

                <x-ad-zone :zone="1257" :index="1" />
            </aside>
        </div>
    </div>
@endsection