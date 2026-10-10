@extends('front.layouts.app')

@section('title', __('ui.authors_title').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))

@section('content')
    <div class="section-title">
        <h1>{{ __('ui.authors_title') }}</h1>
    </div>

    <div class="author-grid">
        @forelse ($authors as $author)
            <article class="author-card">
                <div class="author-avatar">
                    {{ mb_substr($author->name, 0, 1) }}
                </div>
                <div class="author-body">
                    <h3>{{ $author->name }}</h3>
                    <p class="author-title">{{ $author->title }}</p>
                    <p class="author-bio">{{ $author->bio }}</p>
                    <span class="category-tag">{{ $author->news_count }} {{ __('ui.article_count') }}</span>
                </div>
            </article>
        @empty
            <p>{{ __('ui.no_authors') }}</p>
        @endforelse
    </div>
@endsection
