@extends('front.layouts.app')

@section('title', __('media.fund_title').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('media.fund_index_desc'))
@section('canonical', url(\App\Support\Locale::url('/yatirim-fonlari')))

@section('content')
    <div class="fund-page">

        <h1 class="font-unna font-bold text-2xl md:text-4xl mb-3">{{ __('media.fund_title') }}</h1>

        {{-- Üst 2 kolon: ana (fon haber hero slider) + aside (alt-tool nav + advertisement1 reklam çifti) — referans birebir --}}
        <div class="relative w-full flex flex-wrap md:flex-nowrap gap-8 mb-4">
            <div class="w-full md:w-[calc(100%-400px)] min-w-0">
                @if ($fundNews->isNotEmpty())
                    <div data-widget-type="main-slider">
                        <div data-type="swiper" data-name="" class="swiper slider-custom relative"
                             data-swiper='{"hashMode":true,"lazy":{"loadPrevNext":true,"loadPrevNextAmount":1,"loadOnTransitionStart":true},"loop":true,"pagination":{"el":".swiper-pagination","clickable":true},"navigation":{"nextEl":".main-slider .bloomberght-icon-font-chevron-right","prevEl":".main-slider .bloomberght-icon-font-chevron-left"}}'>
                            <div class="swiper-wrapper">
                                @foreach ($fundNews as $i => $item)
                                    @php
                                        $__heroImg = asset($item->image ?: '/img/og-default.png');
                                    @endphp
                                    <div class="swiper-slide " data-hash="{{ md5((string) $item->news_id) }}" style="">
                                        <div class="-z-100">
                                            <figure>
                                                <div class="w-full h-auto aspect-video bg-gray-50">
                                                    <a href="{{ $item->url }}" title="{{ $item->title }}">
                                                        @if ($i === 0)
                                                            <img
                                                                srcset="{{ \App\Support\Media::srcset($__heroImg, [320, 400, 640, 1280]) }}"
                                                                sizes="(max-width: 640px) 100vw, (max-width: 1024px) 640px, 960px"
                                                                src="{{ $__heroImg }}"
                                                                alt="{{ $item->title }}"
                                                                class="w-full h-full "
                                                                loading="eager"
                                                                fetchpriority="high"
                                                            >
                                                        @else
                                                            <img
                                                                src="{{ $__heroImg }}"
                                                                alt="{{ $item->title }}"
                                                                class="swiper-lazy w-full h-full "
                                                                decoding="async"
                                                                loading="lazy"
                                                            >
                                                        @endif
                                                    </a>
                                                </div>
                                                <figcaption class="pt-10">
                                                    <a href="{{ $item->url }}" title="{{ $item->title }}" class="md:min-h-16 inline-flex items-center">
                                                        <div class="font-unna font-bold text-2xl text-ellipsis inline-flex">
                                                            <span class="line-clamp-2">{{ $item->title }}</span>
                                                        </div>
                                                    </a>
                                                </figcaption>
                                            </figure>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="w-full h-20 absolute bottom-0">
                                <div class="swiper-pagination relative type4"></div>
                            </div>
                            <div class="main-slider text-black absolute left-0 top-[170px] 3xl:top-[218px] h-6 w-full z-10 pointer-events-none">
                                <button aria-label="prev" class="swiper-button-prev pointer-events-auto after:content-[''] bg-black bg-opacity-40 text-white border-y border-r border-white w-9 h-9 bloomberght-icon-font-chevron-left left-0 cursor-pointer text-inherit hidden md:flex"></button>
                                <button aria-label="next" class="swiper-button-next pointer-events-auto after:content-[''] bg-black bg-opacity-40 text-white border-y border-l border-white w-9 h-9 bloomberght-icon-font-chevron-right right-0 cursor-pointer text-inherit hidden md:flex"></button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <aside class="w-full md:w-[400px] relative fund-aside">
                @include('fund.partials.tool-nav', ['toolLinks' => $toolLinks])
                <x-ad-zone zone="2304" :index="1" :label="'REKLAM advertisement1'" />
                <x-ad-zone zone="2304" :index="2" :label="'REKLAM advertisement1'" />
            </aside>
        </div>

        {{-- Fon Saati — TV ekranında video epizotları (swiper carousel, referans birebir) --}}
        @if ($fonSaati->isNotEmpty())
            <section class="fund-saati" id="fon-saati">
                <x-section-head :title="__('media.fon_saati')" />
                <div class="funds relative">
                    <div data-type="swiper" data-name="" class="swiper slider-custom relative w-full"
                         data-swiper='{"slidesPerView":1,"spaceBetween":0,"autoHeight":true,"breakpoints":{"@0.00":{"slidesPerView":1,"spaceBetween":0},"@0.50":{"slidesPerView":2.2,"spaceBetween":20},"@1.00":{"slidesPerView":3.2,"spaceBetween":20}},"lazy":true,"navigation":{"nextEl":".funds .swiper-button-next","prevEl":".funds .swiper-button-prev"}}'>
                        <div class="swiper-wrapper">
                            @foreach ($fonSaati as $episode)
                                @php
                                    $__series = $episode->program?->slug;
                                    $__fundsUrl = $__series
                                        ? \App\Support\Locale::url('/'.$__series.'/video/'.$episode->slug.'/'.$episode->id)
                                        : \App\Support\Locale::url('/tv');
                                @endphp
                                <div class="swiper-slide " style="">
                                    <a href="{{ $__fundsUrl }}" title="{{ $episode->display_title }}">
                                        <figure class="mb-4">
                                            <div class="mb-4 relative">
                                                <div class="w-full h-auto aspect-video bg-gray-50">
                                                    <x-lazy-image :src="asset($episode->thumbnail ?: $episode->program?->thumbnail ?: '/img/placeholders/placeholder-1.svg')"
                                                                  alt="" class="img-contract w-full h-full" />
                                                </div>
                                                <div class="absolute right-0 bottom-0">
                                                    <div class="bloomberght-icon-font-play bg-white text-black rounded-tl-lg flex justify-center items-center w-14 h-14 mb-[1px]"></div>
                                                    <div class="flex justify-center items-center bg-white text-black w-14 h-7 text-xs">{{ $episode->duration }}</div>
                                                </div>
                                            </div>
                                            <figcaption>
                                                <div class="font-unna font-bold text-xl text-ellipsis line-clamp-3 mb-1">{{ $episode->display_title }}</div>
                                            </figcaption>
                                        </figure>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <button aria-label="prev" class="swiper-button-prev after:content-[''] w-8 h-11 bloomberght-icon-font-chevron-left right-14 cursor-pointer text-inherit"></button>
                    <button aria-label="next" class="swiper-button-next after:content-[''] w-8 h-11 bloomberght-icon-font-chevron-right right-0 cursor-pointer text-inherit"></button>
                </div>
            </section>
        @endif

        {{-- Günün Özeti — KPI tablo (referans birebir: gri zemin bandı + sticky gri başlık + 4 satır) --}}
        <section class="fund-kpi" id="gunun-ozeti">
            <div class="md:mt-8 relative py-8 px-2">
                <div class="absolute top-0 left-0 right-0 h-full bg-cover bg-center bg-no-repeat -z-100 before:absolute before:top-0 before:left-0 before:w-full before:h-full before:pointer-events-none bg-gray-100"></div>
                <div class="overflow-x-auto border mb-4 relative text-left">
                    <div class="bg-gray-200 text-center text-base py-2 sticky left-0 top-0 font-bold">{{ __('media.today_summary') }}</div>
                    <table data-type="table-type1" class="w-full text-center text-sm bg-white">
                        <tbody class="text-left">
                            <tr class="border-b text-sm">
                                <td class="py-4 w-[55%]"><span class="p-1 font-bold">{{ __('media.portfolio_value') }}</span></td>
                                <td class="py-4"><span>{{ number_format((float) $kpi['portfolio_value'], 0, '.', ',') }}</span></td>
                            </tr>
                            <tr class="border-b text-sm bg-gray-100">
                                <td class="py-4 w-[55%]"><span class="p-1 font-bold">{{ __('media.portfolio_pct') }}</span></td>
                                <td class="py-4"><span class="{{ $kpi['portfolio_change'] > 0 ? 'text-green-700' : ($kpi['portfolio_change'] < 0 ? 'text-red-700' : 'text-gray-400') }}">{{ number_format((float) $kpi['portfolio_change'], 2, '.', '') }}</span></td>
                            </tr>
                            <tr class="border-b text-sm">
                                <td class="py-4 w-[55%]"><span class="p-1 font-bold">{{ __('media.fund_count') }}</span></td>
                                <td class="py-4"><span>{{ $kpi['fund_count'] }}</span></td>
                            </tr>
                            <tr class="border-b text-sm bg-gray-100">
                                <td class="py-4 w-[55%]"><span class="p-1 font-bold">{{ __('media.investor_count') }}</span></td>
                                <td class="py-4"><span>{{ number_format((int) $kpi['investor_count'], 0, '.', ',') }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        {{-- Fon Sınıflarında Bugün — 7 sınıf dropdown + Yükselen/Düşen iki tablo --}}
        @include('fund.partials.classes-widget', ['fundClasses' => $fundClasses])

        {{-- Karşılaştırmalı Fon Getirileri — tüm fonlar + Tümünü Görüntüle --}}
        <section class="fund-returns" id="fon-getirileri">
            <x-section-head :title="__('media.comparative_returns')" />
            @include('fund.partials.returns-table', ['rows' => $returnRows])
        </section>
    </div>
@endsection
