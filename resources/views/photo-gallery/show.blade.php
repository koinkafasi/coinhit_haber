@extends('front.layouts.app')

@section('title', $gallery->title.' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', strip_tags((string) $gallery->summary))
@section('canonical', url($gallery->url))
@section('og_image', url($gallery->cover ?: '/img/og-default.png'))

@section('content')
    <x-breadcrumb :items="[
        ['label' => __('ui.photo_news'), 'url' => \App\Support\Locale::url('/fotohaber')],
        ['label' => $gallery->title, 'url' => null],
    ]" />

    <article class="gallery-detail">
        @if ($gallery->category?->name)
            <span class="category-tag">{{ $gallery->category->name }}</span>
        @endif
        <h1 class="article-title font-unna">{{ $gallery->title }}</h1>

        @if ($gallery->summary)
            <p class="gallery-summary">{{ $gallery->summary }}</p>
        @endif

        <div class="gallery-viewer" data-gallery-slideshow data-total="{{ $gallery->items->count() }}">
            <div class="gallery-slides">
                @foreach ($gallery->items as $i => $item)
                    <figure class="gallery-slide {{ $i === 0 ? 'is-active' : '' }}" data-slide="{{ $i }}">
                        <img src="{{ asset($item->image) }}" alt="{{ $item->caption }}" loading="{{ $i === 0 ? 'eager' : 'lazy' }}">
                        @if ($item->caption)
                            <figcaption class="gallery-caption">{{ $item->caption }}</figcaption>
                        @endif
                    </figure>
                @endforeach
            </div>

            @if ($gallery->items->count() > 1)
                <button type="button" class="gallery-nav gallery-prev" data-gallery-prev aria-label="{{ __('ui.previous') }}">&#8249;</button>
                <button type="button" class="gallery-nav gallery-next" data-gallery-next aria-label="{{ __('ui.next') }}">&#8250;</button>
                <span class="gallery-counter" data-gallery-counter>1 / {{ $gallery->items->count() }}</span>
            @endif
        </div>

        @if ($gallery->items->count() > 1)
            <div class="gallery-thumbs">
                @foreach ($gallery->items as $i => $item)
                    <button type="button" class="gallery-thumb {{ $i === 0 ? 'is-active' : '' }}" data-gallery-thumb="{{ $i }}" aria-label="{{ $i + 1 }}">
                        <img src="{{ asset($item->image) }}" alt="" loading="lazy">
                    </button>
                @endforeach
            </div>
        @endif

        <ul class="share-bar" aria-label="{{ __('ui.share') }}">
            <li><a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url($gallery->url)) }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><x-icon name="facebook" /></a></li>
            <li><a href="https://twitter.com/intent/tweet?url={{ urlencode(url($gallery->url)) }}&text={{ urlencode($gallery->title) }}" target="_blank" rel="noopener noreferrer" aria-label="X"><x-icon name="twitter-x" /></a></li>
            <li><a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url($gallery->url)) }}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><x-icon name="linkedin" /></a></li>
            <li><a href="https://wa.me/?text={{ urlencode($gallery->title) }}%20{{ urlencode(url($gallery->url)) }}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><x-icon name="whatsapp" /></a></li>
            <li><button type="button" class="share-copy" data-copy-link="{{ url($gallery->url) }}" aria-label="{{ __('ui.copy_link') }}"><x-icon name="copy" /></button></li>
        </ul>
    </article>

    @if ($related->isNotEmpty())
        <section class="related">
            <x-section-head :title="__('ui.photo_news')" href="/fotohaber" />
            <div class="gallery-grid">
                @foreach ($related as $item)
                    <article class="gallery-card">
                        <a href="{{ $item->url }}">
                            <figure>
                                <x-lazy-image :src="asset($item->cover ?: '/img/og-default.png')" alt="" contract="news_card_square" class="img-contract" />
                            </figure>
                            <div class="gallery-body">
                                <h4 class="font-unna font-bold line-clamp-2">{{ $item->title }}</h4>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
@endsection