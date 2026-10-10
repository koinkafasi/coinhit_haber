{{-- İlgili Etiketler (spec §8 — arama/etiket sayfaları). --}}
@if (($tags ?? collect())->isNotEmpty())
    <section class="related-tags">
        <x-section-head :title="__('ui.related_tags')" level="h2" />
        <div class="related-tags-chips">
            @foreach ($tags as $tag)
                <a class="chip" href="{{ \App\Support\Locale::url('/haberleri/'.$tag) }}">{{ $tag }}</a>
            @endforeach
        </div>
    </section>
@endif