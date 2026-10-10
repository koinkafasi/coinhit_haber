@extends('front.layouts.app')

@section('title', $instrument->name.' | Endeksler | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', $instrument->name.' canlı değeri, günlük değişimi ve güniçi grafiği.')

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
            ['label' => 'Endeksler', 'url' => '/borsa/endeksler'],
            ['label' => $instrument->name],
        ]" />

        <div class="borsa-layout">
            <div class="borsa-main">
                <div class="quote-hero">
                    <h1 class="font-unna font-bold">{{ $instrument->name }}</h1>
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
                <div class="aside-block">
                    <x-section-head title="BIST Endeksleri" href="/borsa/endeksler" />
                    <x-market-table title="ENDEKSLER" firstLabel="ENDEKSLER" :quotes="$indices" :columns="$shortColumns"
                        :urlResolver="fn ($q) => $q->instrument->indexUrl()" :head="false" />
                </div>
                <x-ad-zone :zone="1257" :index="1" />
            </aside>
        </div>
    </div>
@endsection