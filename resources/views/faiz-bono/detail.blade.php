@extends('front.layouts.app')

@section('title', $instrument->name.' | '.$groupTitle.' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', $instrument->name.' güncel faiz oranı, önceki kapanış ve güniçi grafiği.')

@section('content')
    @php
        $dirClass = $quote->direction() === 'up' ? 'text-green-700' : ($quote->direction() === 'down' ? 'text-red-700' : 'text-gray-400');
        $lastUpdated = $quote->updatedAt->format((string) config('market.timestamp_format', 'Y/m/d H:i:s'));
        $sonColumns = [
            ['label' => 'SON', 'field' => 'last', 'class' => 'lastPrice'],
            ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
        ];
    @endphp

    <div class="borsa-page faiz-bono-page">
        <x-breadcrumb :items="$breadcrumb" />

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

                <x-market-table :title="$groupTitle" :firstLabel="$groupTitle" :quotes="$groupQuotes"
                    href="/faiz-bono" :columns="$sonColumns" :urlResolver="$urlResolver" />

                @include('borsa.partials.related-news', ['items' => $relatedNews])
            </div>

            <aside class="borsa-aside">
                <x-ad-zone :zone="1257" :index="1" />

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

        @include('faiz-bono.partials.note')
    </div>
@endsection