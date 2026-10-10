@extends('front.layouts.app')

@section('title', 'Yazarlar | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))

@section('content')
    <div class="section-title">
        <h1>Görüş &amp; Yazarlar</h1>
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
                    <span class="category-tag">{{ $author->news_count }} yazı</span>
                </div>
            </article>
        @empty
            <p>Yazar bulunmuyor.</p>
        @endforelse
    </div>
@endsection
