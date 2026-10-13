{{-- Video kart — type1 (kapsamlı): büyük thumbnail + play + süre + başlık (referans video-card-type1 birebir) --}}
@props(['episode'])

@php
    $__seriesSlug = $episode->program?->slug;
    $__url = $__seriesSlug
        ? \App\Support\Locale::url('/'.$__seriesSlug.'/video/'.$episode->slug.'/'.$episode->id)
        : \App\Support\Locale::url('/video');
@endphp

<div data-type="video-card-type1">
    <a href="{{ $__url }}" title="{{ $episode->display_title }}">
        <figure>
            <div class="mb-4 relative">
                <div class="w-full h-auto aspect-video bg-gray-50">
                    <x-lazy-image :src="asset($episode->thumbnail ?: $episode->program?->thumbnail ?: '/img/placeholders/placeholder-1.svg')"
                                  alt="" class="w-full h-full" />
                </div>
                <div class="absolute right-0 bottom-0">
                    <div class="bloomberght-icon-font-play bg-white text-black rounded-tl-lg flex justify-center items-center w-14 h-14 mb-[1px]"></div>
                    <div class="flex justify-center items-center bg-white text-black w-14 h-7 text-xs"><span class="program-duration">{{ $episode->duration }}</span></div>
                </div>
            </div>
            <figcaption>
                <div class="font-unna font-bold 2xl:text-2xl text-base text-ellipsis line-clamp-2">{{ $episode->display_title }}</div>
                <div class="text-xxs"></div>
            </figcaption>
        </figure>
    </a>
</div>
