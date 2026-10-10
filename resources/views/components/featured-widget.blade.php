@props([
    'items',
    'title' => null,
    'href' => null,
    'variant' => 'grid',
    'columns' => 3,
    'showSpot' => false,
    'class' => '',
    'id' => null,
])

@php
    $title = $title ?? __('ui.featured');
    $isSidebar = $variant === 'sidebar';
@endphp

{{-- ÖNE ÇIKANLAR widget'ı (spec §7 modül 5/10, §9 aside) — kare 640x640 + kategori etiketi + TÜMÜ --}}
<section class="featured-widget featured-widget-{{ $variant }} {{ $class }}"
         @if ($id) id="{{ $id }}" @endif
         @if ($isSidebar) data-widget-type="headlinesOfTheDay" @endif>
    <x-section-head :title="$title" :href="$href" />

    @if ($isSidebar)
        <div class="featured-sidebar">
            @foreach ($items as $i => $item)
                <x-news-card :news="$item" :type="$i < 2 ? 'type1' : 'type2'" :showSpot="$i === 0" />
            @endforeach
        </div>
    @else
        <div class="news-card-grid cols-{{ $columns }}">
            @foreach ($items as $item)
                <x-news-card :news="$item" type="type2" :showSpot="$showSpot" contract="news_card_square" />
            @endforeach
        </div>
    @endif
</section>