{{-- Ana sayfa Yayın Akışı swiper şeridi — referans (bloomberght.com) birebir (CH-27, CH-36). --}}
<section id="home-broadcast" data-section="streaming" class="grid grid-cols-12 gap-4 mb-4 md:mb-10">
    <div class="bht-grid col-span-12 md:col-span-12 row-span-1" datatype="streaming">
        <div data-widget-type="streaming" class="relative">
            <div data-type="background" class="absolute -z-10 left-1/2 top-0 w-[100vw] h-96 -translate-x-1/2 -mt-4 bg-cover bg-no-repeat bg-gray-100 h-full mt-0"></div>
            <div class="mb-4 border-b border-black relative w-full before:absolute before:top-0 before:bottom-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black"></div>
            <a href="{{ \App\Support\Locale::url('/yayinakisi') }}" title="{{ __('ui.broadcast') }}"
               class="absolute top-4 font-unna font-bold text-3xl mb-2 z-10"><h2>{{ __('ui.broadcast') }}</h2></a>

            <div>
                <div data-type="swiper" data-name="" data-slide-to="true" class="swiper slider-custom relative mb-4 pt-12 pb-6 w-full"
                     data-swiper='{"slidesPerView":"auto","spaceBetween":20,"loop":false,"lazy":true,"navigation":{"nextEl":".streaming .swiper-button-next","prevEl":".streaming .swiper-button-prev"}}'>
                    <div class="swiper-wrapper">
                        @foreach ($broadcast as $row)
                            <div class="swiper-slide w-64 md:w-72">
                                <a href="{{ $row->program?->url ?? \App\Support\Locale::url('/yayinakisi') }}" title="{{ $row->program_name }}" class="block">
                                    <div class="flex items-center border border-gray-400 rounded-l-full bg-white p-2">
                                        <div class="w-24 h-24 rounded-full overflow-hidden shrink-0">
                                            @if ($row->program?->thumbnail)
                                                <x-lazy-image :src="asset($row->program->thumbnail)" alt="" contract="video_thumb" class="w-full h-full rounded-full object-cover" />
                                            @else
                                                <span class="w-full h-full rounded-full flex items-center justify-center bg-gray-100 text-2xl font-bold">{{ mb_strtoupper(mb_substr((string) $row->program_name, 0, 1)) }}</span>
                                            @endif
                                        </div>
                                        <div class="ml-3 min-w-0">
                                            <div class="font-bold text-2xl">{{ $row->air_time }}</div>
                                            <div class="font-unna line-clamp-2 text-xl">{{ $row->program_name }}</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    {{-- Yayın Akışı prev/next ok butonları — referans nav bloğu birebir --}}
                    <div class="streaming text-black bg-white absolute -right-3 top-4 w-20 pl-4 z-90">
                        <button aria-label="prev" class="swiper-button-prev after:content-[''] w-8 h-11 bloomberght-icon-font-chevron-left right-14 cursor-pointer text-inherit"><svg class="swiper-navigation-icon" width="11" height="20" viewBox="0 0 11 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.38296 20.0762C0.111788 19.805 0.111788 19.3654 0.38296 19.0942L9.19758 10.2796L0.38296 1.46497C0.111788 1.19379 0.111788 0.754138 0.38296 0.482966C0.654131 0.211794 1.09379 0.211794 1.36496 0.482966L10.4341 9.55214C10.8359 9.9539 10.8359 10.6053 10.4341 11.007L1.36496 20.0762C1.09379 20.3474 0.654131 20.3474 0.38296 20.0762Z" fill="currentColor"></path></svg></button>
                        <button aria-label="next" class="swiper-button-next after:content-[''] w-8 h-11 bloomberght-icon-font-chevron-right right-0 cursor-pointer text-inherit"><svg class="swiper-navigation-icon" width="11" height="20" viewBox="0 0 11 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.38296 20.0762C0.111788 19.805 0.111788 19.3654 0.38296 19.0942L9.19758 10.2796L0.38296 1.46497C0.111788 1.19379 0.111788 0.754138 0.38296 0.482966C0.654131 0.211794 1.09379 0.211794 1.36496 0.482966L10.4341 9.55214C10.8359 9.9539 10.8359 10.6053 10.4341 11.007L1.36496 20.0762C1.09379 20.3474 0.654131 20.3474 0.38296 20.0762Z" fill="currentColor"></path></svg></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
