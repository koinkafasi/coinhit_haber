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
    $first = true;
@endphp

{{-- EN ÇOK OKUNANLAR — Bugün | Bu Hafta | Bu Ay (spec §6, §7 modül 25; global widget) --}}
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
</section>