{{-- Altın alt-hızlı bağlantıları — referans birebir: 8 kenarlıklı beyaz hap, yatay swiper --}}
<div data-widget-type="tab-menu" class="mb-4" aria-label="{{ __('market.gold_quick_links') }}">
    <div data-type="swiper" data-name="" class="swiper slider-custom relative" data-swiper='{"slidesPerView":"auto","spaceBetween":0}'>
        <div class="swiper-wrapper">
            @foreach ($quickLinks as $link)
                <div class="swiper-slide bg-white text-xs text-center text-black max-w-fit border{{ $loop->last ? ' border-r-0' : '' }}" style="">
                    <a href="{{ \App\Support\Locale::url($link['url']) }}" class="block py-4 px-4 whitespace-nowrap ">{{ $link['label'] }}</a>
                </div>
            @endforeach
        </div>
    </div>
</div>
