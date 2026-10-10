@extends('front.layouts.app')

@section('title', ($pageTitle ?? __('market.doviz')).' | '.__('market.doviz').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', ($pageTitle ?? __('market.doviz')).__('market.doviz_tracking_desc_suffix'))

@section('content')
    @php
        $pageTitle = $pageTitle ?? __('market.doviz');
        $currencyColumns = [
            ['label' => __('market.last'), 'field' => 'last', 'class' => 'lastPrice'],
            ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
        ];
        $currencyUrl = fn ($q) => $q->instrument->currencyUrl();
    @endphp

    <div class="borsa-page doviz-page">
        <x-breadcrumb :items="$breadcrumb" />

        <div class="borsa-layout">
            <div class="borsa-main">
                @if (isset($quote))
                    @php
                        $dirClass = $quote->direction() === 'up' ? 'text-green-700' : ($quote->direction() === 'down' ? 'text-red-700' : 'text-gray-400');
                        $lastUpdated = $quote->updatedAt->format((string) config('market.timestamp_format', 'Y/m/d H:i:s'));
                    @endphp
                    <div class="quote-hero">
                        <h1 class="font-unna font-bold">{{ $pageTitle }}</h1>
                        <div class="quote-hero-price" data-market-code="{{ $quote->code() }}">
                            <span class="lastPrice font-bold {{ $dirClass }}" data-market-field="last">{{ $quote->formattedLast() }}</span>
                            <span class="percentChange {{ $dirClass }}" data-market-field="change_percent">{{ $quote->formattedChangePercent() }}</span>
                        </div>
                        <p class="market-updated text-xs">{{ __('ui.updated') }}: <span data-market-updated>{{ $lastUpdated }}</span></p>
                    </div>

                    @include('doviz.partials.quote-stats', ['quote' => $quote])

                    @include('borsa.partials.intraday-chart', ['quote' => $quote, 'chart' => $chart, 'ranges' => $ranges])
                @else
                    @php
                        $basketDirClass = $basket['direction'] === 'up' ? 'text-green-700' : ($basket['direction'] === 'down' ? 'text-red-700' : 'text-gray-400');
                    @endphp
                    <div class="quote-hero">
                        <h1 class="font-unna font-bold">{{ $pageTitle }}</h1>
                        <div class="quote-hero-price">
                            <span class="lastPrice font-bold {{ $basketDirClass }}">{{ $basket['last'] }}</span>
                            <span class="percentChange {{ $basketDirClass }}">{{ $basket['change_percent'] }}</span>
                        </div>
                    </div>

                    <dl class="quote-stats">
                        <div class="quote-stat"><dt>{{ __('market.basket') }}</dt><dd>%{{ $basket['weight'] }} USD + %{{ $basket['weight'] }} EUR</dd></div>
                        <div class="quote-stat"><dt>{{ __('market.change') }}</dt><dd class="{{ $basketDirClass }}">{{ $basket['change'] }}</dd></div>
                        <div class="quote-stat"><dt>{{ __('market.previous_close') }}</dt><dd>{{ $basket['previous_close'] }}</dd></div>
                    </dl>
                @endif

                <x-market-table :title="__('market.doviz_rates')" :firstLabel="__('market.doviz_heading')" :quotes="$currencyQuotes"
                    :href="\App\Support\Locale::url('/doviz')" :columns="$currencyColumns" :urlResolver="$currencyUrl" id="doviz-kurlari" />

                @include('borsa.partials.related-news', ['items' => $relatedNews])
            </div>

            <aside class="borsa-aside">
                <x-ad-zone :zone="1257" :index="1" />

                <div class="aside-block">
                    <x-section-head :title="__('market.featured')" :href="null" />
                    <ul class="aside-list">
                        @foreach ($featured as $item)
                            <li><a href="{{ $item->url }}">{{ $item->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </aside>
        </div>
    </div>
@endsection