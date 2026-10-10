@extends('front.layouts.app')

@section('title', ($pageTitle ?? 'Faiz Bono').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', 'Tahvil, repo, eurobond ve uluslararası tahvil faiz oranları; TR 2/5/10 yıllık tahvil ve ABD/Almanya/İngiltere/Japonya getirileri.')

@section('content')
    @php
        $pageTitle = $pageTitle ?? 'Faiz Bono';
        $sonColumns = [
            ['label' => 'SON', 'field' => 'last', 'class' => 'lastPrice'],
            ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
        ];
        $bondUrl = fn ($q) => $q->instrument->bondUrl();
        $repoUrl = fn ($q) => $q->instrument->repoUrl();
        $eurobondUrl = fn ($q) => $q->instrument->eurobondUrl();
        $quickLinks = [
            ['label' => 'Tahvil', 'url' => '/faiz-bono#tahvil'],
            ['label' => 'Repo', 'url' => '/faiz-bono#repo'],
            ['label' => 'Eurobondlar', 'url' => '/faiz-bono#eurobondlar'],
            ['label' => 'Uluslararası Tahviller', 'url' => '/faiz-bono#uluslararasi-tahviller'],
        ];
    @endphp

    <div class="borsa-page faiz-bono-page">
        <h1 class="page-title font-unna font-bold">{{ $pageTitle }}</h1>

        <nav class="borsa-quick-links faiz-bono-quick-links" aria-label="Faiz bono hızlı bağlantılar">
            @foreach ($quickLinks as $link)
                <a href="{{ \App\Support\Locale::url($link['url']) }}">{{ $link['label'] }}</a>
            @endforeach
        </nav>

        <div class="borsa-layout">
            <div class="borsa-main">
                <x-market-table title="Tahvil" firstLabel="TAHVİL" :quotes="$bondTr"
                    href="/faiz-bono" :columns="$sonColumns" :urlResolver="$bondUrl" id="tahvil" />

                <x-market-table title="Repo" firstLabel="REPO" :quotes="$repo"
                    href="/faiz-bono" :columns="$sonColumns" :urlResolver="$repoUrl" id="repo" />

                <x-market-table title="Eurobondlar" firstLabel="EUROBONDLAR" :quotes="$eurobonds"
                    href="/faiz-bono" :columns="$sonColumns" :urlResolver="$eurobondUrl" id="eurobondlar" />

                <x-market-table title="Uluslararası Tahviller" firstLabel="Faiz" :quotes="$intlBonds"
                    href="/faiz-bono" :columns="$sonColumns" :urlResolver="$bondUrl" id="uluslararasi-tahviller" />

                @include('borsa.partials.related-news', ['items' => $relatedNews])
            </div>

            <aside class="borsa-aside">
                <x-ad-zone :zone="1257" :index="1" />
                <x-ad-zone :zone="1258" :index="2" />
            </aside>
        </div>

        @include('home.partials.market-widget')

        @include('faiz-bono.partials.note')
    </div>
@endsection