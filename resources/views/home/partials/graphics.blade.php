{{-- Referans graphics ölçümü: col-span-4, başlık düz metin (<h2> yok), --}}
<section class="home-graphics relative w-full" data-widget-type="graphics">
    <div class="mb-4 border-b border-black relative w-full before:absolute before:top-0 before:bottom-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black"></div>
    <a href="{{ \App\Support\Locale::url('/grafik-arsivi') }}" title=""
       class="absolute top-4 left-0 font-unna font-bold text-3xl mb-2 z-10">{{ __('ui.charts') }}</a>
    <div data-type="swiper" data-name="" class="swiper slider-custom relative mb-4 pt-12 w-full graphic"
         data-swiper='{"slidesPerView":"1","spaceBetween":20,"autoplay":{"delay":2500,"disableOnInteraction":false},"loop":false,"lazy":true,"navigation":{"nextEl":".graphic .swiper-button-next","prevEl":".graphic .swiper-button-prev"}}'>
        <div class="swiper-wrapper">
            @foreach ($charts as $chart)
                <div class="swiper-slide">
                    <x-news-card :news="$chart" type="type3" />
                </div>
            @endforeach
        </div>
        <div class="swiper-button-prev bloomberght-icon-font-chevron-left"></div>
        <div class="swiper-button-next bloomberght-icon-font-chevron-right"></div>
    </div>
</section>
