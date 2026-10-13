{{--
    Yazı sayfası (makale detay) — "En Çok Okunanlar" bloğu.

    REFERANS (bloomberght.com makale detay) DOM'u ile birebir:
      <div data-widget-type="most-read-slider" class="w-full py-4 hidden md:block">
        ayraç (230px kalın bant + ince çizgi) → başlık → <ul class="news-list">
        her kayıt: dev gri sıra numarası (text-[110px] text-gray-200) + başlık.
        Sekme YOK · görsel YOK · 2 kayıt · mobilde gizli.

    ANASAYFA mostRead'ı ile BİRLEŞTİRİLMEZ: ayrı bileşen, ayrı widget hook'u
    (`most-read-slider`), ortak `most-read-*` / `tab-menu` sınıfları ve JS
    bağlantısı yok. Anasayfa 3 sütunlu kart ızgarası + Bugün/Bu Hafta/Bu Ay
    sekmeleriyle kalır; buradaki blok sıralı numaralı listedir.
--}}
@props([
    'items' => [],
    'title' => null,
    'limit' => 2,
])

@php
    $title = $title ?? __('ui.most_read');
    $items = $items instanceof \Illuminate\Support\Collection ? $items : collect($items);
    $items = $items->values()->take($limit);
@endphp

<div data-widget-type="most-read-slider" class="w-full py-4 hidden md:block">
    <div class="mb-4 border-b border-black relative before:absolute before:top-0 before:bottom-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black"></div>
    <div class="font-unna font-bold text-3xl">{{ $title }}</div>

    <ul class="news-list">
        @foreach ($items as $rank => $item)
            <li class="w-full py-8 border-b border-gray-500 mb-2">
                <div class="relative w-full h-full pl-10 flex items-center justify-start">
                    <div class="absolute left-0 font-bold text-[110px] text-gray-200 leading-[.5] z-0">{{ $rank + 1 }}</div>
                    <a href="{{ $item->url }}" title="{{ $item->title }}" class="z-10">
                        <figure class="flex gap-2">
                            <figcaption class="w-full flex flex-col justify-around">
                                <div class="font-unna font-bold text-xl text-ellipsis line-clamp-3 leading-6 mb-1">{{ $item->title }}</div>
                            </figcaption>
                        </figure>
                    </a>
                </div>
            </li>
        @endforeach
    </ul>
</div>