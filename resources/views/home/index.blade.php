@extends('front.layouts.app')

@section('title', ($siteSettings['site_name'] ?? 'Bloomberg HT').' | Türkiye\'nin Ekonomi Platformu')

@section('content')
    <section class="hero">
        @if ($featured->isNotEmpty())
            @php($lead = $featured->first())
            <article class="hero-lead">
                <a href="{{ $lead->url }}">
                    <x-lazy-image :src="asset($lead->image)" alt="" contract="news_card" class="hero-img img-contract" eager />
                    <div class="hero-body">
                        <span class="category-tag">{{ $lead->category?->name }}</span>
                        <h1>{{ $lead->title }}</h1>
                        <p class="spot">{{ $lead->spot }}</p>
                    </div>
                </a>
            </article>
            <div class="hero-side">
                @foreach ($featured->slice(1, 2) as $item)
                    <article class="hero-side-item">
                        <a href="{{ $item->url }}">
                            <x-lazy-image :src="asset($item->image)" alt="" contract="news_card" class="img-contract" />
                            <h2>{{ $item->title }}</h2>
                        </a>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <section class="home-grid">
        <div class="home-main">
            <div class="section-title">
                <h2>Son Haberler</h2>
                <a href="{{ route('news.index') }}">TÜMÜ &raquo;</a>
            </div>
            @forelse ($latestNews as $item)
                <article class="news-card">
                    <a href="{{ $item->url }}">
                        <x-lazy-image :src="asset($item->image)" alt="" contract="news_card" class="card-img img-contract" />
                        <div class="card-body">
                            <span class="category-tag">{{ $item->category?->name }}</span>
                            <h3>{{ $item->title }}</h3>
                            <p class="card-time">
                                {{ $item->published_at?->format('d.m.Y H:i') }}
                                @if ($item->is_breaking)
                                    <span class="breaking-badge">SON DAKİKA</span>
                                @endif
                            </p>
                        </div>
                    </a>
                </article>
            @empty
                <p>Henüz haber bulunmuyor. php artisan db:seed çalıştırın.</p>
            @endforelse
        </div>

        <aside class="home-aside">
            <div class="aside-block">
                <div class="section-title"><h2>Son Dakika</h2></div>
                <ol class="aside-list">
                    @forelse ($latestBreaking as $breaking)
                        <li>
                            <a href="{{ $breaking->news?->url ?? '#' }}">
                                <time>{{ $breaking->published_at?->format('H:i') }}</time>
                                {{ $breaking->title }}
                            </a>
                        </li>
                    @empty
                        <li>Son dakika haberi yok.</li>
                    @endforelse
                </ol>
            </div>

            <div class="aside-block">
                <div class="section-title"><h2>Kategoriler</h2></div>
                <ul class="category-list">
                    @foreach ($navCategories as $category)
                        <li>{{ $category->name }}</li>
                    @endforeach
                </ul>
            </div>
        </aside>
    </section>
@endsection
