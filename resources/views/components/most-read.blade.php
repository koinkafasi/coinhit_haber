@props([
    'tabs',
    'title' => null,
    'href' => null,
    'class' => '',
    'id' => null,
    'variant' => 'default',
])

@php
    $title = $title ?? __('ui.most_read');
    $tabLabels = [
        'today' => __('ui.today'),
        'week' => __('ui.this_week'),
        'month' => __('ui.this_month'),
    ];
    // Ana sayfa varyantı — Bloomberg referansı mostRead DOM'u ile birebir.
    // Referans sekmeler: tab-today (aktif) | tab-thisWeek | tab-thisMonth.
    $homePeriods = [
        'today'      => ['tab' => 'tab-today',      'label' => __('ui.today'),      'active' => true],
        'week'       => ['tab' => 'tab-thisWeek',   'label' => __('ui.this_week'),  'active' => false],
        'month'      => ['tab' => 'tab-thisMonth',  'label' => __('ui.this_month'), 'active' => false],
    ];
@endphp

@if ($variant === 'home')
    {{-- CH-37: Ana sayfa En Çok Okunanlar — 3 sütunlu kart ızgarası (referans mostRead, birebir):
         h2 + sağ üstte Bugün | Bu Hafta | Bu Ay pill'i; her sekmede type5+type2 kart çiftleri,
         ortadaki sütun flex-col-reverse. --}}
    <section data-section="mostRead" class="grid grid-cols-12 gap-4 mb-4 md:mb-10 {{ $class }}"
             @if ($id) id="{{ $id }}" @endif>
        <div class="bht-grid col-span-12 md:col-span-12 row-span-1" dataType="mostRead">
            <div data-widget-type="mostRead" class="relative">
                <div class="mb-4 border-b border-black relative before:absolute before:top-0 before:bottom-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black"></div>
                <h2 class="font-unna font-bold text-3xl mb-4">{{ $title }}</h2>
                <div class="tab-menu">
                    <div class="tab-head mb-4 md:my-4 cursor-pointer border rounded-3xl md:absolute top-0 right-0 inline-block bg-white text-special-dark-maroon">
                        @foreach ($homePeriods as $__period => $__cfg)
                            <button type="button"
                                    class="tab-button inline-block font-unna font-bold px-4 py-2 {{ $loop->last ? '' : 'mr-2' }} rounded-3xl {{ $__cfg['active'] ? 'bg-gray-200 is-active' : '' }}"
                                    data-tab="{{ $__cfg['tab'] }}"
                                    aria-selected="{{ $__cfg['active'] ? 'true' : 'false' }}">{{ $__cfg['label'] }}</button>
                        @endforeach
                    </div>
                    <div class="tab-body">
                        @foreach ($homePeriods as $__period => $__cfg)
                            @php
                                $__items = $tabs[$__period] ?? collect([]);
                                $__items = $__items instanceof \Illuminate\Support\Collection ? $__items : collect($__items);
                                $__items = $__items->values();
                            @endphp
                            <div class="tab-content {{ $__cfg['active'] ? '' : 'hidden' }}" data-id="{{ $__cfg['tab'] }}">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    {{-- Referans: 1. sütun flex-col (type5 üstte + type2 altta) --}}
                                    <div class="flex flex-col">
                                        @if ($__items->get(0))
                                            <x-news-card :news="$__items->get(0)" type="type5" contract="news_card_square" />
                                        @endif
                                        @if ($__items->get(1))
                                            <x-news-card :news="$__items->get(1)" type="type2" contract="news_card_square" />
                                        @endif
                                    </div>
                                    {{-- Referans: 2. sütun flex-col-reverse (type2 üstte + type5 altta) --}}
                                    <div class="flex flex-col-reverse">
                                        @if ($__items->get(3))
                                            <x-news-card :news="$__items->get(3)" type="type5" contract="news_card_square" />
                                        @endif
                                        @if ($__items->get(2))
                                            <x-news-card :news="$__items->get(2)" type="type2" contract="news_card_square" />
                                        @endif
                                    </div>
                                    {{-- Referans: 3. sütun flex-col (type5 üstte + type2 altta) --}}
                                    <div class="flex flex-col">
                                        @if ($__items->get(4))
                                            <x-news-card :news="$__items->get(4)" type="type5" contract="news_card_square" />
                                        @endif
                                        @if ($__items->get(5))
                                            <x-news-card :news="$__items->get(5)" type="type2" contract="news_card_square" />
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@else
    {{-- Global/kenar çubuğu varyantı — referans mostRead widget'ı (Bugün | Bu Hafta | Bu Ay pill'leri
         + sıralı liste). data-widget-type + most-read-tabhead/most-read-panels site.js'e bağlanır. --}}
    @php($first = true)
    <section class="home-most-read most-read-widget most-read-{{ $variant }} {{ $class }}"
             @if ($id) id="{{ $id }}" @endif
             data-widget-type="mostRead">
        <div class="mb-4 border-b border-black relative before:absolute before:top-0 before:bottom-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black"></div>
        <h2 class="font-unna font-bold text-3xl mb-4">{{ $title }}</h2>

        <div class="most-read-tabs tab-menu" role="tablist">
            <div class="tab-head mb-4 md:my-4 cursor-pointer border rounded-3xl md:absolute top-0 right-0 inline-block bg-white text-special-dark-maroon most-read-tabhead">
                @foreach ($tabs as $period => $items)
                    <button type="button" role="tab"
                            class="tab-button most-read-tab inline-block font-unna font-bold px-4 py-2 mr-2 rounded-3xl {{ $first ? 'bg-gray-200' : '' }}"
                            data-tab="tab-{{ $period }}"
                            aria-selected="{{ $first ? 'true' : 'false' }}">{{ $tabLabels[$period] ?? $period }}</button>
                    @php($first = false)
                @endforeach
            </div>

            @php($first = true)
            <div class="tab-body most-read-panels">
                @foreach ($tabs as $period => $items)
                    <div class="tab-content most-read-panel {{ $first ? '' : 'hidden' }}" data-id="tab-{{ $period }}">
                        <ol class="most-read-list" data-mostread-list>
                            @forelse ($items as $rank => $item)
                                <li>
                                    <a href="{{ $item->url }}">
                                        <span class="most-read-rank">{{ $rank + 1 }}</span>
                                        <figure class="most-read-figure">
                                            <x-lazy-image :src="asset($item->image ?: '/img/og-default.png')" alt=""
                                                          contract="news_card_square" class="most-read-img" />
                                        </figure>
                                        <span class="most-read-body">
                                            @if ($item->category?->name)
                                                <span class="category text-xs">{{ $item->category->name }}</span>
                                            @endif
                                            <span class="most-read-title font-unna font-bold line-clamp-2">{{ $item->title }}</span>
                                        </span>
                                    </a>
                                </li>
                            @empty
                                <li class="most-read-empty"><span class="most-read-title">{{ __('ui.no_news') }}</span></li>
                            @endforelse
                        </ol>
                    </div>
                    @php($first = false)
                @endforeach
            </div>
        </div>
    </section>
@endif
