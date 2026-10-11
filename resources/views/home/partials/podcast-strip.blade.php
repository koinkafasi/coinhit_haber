{{-- Ana sayfa Podcast Serileri swiper şeridi (referans podcast band, CH-27). --}}
<section class="home-podcasts home-podcasts-strip" id="home-podcasts">
    <div class="bht-grid col-span-12" dataType="podcast">
        <div data-widget-type="podcast" class="podcast-band relative">
            <div class="mb-4 border-b border-black relative w-full bcast-rule" aria-hidden="true"></div>
            <a href="{{ \App\Support\Locale::url('/podcastler/') }}" title="{{ __('ui.podcasts') }}"
               class="absolute top-4 left-0 font-unna font-bold text-3xl z-10 bcast-title">{{ __('ui.podcasts') }}</a>

            <div data-type="swiper" class="swiper slider-custom relative mb-4 pt-12 w-full podcast-swiper"
                 data-swiper='{"slidesPerView":"auto","spaceBetween":20,"lazy":true,"breakpoints":{"0":{"slidesPerView":1.6,"spaceBetween":14},"768":{"slidesPerView":2.2,"spaceBetween":20},"1200":{"slidesPerView":3.4,"spaceBetween":20}}}'>
                <div class="swiper-wrapper">
                    @foreach ($podcasts as $podcast)
                        <div class="swiper-slide" style="width: 17rem;">
                            <a href="{{ $podcast->url }}" title="{{ $podcast->title }}">
                                <figure class="podcast-strip-card">
                                    <div class="podcast-strip-cover">
                                        <x-lazy-image :src="asset($podcast->cover ?: '/img/og-default.png')" alt="{{ $podcast->title }}" contract="news_card_square" class="podcast-strip-img" />
                                    </div>
                                    <figcaption>
                                        <div class="font-unna font-bold text-2xl mb-2 text-ellipsis line-clamp-1">{{ $podcast->title }}</div>
                                        <p class="text-sm text-ellipsis line-clamp-3 mb-4 podcast-strip-desc">{{ $podcast->description }}</p>
                                    </figcaption>
                                </figure>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
