@extends('front.layouts.app')

@section('title', 'Altın Hesaplama | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', 'Gram altın, çeyrek altın, yarım altın, tam altın ve diğer altın çeşitleri arasında anlık dönüşüm yapın.')

@section('content')
    <div class="borsa-page altin-page altin-calc-page">
        <x-breadcrumb :items="[
            ['label' => 'Altın', 'url' => '/altin'],
            ['label' => 'Altın Hesaplama'],
        ]" />

        <h1 class="page-title font-unna font-bold">Altın Hesaplama</h1>

        <div class="borsa-layout">
            <div class="borsa-main">
                @include('altin.partials.calculator')

                <p class="altin-calc-desc">
                    Altın çevirici hesaplamamız ile gram altın, çeyrek altın, yarım altın ve tam altın arasındaki dönüşümleri
                    anlık olarak gerçekleştirebilirsiniz. Türkiye'nin en güncel altın fiyatlarını baz alan aracımız; 14 ayar ve
                    22 ayar altın seçenekleriyle birikim yapanlar ve altın yatırımcıları için hızlı ve güvenilir sonuçlar sunar.
                </p>

                @include('borsa.partials.related-news', ['items' => $relatedNews])

                @include('altin.partials.cme-note')
            </div>

            <aside class="borsa-aside">
                <x-ad-zone :zone="1257" :index="1" />

                <div class="aside-block">
                    <x-section-head title="Öne Çıkanlar" :href="null" />
                    <ul class="aside-list">
                        @foreach ($featured as $item)
                            <li><a href="{{ $item->url }}">{{ $item->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </aside>
        </div>
    </div>
@endsection