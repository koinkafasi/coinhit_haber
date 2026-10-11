{{-- Ana sayfa Podcast Serileri swiper şeridi (referans podcast band, CH-27). --}}
<section class="home-podcasts home-podcasts-strip" id="home-podcasts">
    <div class="bht-grid col-span-12" dataType="podcast">
        <div data-widget-type="podcast" class="relative">
            <div data-type="background" class="absolute -z-10 left-1/2 top-0 w-[100vw] h-96 -translate-x-1/2 -mt-4 overflow-hidden bg-gray-100 h-full mt-0" aria-hidden="true">
                <picture>
                    <img src="https://im.bloomberght.com/assets/bht/bg/desktop/2x/bg-image-2.png?v=1004" alt="" role="presentation" loading="eager" fetchpriority="high" decoding="async" class="absolute inset-0 w-full h-full object-cover object-[center_top] max-md:object-contain max-md:object-[center_top]">
                </picture>
            </div>
            <div class="mb-4 border-b border-black relative w-full before:absolute before:top-0 before:bottom-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black" aria-hidden="true"></div>
            <a href="{{ \App\Support\Locale::url('/podcastler/') }}" title="{{ __('ui.podcasts') }}"
               class="absolute top-4 left-0 font-unna font-bold text-3xl mb-2 z-10"><h2>{{ __('ui.podcasts') }}</h2></a>

            <div>
                <div data-type="swiper" data-name="" class="swiper slider-custom relative mb-4 pt-12 w-full podcast"
                     data-swiper='{"slidesPerView":"auto","spaceBetween":20,"loop":false,"lazy":true,"navigation":{"nextEl":".podcast .swiper-button-next","prevEl":".podcast .swiper-button-prev"}}'>
                    <div class="swiper-wrapper">
                        @foreach ($podcasts as $podcast)
                            <div class="swiper-slide w-72">
                                <a href="{{ $podcast->url }}" title="{{ $podcast->title }}">
                                    <figure class="podcast-strip-card">
                                        <div class="w-full h-auto mb-4 aspect-square bg-gray-50 podcast-strip-cover">
                                            <x-lazy-image :src="asset($podcast->cover ?: '/img/og-default.png')" alt="{{ $podcast->title }}" contract="news_card_square" class="swiper-lazy w-full h-full podcast-strip-img" />
                                        </div>
                                        <figcaption>
                                            <div class="font-unna font-bold text-2xl mb-2 text-ellipsis line-clamp-1">{{ $podcast->title }}</div>
                                            <div class="text-sm text-ellipsis line-clamp-3 mb-4 podcast-strip-desc">{{ $podcast->description }}</div>
                                        </figcaption>
                                    </figure>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
