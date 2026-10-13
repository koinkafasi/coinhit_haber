@extends('front.layouts.app')

@section('title', __('content.channel_enerji').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', $description ?: __('content.channel_enerji_description'))
@section('canonical', url(\App\Support\Locale::url('/enerji')))

@section('content')
    <div class="enerji-page">

        {{-- 1. Arka plan görseli + head-type2 "Enerji" başlığı — referans birebir --}}
        <div class="relative">
            <div data-type="background" class="absolute -z-10 left-1/2 top-0 w-[100vw] h-96 -translate-x-1/2 -mt-4 overflow-hidden" aria-hidden="true">
                <picture>
                    <source media="(max-width: 767px)" srcset="{{ asset('/img/bg/enerji-mobile.png') }}">
                    <img src="{{ asset('/img/bg/enerji-desktop.png') }}" alt="" role="presentation"
                         loading="eager" fetchpriority="high" decoding="async"
                         class="absolute inset-0 w-full h-full object-cover object-[center_top] max-md:object-contain max-md:object-[center_top]">
                </picture>
            </div>
            <div data-type="head-type2">
                <div class="relative before:absolute before:top-0 before:bottom-0 before:m-auto before:w-[230px] before:bg-black before:h-2.5 before:h-[5px] before:!m-0"></div>
                <div class="py-4">
                    <h2 class="font-unna font-bold text-2xl md:text-4xl">{{ __('content.channel_enerji') }}</h2>
                </div>
            </div>
        </div>

        {{-- 2. Haber grid'leri — referans birebir (her grid 3 kolon x 4 kart).
             Kolon kart tipleri: 1. type5 type2 type1 type4
                                2. type1 type4 type5 type2
                                3. type5 type2 type1 type4
             Sayfa başına 24 haber = 2 grid (12'şer). --}}
        @php
            $columnTypes = [
                ['type5', 'type2', 'type1', 'type4'],
                ['type1', 'type4', 'type5', 'type2'],
                ['type5', 'type2', 'type1', 'type4'],
            ];
            // Collection::chunk() anahtarlari KORUDUGU icin ikinci grubun
            // anahtarlari 12..23 olurdu -> her grubu values() ile 0..11'e cekiyoruz.
            $batches = $news->getCollection()->values()->chunk(12)
                ->map(fn ($chunk) => $chunk->values());
        @endphp

        <div data-load-more-container>
            @if ($batches->isEmpty())
                <p class="search-empty">{{ __('ui.no_news') }}</p>
            @else
                @foreach ($batches as $batchIndex => $batch)
                    <div class="grid grid-cols-1 md:grid-cols-3 md:gap-4 mb-4">
                        @foreach ($columnTypes as $column => $types)
                            <div class="flex flex-col justify-between md:gap-4">
                                @foreach ($types as $row => $cardType)
                                    @php
                                        $item = $batch->get($column * 4 + $row);
                                    @endphp
                                    @if ($item)
                                        {{-- type5 figcaption'ında kategori satırı referansta yok;
                                             spot (özet) bu sayfada referansta görünmüyor --}}
                                        <x-news-card :news="$item" :type="$cardType"
                                                     :showSpot="false" :showCategory="$cardType !== 'type5'"
                                                     :eager="$batchIndex === 0 && $column === 0 && $row === 0"
                                                     :wrapClass="$cardType === 'type5' ? '' : 'border-b mb-2 pb-2'"
                                                     contract="news_card_square" />
                                    @endif
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @endif
        </div>

        @if ($news->hasMorePages())
            <div class="load-more-wrap">
                <a class="load-more" href="{{ $news->nextPageUrl() }}"
                   data-load-more data-next-url="{{ $news->nextPageUrl() }}"
                   data-container="[data-load-more-container]">{{ __('ui.list') }}</a>
            </div>
        @endif
    </div>
@endsection