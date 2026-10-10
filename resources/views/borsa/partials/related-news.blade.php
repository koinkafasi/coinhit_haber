{{-- İlgili Haberler bloğu (spec §8) --}}
@if (($items ?? collect())->isNotEmpty())
    <section class="borsa-related">
        <x-section-head :title="$title ?? 'İlgili Haberler'" href="/haberler" />
        <div class="news-card-grid cols-2">
            @foreach ($items as $item)
                <x-news-card :news="$item" type="type2" :showSpot="true" />
            @endforeach
        </div>
    </section>
@endif