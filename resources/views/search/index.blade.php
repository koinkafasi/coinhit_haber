@extends('front.layouts.app')

@section('title', __('ui.search_title').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('ui.search_title').': '.$query)
@section('robots', 'noindex,follow')

@section('content')
    <x-breadcrumb :items="[['label' => __('ui.search_heading'), 'url' => \App\Support\Locale::url('/arama')]]" />

    <div class="section-title">
        <h1>{{ __('ui.search_heading') }}</h1>
    </div>

    @if ($query === '')
        <p class="search-empty">{{ __('ui.search_prompt') }}</p>
    @else
        <p class="search-summary">
            <strong>&ldquo;{{ $query }}&rdquo;</strong> {{ __('ui.search_results', ['count' => $results->total()]) }}
        </p>

        <div class="list-grid">
            @forelse ($results as $item)
                <article class="news-card">
                    <a href="{{ $item->url }}">
                        <x-lazy-image :src="asset($item->image)" alt="" contract="news_card" class="card-img img-contract" />
                        <div class="card-body">
                            <span class="category-tag">{{ $item->category?->name }}</span>
                            <h3>{{ $item->title }}</h3>
                            <p class="card-time">{{ $item->published_at?->format('d.m.Y H:i') }}</p>
                        </div>
                    </a>
                </article>
            @empty
                <p class="search-empty">{{ __('ui.no_results') }}</p>
            @endforelse
        </div>

        <div class="pagination">
            {{ $results->links() }}
        </div>
    @endif
@endsection