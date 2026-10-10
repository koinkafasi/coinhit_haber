@extends('front.layouts.app')

@section('title', __('ui.news_title').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))

@section('content')
    <div class="section-title">
        <h1>{{ __('ui.news_title') }}</h1>
        <a href="{{ \App\Support\Locale::route('breaking.index') }}">{{ __('ui.breaking_link') }} &raquo;</a>
    </div>

    <div class="list-grid">
        @forelse ($news as $item)
            <article class="news-card">
                <a href="{{ $item->url }}">
                    <img src="{{ asset($item->image) }}" alt="" loading="lazy" class="card-img">
                    <div class="card-body">
                        <span class="category-tag">{{ $item->category?->name }}</span>
                        <h3>{{ $item->title }}</h3>
                        @if ($item->spot)
                            <p class="card-spot">{{ \Illuminate\Support\Str::limit(strip_tags($item->spot), 140) }}</p>
                        @endif
                        <p class="card-time">{{ $item->published_at?->format('d.m.Y H:i') }}</p>
                    </div>
                </a>
            </article>
        @empty
            <p>{{ __('ui.no_news') }}</p>
        @endforelse
    </div>

    <div class="pagination">
        {{ $news->links() }}
    </div>
@endsection
