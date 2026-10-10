@php
    $__locale = app()->getLocale();
    $__route = request()->route()?->getName();
    $__isNews = in_array($__route, ['news.show', 'en.news.show'], true);
    $__news = ($__isNews && isset($news) && $news instanceof \App\Models\News) ? $news : null;
@endphp
<div class="bht-lang-switcher" data-lang-switcher aria-label="{{ __('ui.switch_language') }}" role="navigation">
    @foreach (config('locales.supported', []) as $__code => $__meta)
        @if (! empty($__meta['enabled']))
            @php
                if ($__news) {
                    $__slug = $__news->translation($__code)?->slug ?? $__news->getRawOriginal('slug');
                    $__href = \App\Support\Locale::urlFor($__code, '/'.$__slug.'-'.$__news->news_id);
                } else {
                    $__href = \App\Support\Locale::switchUrl($__code);
                }
            @endphp
            <a class="lang-link {{ $__locale === $__code ? 'is-active' : '' }}"
               href="{{ $__href }}"
               hreflang="{{ $__code }}"
               title="{{ $__meta['native'] }}"
               @if ($__locale === $__code) aria-current="true" @endif>{{ strtoupper($__code) }}</a>
        @endif
    @endforeach
</div>