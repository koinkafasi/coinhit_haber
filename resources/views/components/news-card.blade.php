@props([
    'news',
    'type' => 'type1',
    'eager' => false,
    'showSpot' => false,
    'contract' => 'news_card',
    'title' => null,
    'href' => null,
    'wrapClass' => null,
    // type5 figcaption'unda kategori satırı kanal sayfalarında referansta
    // görünmez (type1/2/4'te görünür) — sayfa bazlı kapatılır.
    'showCategory' => true,
])

@php
    // KapHaberi gibi kategori iliskisi olmayan modellerde de calisir.
    $category = data_get($news, 'category.name');
    // Referans: type5 bolum basligini kendi icinde tasir
    // ("Piyasa Haberleri" / "Siyaset Haberleri" vb. link ile).
    $sectionTitle = $title;
    $sectionHref = $href ?? $news->url;
    $showCat = $showCategory ? $category : null;
    // Referans (ölçüldü) kart dis siniflari:
    //   type5 -> ""            (cizgiyi figure tasir: border-b mb-2 pb-2)
    //   diger -> "border-b mb-2 pb-2"
    // Sutun son kartinda referans "md:mb-4" kullanir (CSS last-child kurali).
    $classes = $wrapClass !== null
        ? $wrapClass
        : ($type === 'type5' ? '' : 'border-b mb-2 pb-2');
@endphp

<div class="{{ $classes }}" data-type="news-card-{{ $type }}" data-news-id="{{ $news->news_id }}">
    @if ($type === 'type5' && $sectionTitle)
        <a href="{{ $sectionHref }}" title="{{ $sectionTitle }}" class="block font-unna font-bold text-3xl mb-2 z-10">{{ $sectionTitle }}</a>
    @endif
    <a href="{{ $news->url }}" title="{{ $news->title }}" class="{{ $type === 'type5' ? 'block' : '' }}">
        @if ($type === 'type5')
            <figure class="relative w-full border-b mb-2 pb-2">
                <div class="w-full aspect-square bg-gray-50">
                    <x-lazy-image :src="asset($news->image ?: '/img/og-default.png')" alt="" :contract="$contract" class="w-full h-full " :eager="$eager" />
                </div>
                <figcaption class="relative w-11/12 h-auto -mt-12 p-4 pl-0 md:min-h-30 bg-white">
            @if ($showCat)
                <div class="category text-xs">{{ $category }}</div>
            @endif
                    <div class="font-unna font-bold text-xl 2xl:text-2xl text-ellipsis mb-2 md:min-h-[64px] ">
                        <span class="line-clamp-2">{{ $news->title }}</span>
                    </div>
                    @if ($showSpot && $news->spot)
                        <div class=" text-base line-clamp-3">{{ \Illuminate\Support\Str::limit(strip_tags($news->spot), 170) }}</div>
                    @endif
                </figcaption>
            </figure>
        @elseif ($type === 'type2')
            <figure class="flex gap-2 ">
                <div class="w-4/12 h-full aspect-square bg-gray-50 max-w-40">
                    <x-lazy-image :src="asset($news->image ?: '/img/og-default.png')" alt="" :contract="$contract" class="w-full h-full object-fill " :eager="$eager" />
                </div>
                <figcaption class="w-8/12 flex flex-col justify-center space-y-2 ">
                    @if ($showCat)
                        <div class="category text-xs">{{ $category }}</div>
                    @endif
                    <div class="font-unna font-bold text-base 2xl:text-xl text-ellipsis line-clamp-3 ">{{ $news->title }}</div>
                    @if ($showSpot && $news->spot)
                        <div class=" text-base line-clamp-3">{{ \Illuminate\Support\Str::limit(strip_tags($news->spot), 170) }}</div>
                    @endif
                </figcaption>
            </figure>
        @elseif ($type === 'type3')
            {{-- Referans type3 (highlights/graphics): aspect-video kapak + baslik.
                 Bolum basligi referansta <h2 class="... mb-2 p-2 pt-4"> olarak
                 kartin icinde yer alir (link degildir). --}}
            @if ($sectionTitle)
                <h2 class="font-unna font-bold text-3xl mb-2 p-2 pt-4">{{ $sectionTitle }}</h2>
            @endif
            <figure class="px-2">
                <div class="w-full h-full aspect-video bg-gray-50 mb-4">
                    <x-lazy-image :src="asset($news->image ?: '/img/og-default.png')" alt="" :contract="$contract" class="w-full h-full " :eager="$eager" />
                </div>
                <figcaption class="w-full h-full flex flex-col justify-around">
                    @if ($showCat)
                        <div class="category text-xs">{{ $category }}</div>
                    @endif
                    <div class="font-unna font-bold text-base 2xl:text-xl text-ellipsis line-clamp-2 ">{{ $news->title }}</div>
                    @if ($showSpot && $news->spot)
                        <div class=" text-base line-clamp-3">{{ \Illuminate\Support\Str::limit(strip_tags($news->spot), 170) }}</div>
                    @endif
                </figcaption>
            </figure>
        @elseif ($type === 'type4')
            {{-- Referans type4 (kanal sayfalari): metin SOLDA (w-8/12), gorsel
                 SAGDA (w-4/12) — type2'nin yatay ayna karsiligi. --}}
            <figure class="flex gap-2 justify-between ">
                <figcaption class="w-8/12 flex flex-col justify-center">
                    @if ($showCat)
                        <div class="category text-xs">{{ $showCat }}</div>
                    @endif
                    <div class="font-unna font-bold text-base 2xl:text-xl text-ellipsis line-clamp-3 ">{{ $news->title }}</div>
                    @if ($showSpot && $news->spot)
                        <div class=" text-base line-clamp-3">{{ \Illuminate\Support\Str::limit(strip_tags($news->spot), 170) }}</div>
                    @endif
                </figcaption>
                <div class="w-4/12 h-full aspect-square bg-gray-50 max-w-40">
                    <x-lazy-image :src="asset($news->image ?: '/img/og-default.png')" alt="" :contract="$contract" class="w-full h-full object-fill " :eager="$eager" />
                </div>
            </figure>
        @else
            {{-- type1 (ve tip belirtilmemis): baslik varsa referansta dogrudan
                 <a class="block font-unna font-bold text-3xl mb-2 z-10"> — ic h2 yok. --}}
            @if ($sectionTitle)
                <a href="{{ $sectionHref }}" title="{{ $sectionTitle }}" class="block font-unna font-bold text-3xl mb-2 z-10">
                    {{ $sectionTitle }}
                </a>
            @endif
            {{-- Referans type1: <figure class=""> — ek px-2/padding yok. --}}
            <figure>
                <figcaption>
                    @if ($showCat)
                        <div class="category text-xs">{{ $category }}</div>
                    @endif
                    <div class="font-unna font-bold text-base 2xl:text-xl text-ellipsis line-clamp-2 ">{{ $news->title }}</div>
                    @if ($showSpot && $news->spot)
                        <div class=" text-base line-clamp-3">{{ \Illuminate\Support\Str::limit(strip_tags($news->spot), 170) }}</div>
                    @endif
                </figcaption>
            </figure>
        @endif
    </a>
</div>