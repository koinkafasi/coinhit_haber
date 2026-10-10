@extends('front.layouts.app')

@section('title', $news->title.' | '.($news->category?->name ?? 'Haber').' haberleri')
@section('description', strip_tags($news->spot ?? ''))
@section('head')
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $news->title }}">
    <meta property="og:description" content="{{ strip_tags($news->spot ?? '') }}">
    <meta property="og:url" content="{{ url($news->url) }}">
    <meta property="og:image" content="{{ url($news->image_og ?: $news->image ?: '/img/og-default.png') }}">
    <meta property="og:locale" content="{{ $siteSettings['og_locale'] ?? 'tr_TR' }}">
@endsection

@section('content')
    <nav class="breadcrumb">
        <a href="{{ route('home') }}">Bloomberg HT</a>
        <span>&rsaquo;</span>
        <span>{{ $news->category?->name }}</span>
    </nav>

    <article class="article">
        <span class="category-tag">{{ $news->category?->name }}</span>
        <h1 class="article-title">{{ $news->title }}</h1>

        @if ($news->spot)
            <div class="article-spot">
                {!! nl2br(e($news->spot)) !!}
            </div>
        @endif

        @if ($news->author)
            <p class="article-author">
                <a href="{{ route('authors.index') }}">{{ $news->author->name }}</a> · {{ $news->author->title }}
            </p>
        @endif

        <div class="article-image">
            <img src="{{ asset($news->image) }}" alt="">
        </div>

        <p class="article-meta">
            {{ $news->published_at?->translatedFormat('j F Y, H:i') }}
            @if ($news->modified_at)
                · Güncelleme: <time>{{ $news->modified_at->translatedFormat('H:i') }}</time>
            @endif
        </p>

        <div class="article-body">
            {!! nl2br(e($news->body)) !!}
        </div>

        @if (! empty($news->tags))
            <div class="article-tags">
                @foreach ($news->tags as $tag)
                    <span>{{ $tag }}</span>
                @endforeach
            </div>
        @endif
    </article>

    @if ($related->isNotEmpty())
        <section class="related">
            <div class="section-title"><h2>İlgili Haberler</h2></div>
            <div class="list-grid">
                @foreach ($related as $item)
                    <article class="news-card">
                        <a href="{{ $item->url }}">
                            <img src="{{ asset($item->image) }}" alt="" loading="lazy" class="card-img">
                            <div class="card-body">
                                <h3>{{ $item->title }}</h3>
                                <p class="card-time">{{ $item->published_at?->format('d.m.Y H:i') }}</p>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
@endsection
