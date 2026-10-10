@extends('front.layouts.app')

@section('title', __('ui.opinions_title').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('ui.opinions_title').' — '.($siteSettings['site_tagline'] ?? ''))
@section('canonical', url(\App\Support\Locale::url('/gorus')))

@section('content')
    <div class="section-title bht-page-band">
        <h1 class="font-unna font-bold">{{ __('ui.opinions_title') }}</h1>
    </div>

    <div class="opinion-grid">
        @forelse ($authors as $author)
            <article class="opinion-card">
                <div class="opinion-avatar">
                    @if ($author->avatar)
                        <img src="{{ asset($author->avatar) }}" alt="{{ $author->name }}" loading="lazy">
                    @else
                        <span class="author-avatar">{{ mb_substr($author->name, 0, 1) }}</span>
                    @endif
                </div>
                <div class="opinion-body">
                    <a class="opinion-name font-unna font-bold" href="{{ \App\Http\Controllers\AuthorController::articleUrl($author) }}">{{ $author->name }}</a>
                    @if ($author->title)
                        <div class="opinion-corner">{{ $author->title }}</div>
                    @endif
                    @if ($author->bio)
                        <p class="opinion-summary">{{ $author->bio }}</p>
                    @endif

                    <ul class="opinion-latest">
                        @foreach ($author->getRelation('latestNews') as $item)
                            <li><a href="{{ $item->url }}">{{ $item->title }}</a></li>
                        @endforeach
                    </ul>

                    <a class="opinion-all" href="{{ \App\Http\Controllers\AuthorController::archiveUrl($author) }}">{{ __('ui.all_writings') }} &raquo;</a>
                </div>
            </article>
        @empty
            <p class="search-empty">{{ __('ui.no_authors') }}</p>
        @endforelse
    </div>
@endsection