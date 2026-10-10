@extends('front.layouts.app')

@section('title', $news->title.' | '.($news->category?->name ?? __('ui.news_singular')).' '.__('ui.news_suffix'))
@section('description', strip_tags($news->spot ?? ''))
@section('og_type', 'article')
@section('og_title', $news->title)
@section('og_description', strip_tags($news->spot ?? ''))
@section('og_image', url($news->image_og ?: $news->image ?: '/img/og-default.png'))
@section('canonical', url($news->url))

@section('content')
    <x-breadcrumb :items="[
        ['label' => $news->category?->name ?? __('ui.news_title'), 'url' => \App\Support\Locale::url('/haberler')],
        ['label' => $news->title, 'url' => null],
    ]" />

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
                <a href="{{ \App\Support\Locale::route('authors.index') }}">{{ $news->author->name }}</a> · {{ $news->author->title }}
            </p>
        @endif

        <div class="article-image">
            <img src="{{ asset($news->image) }}" alt="">
        </div>

        <p class="article-meta">
            {{ $news->published_at?->translatedFormat('j F Y, H:i') }}
            @if ($news->modified_at)
                · {{ __('ui.updated') }}: <time>{{ $news->modified_at->translatedFormat('H:i') }}</time>
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
            <div class="section-title"><h2>{{ __('ui.related') }}</h2></div>
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
