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

    {{-- Referans headline-cover: breadcrumb (font) + H1 + spot listesi + Google CTA --}}
    <div class="pb-2 overflow-hidden border-b flex justify-between">
        <x-breadcrumb :items="[
            ['label' => $news->category?->name ?? __('ui.news_title'), 'url' => $categoryUrl ?? \App\Support\Locale::url('/haberler')],
            ['label' => \Illuminate\Support\Str::limit($news->title, 60), 'url' => null],
        ]" />
    </div>

    <div class="py-4 flex flex-col gap-4 mb-5 border-b-2 border-gray-500">
        <h1 class="title font-unna font-bold text-3xl md:text-[44px] md:leading-[3.0rem]">{{ $news->title }}</h1>
        @if (! empty($spotItems))
            <ul class="article-spot-list">
                @foreach ($spotItems as $spot)
                    <li class="relative before:absolute before:top-2 before:left-0 before:w-2 before:h-2 pl-5 before:bg-black {{ $loop->last ? '' : 'mb-4' }}">
                        <h2 class="description text-base md:text-lg">{{ $spot }}</h2>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- Google News promo (spec §9) — referans yapısı --}}
    <a class="group w-full flex items-center justify-center gap-3 px-4 min-h-[54px] mb-4 border border-solid border-transparent bg-gray-800 hover:bg-white hover:border-gray-300 transition-colors duration-200"
       href="https://www.google.com/preferences/source?q=bloomberght.com" target="_blank" rel="noopener">
        <img src="https://geoim.bloomberght.com/cdn/google-news/google-news.svg" alt="" class="w-7 h-7 object-contain flex-none" decoding="async" width="28" height="28" />
        <span class="text-sm font-medium text-white group-hover:text-gray-900">{{ __('content.google_news_promo') }}</span>
    </a>

    <figure class="article-image">
        <x-lazy-image :src="asset($mainImage)" alt="" contract="og" class="article-img" :eager="$eager" />
    </figure>

    {{-- news-info: tarih + yazı büyütme / paylaş (referans yerleşim — görselin altında) --}}
    <div data-type="news-info">
        <div class="flex items-center justify-between gap-2 w-full border-b-2 border-gray-500 mb-2 pb-2">
            <div class="text-xs flex flex-col gap-1">
                <span>{{ $published?->translatedFormat('j F Y, H:i') }}</span>
                @if ($updated)
                    <span><b>{{ __('ui.updated') }} :</b> <time datetime="{{ $updated->toIso8601String() }}">{{ $updated->translatedFormat('j F Y, H:i') }}</time></span>
                @endif
            </div>
            <div class="flex flex-none gap-2">
                <ul class="flex items-center justify-center text-xl gap-2">
                    <li class="block p-2">
                        <button type="button" aria-label="{{ __('content.increase_font_size') }}" class="text-increase" data-text-increase>
                            <i class="bloomberght-icon-font-text-increase" aria-hidden="true"></i>
                        </button>
                    </li>
                    <li class="block p-2">
                        <button type="button" aria-label="Yazdır" class="print-button hover:text-red-700 transition" onclick="window.print()" title="Yazdır">
                            <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2m-4 0v5H8v-5m4 0h4"></path></svg>
                        </button>
                    </li>
                    <li class="relative group">
                        <i class="bloomberght-icon-font-share block p-2 cursor-pointer hover:text-red-700 transition" aria-hidden="true" title="Paylaş"></i>
                        <ul class="absolute right-0 bg-white shadow-md flex gap-4 p-3 border border-gray-200 rounded-sm hidden group-hover:flex text-black z-20 items-center" role="menu" aria-label="{{ __('ui.share') }}">
                            <li><a href="https://www.facebook.com/sharer/sharer.php?u={{ $u }}" target="_blank" rel="noopener noreferrer" title="Facebook"><i class="bloomberght-icon-font-facebook py-1" aria-hidden="true"></i></a></li>
                            <li><a href="https://twitter.com/intent/tweet?url={{ $u }}&text={{ $t }}" target="_blank" rel="noopener noreferrer" title="X (Twitter)"><i class="bloomberght-icon-font-twitter-x py-1" aria-hidden="true"></i></a></li>
                            <li><a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $u }}" target="_blank" rel="noopener noreferrer" title="LinkedIn"><i class="bloomberght-icon-font-linkedin py-1" aria-hidden="true"></i></a></li>
                            <li><a href="https://wa.me/?text={{ $t }}%20{{ $u }}" target="_blank" rel="noopener noreferrer" title="WhatsApp"><i class="bloomberght-icon-font-whatsapp py-1" aria-hidden="true"></i></a></li>
                            <li><a href="mailto:?subject={{ $t }}&body={{ $u }}" title="E-posta"><i class="bloomberght-icon-font-mail py-1" aria-hidden="true"></i></a></li>
                            <li>
                                <button type="button" aria-label="{{ __('ui.copy_short_link') }}" class="copy" data-copy-link="{{ $news->shortlink_url }}" title="Bağlantıyı Kopyala">
                                    <i class="bloomberght-icon-font-copy" aria-hidden="true"></i>
                                </button>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>

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

    @if (! empty($news->tags))
        <div class="article-tags">
            @foreach ($news->tags as $tag)
                <a href="{{ \App\Support\Locale::url('/haberleri/'.\Illuminate\Support\Str::slug($tag)) }}">{{ $tag }}</a>
            @endforeach
        </div>
    @endif
    @php
        $relatedNews = $related ?? \App\Models\News::with(['category', 'author'])
            ->where('category_id', $news->category_id)
            ->where('id', '!=', $news->id)
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->take(10)
            ->get();
    @endphp

    @if ($relatedNews->isNotEmpty())
        <section class="article-related mt-8 relative">
            <div class="flex items-center justify-between mb-4 pb-2 relative border-b border-black">
                <h2 class="font-unna font-bold text-3xl">{{ __('ui.related') }}</h2>
                <div class="flex items-center space-x-2">
                    <button type="button" class="related-prev w-7 h-7 rounded-full border border-gray-400 flex items-center justify-center hover:bg-black hover:text-white transition" aria-label="Önceki" data-related-prev>
                        <i class="bloomberght-icon-font-chevron-left text-xs"></i>
                    </button>
                    <button type="button" class="related-next w-7 h-7 rounded-full border border-gray-400 flex items-center justify-center hover:bg-black hover:text-white transition" aria-label="Sonraki" data-related-next>
                        <i class="bloomberght-icon-font-chevron-right text-xs"></i>
                    </button>
                </div>
                <span class="absolute left-0 -bottom-[1px] w-[230px] h-2.5 bg-black" aria-hidden="true"></span>
            </div>
            <div class="related-slider mt-4 overflow-hidden" data-related-slider>
                <div class="related-track flex gap-4 overflow-x-auto pb-2 scrollbar-none" data-related-track>
                    @foreach ($relatedNews as $item)
                        <article class="related-slide flex-none w-[288px]">
                            <a href="{{ $item->url }}" class="group block">
                                <figure class="related-figure mb-2 overflow-hidden bg-gray-50 aspect-video">
                                    <x-lazy-image :src="asset($item->image ?: '/img/og-default.png')" alt=""
                                                  contract="news_card" class="related-img w-full h-full object-cover group-hover:scale-105 transition duration-300" />
                                </figure>
                                @if ($item->category?->name)
                                    <span class="category text-xs font-semibold text-gray-600 uppercase mb-1 block">{{ $item->category->name }}</span>
                                @endif
                                <h3 class="related-slide-title font-unna font-bold text-base line-clamp-3 group-hover:text-[#bd1b2d] transition">{{ $item->title }}</h3>
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</article>