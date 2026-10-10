@php
    $seriesSlug = $episode->program?->slug;
    $url = $seriesSlug
        ? \App\Support\Locale::url('/'.$seriesSlug.'/video/'.$episode->slug.'/'.$episode->id)
        : \App\Support\Locale::url('/video');
@endphp
<article class="video-card" data-news-id="{{ $episode->id }}">
    <a href="{{ $url }}">
        <figure class="video-thumb">
            <x-lazy-image :src="asset($episode->thumbnail ?: $episode->program?->thumbnail ?: '/img/placeholders/placeholder-1.svg')"
                          alt="" contract="video_thumb" class="img-contract" />
            <span class="program-duration">{{ $episode->duration }}</span>
        </figure>
        <h3 class="video-title font-unna font-bold line-clamp-2">{{ $episode->display_title }}</h3>
        @if ($episode->program)
            <span class="video-series-name">{{ $episode->program->name }}</span>
        @endif
    </a>
</article>