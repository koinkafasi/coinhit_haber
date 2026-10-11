@props([
    'title',
    'items',
    'href' => null,
    'columns' => 3,
    'type' => 'type2',
    'showSpot' => true,
    'class' => '',
    'id' => null,
    'level' => 'h2',
])

{{--
    Reuters/piyasa haber modülü (spec §7, §8): başlık + kart grid + TÜMÜ.
    "Piyasa Haberleri", "Emtia Haberleri", "Sektör Haberleri", "Siyaset Haberleri"
    ve ana sayfa modüllerinde yeniden kullanılabilir (doğru kategori rotasına linkler).
--}}
<section class="category-news-module news-module {{ $class }}"
         @if ($id) id="{{ $id }}" @endif>
    <div class="mb-4 border-b border-black relative before:absolute before:top-0 before:bottom-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black"></div>
    <a href="{{ $href }}" title="{{ $title }}" class="block font-unna font-bold text-3xl mb-2 z-10">{{ $title }}</a>

    @if ($items->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 md:gap-4">
            <div class="w-full flex flex-col justify-between">
                @foreach ($items->take(2) as $item)
                    <x-news-card :news="$item" :type="$type" :showSpot="$showSpot" />
                @endforeach
            </div>
            <div class="w-full flex flex-col justify-between">
                @foreach ($items->slice(2) as $item)
                    <x-news-card :news="$item" :type="$type" :showSpot="$showSpot" />
                @endforeach
            </div>
        </div>
        @if ($href)
            <a href="{{ $href }}" data-type="link-type1" title="{{ __('ui.all') }}" class="w-full flex gap-2 my-2 items-center text-black bg-transparent col-span-2">
                <div class="flex-grow border-t border-gray-300 relative before:content-[''] before:absolute before:bottom-0 before:flex before:h-4 before:w-[1px] before:bg-gray-300"></div>
                <span class="ml-2">
                    <span class="text-base mr-2">{{ __('ui.all') }}</span>
                    <span class="bloomberght-icon-font-chevron-right text-sm"></span>
                    <span class="bloomberght-icon-font-chevron-right text-sm"></span>
                    <span class="bloomberght-icon-font-chevron-right text-sm"></span>
                </span>
            </a>
        @endif
    @else
        <p class="news-module-empty">{{ __('ui.no_news') }}</p>
    @endif
</section>