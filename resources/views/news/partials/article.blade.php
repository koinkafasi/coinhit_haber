{{--
    Makale gövdesi (spec §9 / §15.15): breadcrumb → H1 → spot → Google CTA →
    ana görsel → meta → gövde → paylaş ikon çubuğu. Sonsuz kaydırma parçası ve
    ana makale görünümü tarafından ortak kullanılır.
--}}
@php
    $isInfinite = $isInfinite ?? false;
    $eager = $eager ?? ! $isInfinite;
    $spotItems = $news->spotItems();
    $shareUrl = $news->url;
    $shareTitle = $news->title;
    $u = urlencode($shareUrl);
    $t = urlencode($shareTitle);
    $mainImage = $news->image ?: '/img/og-default.png';
    $blocks = $news->contentBlocks();
    $published = $news->published_at;
    $updated = $news->modified_at;
@endphp

<article class="article-detail {{ $isInfinite ? 'article-detail-infinite' : '' }}"
         data-id="{{ $news->news_id }}"
         data-it='{!! json_encode($news->infiniteData($index ?? 1), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}'
         data-infinite-article>

    <x-breadcrumb :items="[
        ['label' => $news->category?->name ?? __('ui.news_title'), 'url' => $categoryUrl ?? \App\Support\Locale::url('/haberler')],
        ['label' => \Illuminate\Support\Str::limit($news->title, 60), 'url' => null],
    ]" />

    <h1 class="article-title font-unna font-bold">{{ $news->title }}</h1>

    @if (! empty($spotItems))
        <ul class="article-spot-list">
            @foreach ($spotItems as $spot)
                <li class="relative"><h2 class="description">{{ $spot }}</h2></li>
            @endforeach
        </ul>
    @endif

    {{-- Google News promo (spec §9) --}}
    <a class="google-news-bar" href="https://www.google.com/preferences/source?q=bloomberght.com" target="_blank" rel="noopener">
        {{ __('content.google_news_promo') }}
    </a>

    <figure class="article-image">
        <x-lazy-image :src="asset($mainImage)" alt="" contract="og" class="article-img" :eager="$eager" />
    </figure>

    <p class="article-meta">
        <time datetime="{{ $published?->toIso8601String() }}">{{ $published?->translatedFormat('j F Y, H:i') }}</time>
        @if ($updated)
            <span class="article-updated">{{ __('ui.updated') }} : <time datetime="{{ $updated->toIso8601String() }}">{{ $updated->translatedFormat('j F Y, H:i') }}</time></span>
        @endif
    </p>

    <div class="article-wrapper mb-4 mt-5">
        @foreach ($blocks as $block)
            @if ($block['type'] === 'paragraph')
                <p>{!! $news->linkedParagraph($block['html']) !!}</p>
            @elseif ($block['type'] === 'image')
                <figure class="article-inline-image">
                    <img src="{{ asset($block['src']) }}" alt="{{ $block['alt'] ?? '' }}" loading="lazy" decoding="async" class="{{ $block['class'] ?? '' }}">
                </figure>
            @elseif ($block['type'] === 'flourish')
                <div class="flourish-embed flourish-chart" data-src="{{ $block['data_src'] }}"></div>
            @endif
        @endforeach
    </div>

    @once
        <script src="https://flourish.studio/resources/embed.js" async></script>
    @endonce

    {{-- Paylaş satırı: yazı boyutu + hover dropdown (spec §9, §17.3) --}}
    <div class="article-tools" data-article-tools>
        <button type="button" class="article-tool text-increase" data-text-increase aria-label="{{ __('content.increase_font_size') }}">
            <span class="ti-small" aria-hidden="true">A</span><span class="ti-large" aria-hidden="true">A</span>
        </button>

        <div class="share-dropdown group">
            <button type="button" class="article-tool share-trigger" aria-haspopup="true" aria-expanded="false">
                <x-icon name="share" /> <span>{{ __('ui.share') }}</span>
            </button>
            <ul class="share-menu" role="menu" aria-label="{{ __('ui.share') }}">
                <li><a href="https://www.facebook.com/sharer/sharer.php?u={{ $u }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><x-icon name="facebook" /></a></li>
                <li><a href="https://twitter.com/intent/tweet?url={{ $u }}&text={{ $t }}" target="_blank" rel="noopener noreferrer" aria-label="X"><x-icon name="twitter-x" /></a></li>
                <li><a href="https://nsosyal.com/share?url={{ $u }}" target="_blank" rel="noopener noreferrer" aria-label="nSosyal"><x-icon name="nsosyal" /></a></li>
                <li><a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $u }}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><x-icon name="linkedin" /></a></li>
                <li><a href="https://wa.me/?text={{ $t }}%20{{ $u }}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><x-icon name="whatsapp" /></a></li>
                <li><a href="mailto:?subject={{ $t }}&body={{ $u }}" aria-label="{{ __('content.email') }}"><x-icon name="mail" /></a></li>
                <li>
                    <button type="button" class="share-copy" data-copy-link="{{ $news->shortlink_url }}" aria-label="{{ __('ui.copy_short_link') }}">
                        <x-icon name="copy" /> <span class="shortlink-text">{{ $news->shortlink }}</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    @if (! empty($news->tags))
        <div class="article-tags">
            @foreach ($news->tags as $tag)
                <a href="{{ \App\Support\Locale::url('/haberleri/'.\Illuminate\Support\Str::slug($tag)) }}">{{ $tag }}</a>
            @endforeach
        </div>
    @endif
</article>