@php
    /** @var \App\Models\Advertisement $ad */
@endphp
@if (! empty($ad))
    <div class="ad-banner">
        @if (filled($ad->code))
            {!! $ad->code !!}
        @elseif ($ad->imageUrl())
            <a href="{{ $ad->link_url ?: '#' }}" class="ad-slot" target="_blank" rel="sponsored noopener">
                <img src="{{ $ad->imageUrl() }}" alt="{{ $ad->title }}" class="ad-image" loading="lazy">
            </a>
        @endif
    </div>
@endif
