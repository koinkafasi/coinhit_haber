{{-- Video kart — type2 (kenar listesi): solda mini thumbnail + sağda başlık (referans video-card-type2 birebir;
     seri içi son kartta `border-b` yok) --}}
@props(['episode', 'last' => false])

@php
    $__seriesSlug = $episode->program?->slug;
    $__url = $__seriesSlug
        ? \App\Support\Locale::url('/'.$__seriesSlug.'/video/'.$episode->slug.'/'.$episode->id)
        : \App\Support\Locale::url('/video');
@endphp

<div data-type="video-card-type2">
    <a href="{{ $__url }}" title="{{ $episode->display_title }}">
        <figure class="flex items-center gap-3{{ $last ? '' : ' border-b' }} pb-4 mb-4">
            <div class="w-1/2">
                <div class="relative aspect-video bg-gray-50">
                    <x-lazy-image :src="asset($episode->thumbnail ?: $episode->program?->thumbnail ?: '/img/placeholders/placeholder-1.svg')"
                                  alt="" class="w-full h-full" />
                    <div class="absolute right-0 bottom-0 flex items-center">
                        <span class="bloomberght-icon-font-play bg-white rounded-tl-lg flex justify-center items-center w-7 h-7 mr-[1px] text-xxs text-black"></span>
                        <span class="flex justify-center items-center bg-white w-12 h-7 text-xs text-black"><span class="program-duration">{{ $episode->duration }}</span></span>
                    </div>
                </div>
            </div>
            <figcaption class="w-1/2 flex flex-col justify-around">
                <div class="font-unna font-bold text-base text-ellipsis line-clamp-3">{{ $episode->display_title }}</div>
                <div class="text-xxs"></div>
            </figcaption>
        </figure>
    </a>
</div>
