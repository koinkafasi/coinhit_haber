{{-- Ana sayfa Yayın Akışı swiper şeridi (referans streaming, CH-27). --}}
<section class="home-broadcast home-broadcast-strip" data-section="streaming" id="home-broadcast">
    <div class="bht-grid col-span-12" dataType="streaming">
        <div data-widget-type="streaming" class="relative">
            <div data-type="background" class="absolute -z-10 left-1/2 top-0 w-[100vw] h-96 -translate-x-1/2 -mt-4 bg-cover bg-no-repeat bg-gray-100 h-full mt-0"></div>
            <div class="mb-4 border-b border-black relative w-full before:absolute before:top-0 before:bottom-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black"></div>
            <a href="{{ \App\Support\Locale::url('/yayinakisi') }}" title="{{ __('ui.broadcast') }}"
               class="absolute top-4 font-unna font-bold text-3xl mb-2 z-10 bcast-title"><h2>{{ __('ui.broadcast') }}</h2></a>

            <div>
                <div data-type="swiper" data-name="" data-slide-to="true" class="swiper slider-custom relative mb-4 pt-12 pb-6 w-full streaming"
                     data-swiper='{"slidesPerView":"auto","spaceBetween":20,"loop":false,"lazy":true,"navigation":{"nextEl":".streaming .swiper-button-next","prevEl":".streaming .swiper-button-prev"}}'>
                    <div class="swiper-wrapper">
                        @foreach ($broadcast as $row)
                            <div class="swiper-slide w-auto w-64 md:w-72" data-slide-to-key="false">
                                <a href="{{ $row->program?->url ?? \\App\\Support\\Locale::url('/yayinakisi') }}" title="{{ $row->program_name }}" class="block">
                                    <div class="flex items-center border rounded-l-full border-gray-400 rounded p-2 bg-white bcast-card">
                                        <div class="h-24 aspect-square bcast-thumb">
                                            @if ($row->program?->thumbnail)
                                                <x-lazy-image :src="asset($row->program->thumbnail)" alt="" contract="video_thumb" class="w-full h-full rounded-full bcast-thumb-img" />
                                            @else
                                                <span class="bcast-thumb-ph w-full h-full rounded-full flex items-center justify-center bg-gray-100 text-2xl font-bold">{{ mb_strtoupper(mb_substr((string) $row->program_name, 0, 1)) }}</span>
                                            @endif
                                        </div>
                                        <div class="ml-3 bcast-info">
                                            <div class="font-bold text-2xl bcast-time">{{ $row->air_time }}</div>
                                            <div class="font-unna line-clamp-2 text-lg bcast-name">{{ $row->program_name }}</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
