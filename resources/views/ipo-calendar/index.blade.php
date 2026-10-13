@extends('front.layouts.app')

@section('title', __('ui.ipo_title').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('ui.ipo_intro'))
@section('canonical', url(\App\Support\Locale::url('/halka-arz-takvimi')))

@section('content')
    <div class="ipo-page">

        {{-- 1. Arka plan (kaynakta başlık çubuğu YOK; sadece data-type="background") --}}
        <div class="relative">
            <div data-type="background" class="absolute -z-10 left-1/2 top-0 w-[100vw] h-96 -translate-x-1/2 -mt-4 overflow-hidden" aria-hidden="true">
                <picture>
                    <source media="(max-width: 767px)" srcset="{{ asset('/img/bg/haberler-mobile.png') }}">
                    <img src="{{ asset('/img/bg/haberler-desktop.png') }}" alt="" role="presentation"
                         loading="eager" fetchpriority="high" decoding="async"
                         class="absolute inset-0 w-full h-full object-cover object-[center_top] max-md:object-contain max-md:object-[center_top]">
                </picture>
            </div>
        </div>

        {{-- 2. Kaynak düzeni: article (calc(100%-400px)) + 400px aside (Öne Çıkanlar) --}}
        <div class="relative flex flex-wrap md:flex-nowrap w-full">
            <article class="w-full md:w-[calc(100%-400px)] md:pr-7">
                <h1 class="text-4xl font-unna font-bold mb-4">{{ __('ui.ipo_heading') }}</h1>

                {{-- Referans tek bir <p class="text-base mb-4"> paragrafı kullanıyor --}}
                <p class="text-base mb-4">{{ __('ui.ipo_intro') }}</p>

                {{-- 2a. Talep toplama tarihleri tablosu — referans birebir.
                     th: p-4 text-left ml-4 | td: py-4 + div.text-left.ml-4.text-ellipses.line-clamp-1
                     Kaynakta "Halka Arz Değeri" değeri "40,000,000 Lot" biçiminde,
                     fiyat "30,24 TL" biçimindedir. --}}
                <div data-widget-type="table" class="mb-8">
                    <table data-type="table-type1" class="w-full text-center text-sm bg-white ">
                        <thead>
                            <tr class="border-b text-sm">
                                <th class="p-4 text-left ml-4">{{ __('ui.ipo_col_request') }}</th>
                                <th class="p-4 text-left ml-4">{{ __('ui.ipo_col_company') }}</th>
                                <th class="p-4 text-left ml-4">{{ __('ui.ipo_col_value') }}</th>
                                <th class="p-4 text-left ml-4">{{ __('ui.ipo_col_price') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $item)
                                <tr class="border-b text-sm">
                                    <td class="py-4"><div class="text-left ml-4 text-ellipses line-clamp-1">{{ $item->request_range }}</div></td>
                                    <td class="py-4"><div class="text-left ml-4 text-ellipses line-clamp-1">{{ $item->ticker ?: $item->company_name }}</div></td>
                                    <td class="py-4"><div class="text-left ml-4 text-ellipses line-clamp-1">{{ $item->lot_value ? number_format((float) $item->lot_value, 0, ',', ',').' Lot' : '—' }}</div></td>
                                    <td class="py-4"><div class="text-left ml-4 text-ellipses line-clamp-1">{{ $item->price ? number_format((float) $item->price, 2, ',', '.').' TL' : '—' }}</div></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-4 text-left ml-4 text-gray-400">{{ __('ui.no_data') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- 2b. İlgili Haberler — referans: related-news-type1 + head-type1,
                         2 kolon x (type3 + type2) = 4 kart --}}
                @if ($related->isNotEmpty())
                    <div data-widget-type="related-news-type1">
                        <div data-type="head-type1">
                            <div class="relative before:absolute before:top-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black before:bottom-0 before:h-2.5 border-b border-black mb-4"></div>
                            <div class="mb-3"><h2 class="font-unna font-bold text-3xl">{{ __('ui.related') }}</h2></div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 md:gap-4">
                            @php
                                // Referans sırası: type3, type2, type3, type2 (2 kolon x 2 kart)
                                $relatedTypes = ['type3', 'type2', 'type3', 'type2'];
                                $relatedAll = $related->values()->all();
                            @endphp
                            @foreach ([0, 1] as $col)
                                <div class="flex flex-col">
                                    @foreach ([0, 1] as $row)
                                        @php
                                            $__i = $col * 2 + $row;
                                        @endphp
                                        @if (isset($relatedAll[$__i]))
                                            <x-news-card :news="$relatedAll[$__i]" :type="$relatedTypes[$__i]"
                                                         :showSpot="false" contract="news_card_square" />
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </article>

            {{-- 2c. Aside: Öne Çıkanlar (highlights) — referans kart sırası: type1, type2, type1, type4 --}}
            <aside class="relative w-full md:w-[400px] md:pr-4">
                @if ($featured->isNotEmpty())
                    <div data-widget-type="highlights">
                        <div data-type="head-type1">
                            <div class="relative before:absolute before:top-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black before:bottom-0 before:h-2.5 border-b border-black mb-4"></div>
                            <div class="mb-3"><h2 class="font-unna font-bold text-3xl">{{ __('ui.featured') }}</h2></div>
                        </div>
                        <div class="mb-4">
                            @php
                                // Referans sırası: type1, type2, type1, type4
                                $featuredTypes = ['type1', 'type2', 'type1', 'type4'];
                            @endphp
                            @foreach ($featured->values() as $index => $item)
                                @if (isset($featuredTypes[$index]))
                                    <x-news-card :news="$item" :type="$featuredTypes[$index]" :showSpot="false"
                                                 contract="news_card_square" />
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </div>
@endsection