@props([
    'title',
    'items',
    'href' => null,
    'columns' => 3,
    'type' => 'type2',
    'showSpot' => true,
    'class' => '',
    'id' => null,
])

{{--
    Reuters/piyasa haber modülü (spec §7, §8): başlık + kart grid + TÜMÜ.
    "Piyasa Haberleri", "Emtia Haberleri", "Sektör Haberleri", "Siyaset Haberleri"
    ve ana sayfa modüllerinde yeniden kullanılabilir (doğru kategori rotasına linkler).
--}}
<section class="category-news-module news-module {{ $class }}"
         @if ($id) id="{{ $id }}" @endif>
    <x-section-head :title="$title" :href="$href" />

    @if ($items->isNotEmpty())
        <div class="news-card-grid cols-{{ $columns }}">
            @foreach ($items as $item)
                <x-news-card :news="$item" :type="$type" :showSpot="$showSpot" />
            @endforeach
        </div>
    @else
        <p class="news-module-empty">{{ __('ui.no_news') }}</p>
    @endif
</section>