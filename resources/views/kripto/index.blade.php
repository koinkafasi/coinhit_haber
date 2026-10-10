@extends('front.layouts.app')

@section('title', 'Kripto Para | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', 'Bitcoin, Ethereum, XRP ve diğer kripto para fiyatları, 24 saatlik ve 7 günlük değişim, hacim ve piyasa değerleri.')

@section('content')
    <div class="borsa-page kripto-page">
        <h1 class="page-title font-unna font-bold">Kripto</h1>

        {{-- Hero haber kartları (12) --}}
        @if ($hero->isNotEmpty())
            <section class="kripto-hero-news">
                <x-section-head title="Kripto Haberleri" href="/haberler" />
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
                <section class="crypto-swiper finance-strip" data-type="finance-strip" aria-label="Kripto fiyatları">
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