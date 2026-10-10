@extends('front.layouts.app')

@section('title', $author->name.' — '.__('ui.opinion_archive').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', $author->bio ?: $author->name)
@section('canonical', url(\App\Http\Controllers\AuthorController::archiveUrl($author)))

@section('content')
    <x-breadcrumb :items="[
        ['label' => __('ui.opinions_title'), 'url' => \App\Support\Locale::url('/gorus')],
        ['label' => $author->name, 'url' => null],
    ]" />

    <header class="archive-author">
        <div class="author-avatar">
            @if ($author->avatar)
                <img src="{{ asset($author->avatar) }}" alt="{{ $author->name }}">
            @else
                {{ mb_substr($author->name, 0, 1) }}
            @endif
        </div>
        <div class="archive-author-body">
            <h1 class="font-unna font-bold">{{ $author->name }}</h1>
            @if ($author->title)
                <div class="archive-author-title">{{ $author->title }}</div>
            @endif
            @if ($author->bio)
                <p class="archive-author-bio">{{ $author->bio }}</p>
            @endif
        </div>
    </header>

    @include('partials.news-list', ['items' => $news, 'showSpot' => true, 'showAge' => true])

    @include('borsa.partials.pagination', ['paginator' => $news])
@endsection