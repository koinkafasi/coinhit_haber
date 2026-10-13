{{-- Businessweek + SON SAYILAR — referans (bloomberght.com) <section data-section="businessweek"> birebir.
     Sol: Businessweek logosu + öne çıkan haberler (type3 + type1 + type4 + type1).
     Sağ: "Son Sayılar" h2 + dergi kapakları swiper (effect: "cards").
     Swiper başlatma: site.js initSwiper() -> `[data-type="swiper"]` + `data-swiper` JSON. --}}
@php
    $bwNews = $bwNews ?? ($featuredNews ?? collect());
    $bwNews = $bwNews instanceof \Illuminate\Support\Collection
        ? $bwNews->take(4)->values()
        : collect(is_array($bwNews) ? array_values($bwNews) : [])->take(4)->values();

    // SON SAYILAR: dergi kapakları. Veri modeli yok; sayılar Cuma günü yayınlanır
    // (referansla aynı), haftalık döngüyle türetilir; kapaklar yerel placeholder'dır.
    $bwIssues = collect(range(0, 4))->map(function (int $i) {
        $date = now()->subWeeks($i)->startOfWeek(\Carbon\CarbonInterface::FRIDAY)->locale('tr');
        $label = $date->isoFormat('DD MMMM YYYY') . ', ' . $date->isoFormat('dddd');
        return (object) [
            'title' => __('ui.latest_issues') . ' | ' . $label,
            'date'  => $label,
            'url'   => 'https://www.businessweek.com.tr/',
            'image' => '/img/placeholders/placeholder-' . (($i % 8) + 1) . '.svg',
        ];
    });

    [$bwBig, $bwList] = [$bwNews->first(), $bwNews->slice(1, 3)->values()];
@endphp

<section data-section="businessweek" class="home-businessweek grid grid-cols-12 gap-4 mb-4 md:mb-10">
    <div class="bht-grid col-span-12 md:col-span-12 row-span-1" dataType="businessWeekTurkey">
        <div data-widget-type="businessWeekTurkey" class="relative pt-8 pb-4 flex flex-wrap md:flex-nowrap gap-6">

            {{-- Koyu bant (tam genişlik) — referans bg-image-15.png yerine düz siyah --}}
            <div data-type="background" class="absolute -z-10 left-1/2 top-0 w-[100vw] h-full -translate-x-1/2 -mt-4 bg-cover bg-no-repeat bg-black"></div>

            {{-- SOL: Businessweek logosu + öne çıkan haberler --}}
            <div class="w-full md:w-9/12">
                <a href="https://www.businessweek.com.tr/" title="Businessweek" target="_blank" rel="noopener noreferrer" class="block h-16 mb-8">
                    <x-lazy-image src="/brand/businessweek-logo-white.svg" alt="businessweek" class="h-full w-auto" eager />
                </a>

                @if ($bwBig)
                    <div class="text-white overflow-hidden flex flex-col gap-4 flex-wrap h-auto md:max-h-[400px] bg-cover bg-center bg-no-repeat relative">
                        <div class="flex flex-wrap md:flex-nowrap w-full gap-4">
                            <div class="w-full md:w-1/2">
                                {{-- type3: geniş video kapağı + başlık --}}
                                <div data-type="news-card-type3" data-news-id="{{ $bwBig->news_id }}" class="mb-4">
                                    <a href="{{ $bwBig->url }}" title="{{ $bwBig->title }}">
                                        <figure>
                                            <div class="w-full h-full aspect-video bg-gray-50 mb-4">
                                                <x-lazy-image :src="asset($bwBig->image ?: '/img/og-default.png')" alt="" contract="news_card" class="w-full h-full" />
                                            </div>
                                            <figcaption class="w-full h-full flex flex-col justify-around">
                                                <div class="font-unna font-bold text-base 2xl:text-xl text-ellipsis line-clamp-2">{{ $bwBig->title }}</div>
                                            </figcaption>
                                        </figure>
                                    </a>
                                </div>
                            </div>
                            <div class="w-full md:w-1/2">
                                @foreach ($bwList as $__i => $__item)
                                    @if ($__i === 1)
                                        {{-- type4: başlık solda + küçük görsel sağda --}}
                                        <div data-type="news-card-type4" data-news-id="{{ $__item->news_id }}" class="border-b mb-2 pb-2">
                                            <a href="{{ $__item->url }}" title="{{ $__item->title }}">
                                                <figure class="flex gap-2 justify-between">
                                                    <figcaption class="w-8/12 flex flex-col justify-center">
                                                        <div class="font-unna font-bold text-base 2xl:text-xl text-ellipsis line-clamp-3">{{ $__item->title }}</div>
                                                    </figcaption>
                                                    <div class="w-4/12 h-full aspect-square bg-gray-50 max-w-40">
                                                        <x-lazy-image :src="asset($__item->image ?: '/img/og-default.png')" alt="" contract="news_card_square" class="w-full h-full object-fill" />
                                                    </div>
                                                </figure>
                                            </a>
                                        </div>
                                    @else
                                        {{-- type1: başlık --}}
                                        <div data-type="news-card-type1" data-news-id="{{ $__item->news_id }}" class="{{ $__i === 2 ? 'md:mb-4' : 'border-b mb-2 pb-2' }}">
                                            <a href="{{ $__item->url }}" title="{{ $__item->title }}">
                                                <figure>
                                                    <figcaption>
                                                        <div class="font-unna font-bold text-base 2xl:text-xl text-ellipsis line-clamp-2">{{ $__item->title }}</div>
                                                    </figcaption>
                                                </figure>
                                            </a>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- SAĞ: SON SAYILAR — kapak swiper'ı (effect: cards) --}}
            <div class="w-3/5 md:w-3/12 md:p-8 m-auto 2xl:p-12">
                <h2 class="font-unna font-bold text-3xl mb-2 text-center text-white">{{ __('ui.latest_issues') }}</h2>

                <div data-type="swiper" data-name=""
                     class="swiper slider-custom relative"
                     data-swiper='{"slidesPerView":1,"loop":true,"lazy":true,"effect":"cards","grabCursor":true,"navigation":{"nextEl":".businessweek .bloomberght-icon-font-chevron-right","prevEl":".businessweek .bloomberght-icon-font-chevron-left"}}'>
                    <div class="swiper-wrapper">
                        @forelse ($bwIssues as $issue)
                            <div class="swiper-slide w-auto">
                                <a href="{{ $issue->url }}" title="{{ $issue->title }}" class="inline-block w-full">
                                    <figure class="bg-black">
                                        <div class="w-full aspect-[260/342] mb-4 overflow-hidden">
                                            <x-lazy-image :src="asset($issue->image)" alt="{{ $issue->title }}" contract="magazine" class="w-full h-full object-cover" :eager="$loop->first" />
                                        </div>
                                        <figcaption>
                                            <div class="2xl:text-sm text-xxs text-center text-ellipsis line-clamp-1">{{ $issue->date }}</div>
                                        </figcaption>
                                    </figure>
                                </a>
                            </div>
                        @empty
                            <p class="most-read-empty">{{ __('ui.no_news') }}</p>
                        @endforelse
                    </div>

                    <div class="businessweek text-white absolute left-0 bottom-0 h-7 w-full z-90">
                        <button type="button" aria-label="prev" class="swiper-button-prev after:content-[''] w-9 h-9 bloomberght-icon-font-chevron-left left-0 cursor-pointer text-inherit"></button>
                        <button type="button" aria-label="next" class="swiper-button-next after:content-[''] w-9 h-9 bloomberght-icon-font-chevron-right right-0 cursor-pointer text-inherit"></button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
