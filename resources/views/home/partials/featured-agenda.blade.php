{{-- Referans marketAgenda (ölçüldü): başlık + grid-cols-2.
         Sol sütun: type5 (başlıklı, <h2>Öne Çıkanlar</h2>) + type2 (mb-4)
         Sağ sütun: type1 + type1 + type5 (başlıksız)
         Ardından link-type1. --}}
@php
    $lead = $items[0] ?? null;
    $rows = collect($items)->slice(1, 5)->values();
@endphp
<section class="featured-agenda" data-widget-type="marketAgenda" id="home-featured-office">
    <div class="mb-4 border-b border-black relative before:absolute before:top-0 before:bottom-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black"></div>

    <div class="grid grid-cols-1 md:grid-cols-2 md:gap-4">
        <div class="w-full flex flex-col justify-between">
            @if ($lead)
                <x-news-card :news="$lead" type="type5" :title="__('ui.featured')" :href="\App\Support\Locale::url('/haberler')" />
            @endif
            @if ($rows->get(0))
                <x-news-card :news="$rows->get(0)" type="type2" wrap-class="mb-4" />
            @endif
        </div>
        <div class="w-full flex flex-col justify-between">
            @if ($rows->get(1))
                <x-news-card :news="$rows->get(1)" type="type1" />
            @endif
            @if ($rows->get(2))
                <x-news-card :news="$rows->get(2)" type="type1" />
            @endif
            @if ($rows->get(3))
                <x-news-card :news="$rows->get(3)" type="type5" />
            @endif
        </div>
    </div>

    <a href="{{ \App\Support\Locale::url('/haberler') }}" data-type="link-type1" title="{{ __('ui.all') }}" class="w-full flex gap-2 my-2 items-center text-black bg-white">
        <div class="flex-grow border-t border-gray-300 relative before:content-[''] before:absolute before:bottom-0 before:flex before:h-4 before:w-[1px] before:bg-gray-300"></div>
        <span class="ml-2">
            <span class="text-base mr-2">{{ __('ui.all') }}</span>
            <span class="bloomberght-icon-font-chevron-right text-sm"></span>
            <span class="bloomberght-icon-font-chevron-right text-sm"></span>
            <span class="bloomberght-icon-font-chevron-right text-sm"></span>
        </span>
    </a>
</section>
