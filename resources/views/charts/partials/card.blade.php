{{--
    Grafik arşivi kartı (spec §8 /grafik-arsivi, §15.14).
    16:9 görsel + kategori etiketi + başlık; kart `/grafik-arsivi/{slug}-{id}` linki.
--}}
@props(['chart'])

<article class="chart-card news-card-item" data-type="news-card-type3" data-news-id="{{ $chart->chart_id }}">
    <a href="{{ $chart->url }}" title="{{ $chart->title }}">
        <figure class="chart-figure">
            <x-lazy-image :src="asset($chart->image ?: '/img/og-default.png')"
                          alt="{{ $chart->title }}" contract="news_card" class="chart-img" />
        </figure>
        <figcaption class="chart-caption">
            <div class="category text-xs">{{ $chart->category }}</div>
            <div class="font-unna font-bold chart-title line-clamp-2">{{ $chart->title }}</div>
        </figcaption>
    </a>
</article>