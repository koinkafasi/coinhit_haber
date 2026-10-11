{{-- Ana sayfa Yayın Akışı swiper şeridi (referans streaming, CH-27). --}}
<section class="home-broadcast home-broadcast-strip" data-section="streaming" id="home-broadcast">
    <div class="bht-grid col-span-12" dataType="streaming">
        <div data-widget-type="streaming" class="bcast-strip-relative relative">
            <div class="mb-4 border-b border-black relative w-full bcast-rule" aria-hidden="true"></div>
            <a href="{{ \App\Support\Locale::url('/yayinakisi') }}" title="{{ __('ui.broadcast') }}"
               class="absolute top-4 font-unna font-bold text-3xl mb-2 z-10 bcast-title"><h2>{{ __('ui.broadcast') }}</h2></a>

            <div data-type="swiper" class="swiper slider-custom relative mb-4 pt-12 pb-4 w-full bcast-swiper"
                 data-swiper='{"slidesPerView":"auto","spaceBetween":20,"lazy":true,"breakpoints":{"0":{"slidesPerView":1.4,"spaceBetween":14},"768":{"slidesPerView":"auto","spaceBetween":20}}}'>
                <div class="swiper-wrapper">
                    @foreach ($broadcast as $row)
                        <div class="swiper-slide w-auto" style="width: 18rem;">
                            <a href="{{ \App\Support\Locale::url('/yayinakisi') }}" title="{{ $row->program_name }}" class="block">
                                <span class="bcast-card">
                                    <span class="bcast-thumb">
                                        @if ($row->program?->thumbnail)
                                            <x-lazy-image :src="asset($row->program->thumbnail)" alt="" contract="video_thumb" class="bcast-thumb-img" />
                                        @else
                                            <span class="bcast-thumb-ph">{{ mb_strtoupper(mb_substr((string) $row->program_name, 0, 1)) }}</span>
                                        @endif
                                    </span>
                                    <span class="bcast-info">
                                        <span class="bcast-time font-bold">{{ $row->air_time }}</span>
                                        <span class="bcast-name font-unna line-clamp-2">{{ $row->program_name }}</span>
                                    </span>
                                </span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
