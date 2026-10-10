@extends('front.layouts.app')

@section('title', __('ui.breaking_link').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('ui.breaking_link').' — '.($siteSettings['site_tagline'] ?? ''))
@section('canonical', url(\App\Support\Locale::url('/sondakika')))

@section('content')
    <div class="section-title bht-page-band">
        <h1 class="font-unna font-bold">{{ __('ui.breaking_link') }}</h1>
        <span class="breaking-live"><span class="live-dot" aria-hidden="true"></span>{{ __('ui.live') }}</span>
    </div>

    @php($groups = $items->getCollection()->groupBy(fn ($item) => optional($item->published_at)->toDateString()))

    <div class="breaking-groups">
        @forelse ($groups as $date => $group)
            <section class="breaking-group">
                <h2 class="breaking-group-date font-unna">
                    {{ $group->first()->published_at?->translatedFormat('j F Y, l') }}
                </h2>

                <ol class="breaking-list">
                    @foreach ($group as $item)
                        <li class="breaking-row">
                            <time class="breaking-time">{{ $item->published_at?->format('H:i') }}</time>
                            <div class="breaking-main">
                                <a class="breaking-title" href="{{ $item->news?->url ?? '#' }}">{{ \Illuminate\Support\Str::upper($item->title) }}</a>
                                @include('partials.breaking-share', ['item' => $item])
                            </div>
                        </li>
                    @endforeach
                </ol>
            </section>
        @empty
            <p class="search-empty">{{ __('ui.no_breaking') }}</p>
        @endforelse
    </div>

    @include('borsa.partials.pagination', ['paginator' => $items])

    <div class="breaking-ad">
        <x-ad-zone :zone="1258" :index="1" />
    </div>
@endsection