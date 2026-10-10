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
         data-widget-type="most-read">
    <x-section-head :title="$title" :href="$href" />

    <div class="most-read-tabs tab-menu" role="tablist">
        @foreach ($tabs as $period => $items)
            <button type="button" role="tab"
                    class="tab-button most-read-tab font-unna font-bold {{ $first ? 'is-active' : '' }}"
                    data-tab="{{ $period }}"
                    aria-selected="{{ $first ? 'true' : 'false' }}">{{ $tabLabels[$period] ?? $period }}</button>
            @php($first = false)
        @endforeach
    </div>

    @php($first = true)
    @foreach ($tabs as $period => $items)
        <ol class="most-read-list tab-content {{ $first ? '' : 'hidden' }}" data-id="{{ $period }}">
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
        @php($first = false)
    @endforeach
</section>