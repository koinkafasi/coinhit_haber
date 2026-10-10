@extends('front.layouts.app')

@section('title', __('ui.breaking_link').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))

@section('content')
    <div class="section-title">
        <h1>{{ __('ui.breaking_link') }}</h1>
    </div>

    <ol class="breaking-list">
        @forelse ($items as $item)
            <li>
                <time>{{ $item->published_at?->format('H:i') }}</time>
                <span class="breaking-date">{{ $item->published_at?->translatedFormat('j F Y, l') }}</span>
                <a href="{{ $item->news?->url ?? '#' }}">{{ $item->title }}</a>
            </li>
        @empty
            <li>{{ __('ui.no_breaking') }}</li>
        @endforelse
    </ol>
@endsection
