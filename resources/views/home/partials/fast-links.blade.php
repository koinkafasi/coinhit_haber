{{-- Hızlı Linkler (fastLinks) — referans birebir:
     başlık düz <a>, swiper pill'leri (ilk bg-gray-200), .fast-link ok sarmalayıcısı,
     tab-body başlık panelleri (her trendin son haberi). --}}
<section class="relative" data-widget-type="fastLinks">
    <div class="mb-4 border-b border-black relative before:absolute before:top-0 before:bottom-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black"></div>
    <a href="{{ \App\Support\Locale::url('/trendler') }}" title=""
       class="absolute top-4 left-0 font-unna font-bold text-3xl mb-2 z-10">{{ __('ui.quick_links') }}</a>
    <div class="tab-menu">
        <div class="tab-head my-4">
            <div data-type="swiper" data-name="" class="swiper slider-custom relative mb-4 pt-12 w-full text-sm md:text-base"
                 data-swiper='{"slidesPerView":"auto","spaceBetween":10,"loop":false,"lazy":true,"navigation":{"nextEl":".fast-link .swiper-button-next","prevEl":".fast-link .swiper-button-prev"}}'>
                <div class="swiper-wrapper">
                    @foreach ($trends as $__i => $trend)
                        <div class="swiper-slide w-auto">
                            <button type="button"
                                    class="tab-button inline-block px-4 py-2 rounded-3xl border {{ $__i === 0 ? 'bg-gray-200 font-bold is-active' : '' }}"
                                    data-tab="tab-fast-{{ $__i }}"
                                    aria-selected="{{ $__i === 0 ? 'true' : 'false' }}">{{ $trend->label }}</button>
                        </div>
                    @endforeach
                </div>
                <div class="fast-link text-black bg-white absolute -right-3 top-4 w-20 pl-4 z-90">
                    <button type="button" aria-label="prev" class="swiper-button-prev after:content-[''] w-8 h-11 bloomberght-icon-font-chevron-left right-14 cursor-pointer text-inherit"></button>
                    <button type="button" aria-label="next" class="swiper-button-next after:content-[''] w-8 h-11 bloomberght-icon-font-chevron-right right-0 cursor-pointer text-inherit"></button>
                </div>
            </div>
        </div>
        <div class="tab-body">
            @foreach ($trends as $__i => $trend)
                @php($__lead = $trend->news->first())
                <div class="tab-content {{ $__i === 0 ? '' : 'hidden' }}" data-id="tab-fast-{{ $__i }}">
                    @if ($__lead)
                        <a href="{{ $__lead->url }}" title="{{ $__lead->title }}">
                            <div class="font-unna font-bold text-xl line-clamp-5 mb-2">{{ $__lead->title }}</div>
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
