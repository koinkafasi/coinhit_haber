{{-- Foto Haber kartları — referans (bloomberght.com) photoNews birebir.
     Sol kolon : type5 (bölüm başlıklı "Foto Haber", çizgili) + type2 (mb-4)
     Sağ kolon : type1 + type1 + type5 (çizgisiz büyük)
     TÜMÜ linki: link-type1 deseni. --}}
@php
    $__photoItems = $galleries->take(5)->values();
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 md:gap-4">
    {{-- SOL --}}
    <div class="w-full flex flex-col justify-between">
        @if ($__photoItems->get(0))
            <div data-type="news-card-type5" data-news-id="{{ $__photoItems->get(0)->news_id }}" class="">
                <a href="{{ \App\Support\Locale::url('/fotohaber') }}" title="{{ __('ui.photo_news') }}" class="block font-unna font-bold text-3xl mb-2 z-10">{{ __('ui.photo_news') }}</a>
                <a href="{{ $__photoItems->get(0)->url }}" title="{{ $__photoItems->get(0)->title }}" class="block">
                    <figure class="relative w-full border-b mb-2 pb-2 ">
                        <div class="w-full aspect-square bg-gray-50">
                            <x-lazy-image :src="asset($__photoItems->get(0)->cover ?: '/img/og-default.png')" alt="" contract="news_card_square" class="w-full h-full " />
                        </div>
                        <figcaption class="relative w-11/12 h-auto -mt-12 p-4 pl-0 md:min-h-30 bg-white">
                            <div class="font-unna font-bold text-xl 2xl:text-2xl text-ellipsis mb-2 md:min-h-[64px]">
                                <span class="line-clamp-2">{{ $__photoItems->get(0)->title }}</span>
                            </div>
                        </figcaption>
                    </figure>
                </a>
            </div>
        @endif
        @if ($__photoItems->get(1))
            <div data-type="news-card-type2" data-news-id="{{ $__photoItems->get(1)->news_id }}" class="mb-4">
                <a href="{{ $__photoItems->get(1)->url }}" title="{{ $__photoItems->get(1)->title }}">
                    <figure class="flex gap-2 ">
                        <div class="w-4/12 h-full aspect-square bg-gray-50 max-w-40">
                            <x-lazy-image :src="asset($__photoItems->get(1)->cover ?: '/img/og-default.png')" alt="" contract="news_card_square" class="w-full h-full object-fill " />
                        </div>
                        <figcaption class="w-8/12 flex flex-col justify-center space-y-2">
                            @if ($__photoItems->get(1)->category?->name)
                                <div class="category text-xs">{{ $__photoItems->get(1)->category->name }}</div>
                            @endif
                            <div class="font-unna font-bold text-base 2xl:text-xl text-ellipsis line-clamp-3 ">{{ $__photoItems->get(1)->title }}</div>
                        </figcaption>
                    </figure>
                </a>
            </div>
        @endif
    </div>

    {{-- SAĞ --}}
    <div class="w-full flex flex-col justify-between">
        @foreach ([2, 3] as $__idx)
            @if ($__photoItems->get($__idx))
                <div data-type="news-card-type1" data-news-id="{{ $__photoItems->get($__idx)->news_id }}" class="border-b mb-2 pb-2 ">
                    <a href="{{ $__photoItems->get($__idx)->url }}" title="{{ $__photoItems->get($__idx)->title }}">
                        <figure class="">
                            <figcaption>
                                @if ($__photoItems->get($__idx)->category?->name)
                                    <div class="category text-xs">{{ $__photoItems->get($__idx)->category->name }}</div>
                                @endif
                                <div class="font-unna font-bold text-base 2xl:text-xl text-ellipsis line-clamp-2 ">{{ $__photoItems->get($__idx)->title }}</div>
                            </figcaption>
                        </figure>
                    </a>
                </div>
            @endif
        @endforeach
        @if ($__photoItems->get(4))
            <div data-type="news-card-type5" data-news-id="{{ $__photoItems->get(4)->news_id }}" class="">
                <a href="{{ $__photoItems->get(4)->url }}" title="{{ $__photoItems->get(4)->title }}" class="block">
                    <figure class="relative w-full mb-4 ">
                        <div class="w-full aspect-square bg-gray-50">
                            <x-lazy-image :src="asset($__photoItems->get(4)->cover ?: '/img/og-default.png')" alt="" contract="news_card_square" class="w-full h-full " />
                        </div>
                        <figcaption class="relative w-11/12 h-auto -mt-12 p-4 pl-0 md:min-h-30 bg-white">
                            <div class="font-unna font-bold text-xl 2xl:text-2xl text-ellipsis mb-2 md:min-h-[64px]">
                                <span class="line-clamp-2">{{ $__photoItems->get(4)->title }}</span>
                            </div>
                        </figcaption>
                    </figure>
                </a>
            </div>
        @endif
        @if ($__photoItems->isEmpty())
            <p class="most-read-empty">{{ __('ui.no_news') }}</p>
        @endif
    </div>
</div>

<a href="{{ \App\Support\Locale::url('/fotohaber') }}" data-type="link-type1" title="{{ __('ui.all') }}" class="w-full flex gap-2 my-2 items-center text-black bg-transparent col-span-2">
    <div class="flex-grow border-t border-gray-300 relative before:content-[''] before:absolute before:bottom-0 before:flex before:h-4 before:w-[1px] before:bg-gray-300"></div>
    <span class="ml-2">
        <span class="text-base mr-2">{{ __('ui.all') }}</span>
        <span class="bloomberght-icon-font-chevron-right text-sm"></span>
        <span class="bloomberght-icon-font-chevron-right text-sm"></span>
        <span class="bloomberght-icon-font-chevron-right text-sm"></span>
    </span>
</a>
