@extends('front.layouts.app')

@section('title', __('market.crypto_title').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('market.kripto_index_desc'))

@section('content')
    <div class="borsa-page kripto-page">
        <h1 class="page-title font-unna font-bold">{{ __('market.crypto') }}</h1>

        {{-- Hero haber kartları (12) --}}
        @if ($hero->isNotEmpty())
            <section class="kripto-hero-news">
                <x-section-head :title="__('market.crypto_news')" :href="\App\Support\Locale::url('/haberler')" />
                <div class="news-card-grid cols-3">
                    @foreach ($hero as $item)
                        <x-news-card :news="$item" type="type2" :showSpot="false" />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- A-Z filtre çubuğu --}}
        @include('borsa.partials.az-tabs', [
            'azBase' => '/kripto/liste',
            'letter' => $letter,
            'letters' => $letters,
        ])

        <div class="borsa-layout">
            <div class="borsa-main">
                @include('kripto.partials.crypto-table', ['quotes' => $quotes])

                {{-- Kripto fiyat swiper şeridi --}}
                <section class="crypto-swiper finance-strip" data-type="finance-strip" aria-label="{{ __('market.crypto_prices_aria') }}">
                    <div class="finance-strip-track">
                        @foreach ($allQuotes as $sweep)
                            <a class="finance-strip-item" href="{{ $sweep->instrument->cryptoUrl() ?? \App\Support\Locale::url('/kripto') }}" data-market-code="{{ $sweep->code() }}">
                                <span class="fs-name text-xs">{{ $sweep->instrument->name }}</span>
                                <span class="lastPrice font-bold" data-market-field="last">{{ $sweep->formattedUsdPrice() }}</span>
                            </a>
                        @endforeach
                    </div>
                </section>

                @include('borsa.partials.related-news', ['items' => $relatedNews])
            </div>

            <aside class="borsa-aside">
                <x-ad-zone :zone="1257" :index="1" />

                @include('kripto.partials.quick-quotes')
            </aside>
        </div>

        @include('kripto.partials.note')
    </div>
@endsection