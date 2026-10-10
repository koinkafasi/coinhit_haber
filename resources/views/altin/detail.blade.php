@extends('front.layouts.app')

@section('title', $instrument->name.' | Altın | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', $instrument->name.' canlı fiyatı, alış/satış, önceki kapanış, en yüksek/düşük, güniçi grafiği ve altın hesaplama.')

@section('content')
    @php
        $dirClass = $quote->direction() === 'up' ? 'text-green-700' : ($quote->direction() === 'down' ? 'text-red-700' : 'text-gray-400');
        $lastUpdated = $quote->updatedAt->format((string) config('market.timestamp_format', 'Y/m/d H:i:s'));
    @endphp

    <div class="borsa-page altin-page">
        <x-breadcrumb :items="$breadcrumb" />

        @include('altin.partials.quick-links')

        <div class="borsa-layout">
            <div class="borsa-main">
                <div class="quote-hero">
                    <h1 class="font-unna font-bold">{{ $instrument->name }}</h1>
                    <div class="quote-hero-price" data-market-code="{{ $quote->code() }}">
                        <span class="lastPrice font-bold {{ $dirClass }}" data-market-field="last">{{ $quote->formattedLast() }}</span>
                        <span class="percentChange {{ $dirClass }}" data-market-field="change_percent">{{ $quote->formattedChangePercent() }}</span>
                    </div>
                    <p class="market-updated text-xs">Güncelleme: <span data-market-updated>{{ $lastUpdated }}</span></p>
                </div>

                @include('doviz.partials.quote-stats', ['quote' => $quote])

                @include('borsa.partials.intraday-chart', ['quote' => $quote, 'chart' => $chart, 'ranges' => $ranges])

                @include('altin.partials.serbest', ['quotes' => $serbestQuotes])

                @include('borsa.partials.related-news', ['items' => $relatedNews])
            </div>

            <aside class="borsa-aside">
                <x-ad-zone :zone="1257" :index="1" />

                @include('altin.partials.calculator')

                <div class="aside-block">
                    <x-section-head title="Öne Çıkanlar" :href="null" />
                    <ul class="aside-list">
                        @foreach ($featured as $item)
                            <li><a href="{{ $item->url }}">{{ $item->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </aside>
        </div>

        @include('altin.partials.cme-note')
    </div>
@endsection