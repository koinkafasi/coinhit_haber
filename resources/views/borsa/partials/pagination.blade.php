{{-- Sayfalama: 1 | 2 | 3 … | Sonraki (spec §15.3, §17.3) --}}
@php
    $last = $paginator->lastPage();
    $current = $paginator->currentPage();
    $window = 5;
    $start = max(1, min($current - 2, $last - $window + 1));
    $end = min($last, $start + $window - 1);
@endphp

@if ($last > 1)
    <nav class="pagination flex w-full gap-2 text-sm" aria-label="Sayfalama">
        @for ($p = $start; $p <= $end; $p++)
            @if ($p === $current)
                <span class="page-btn is-active" aria-current="page">{{ $p }}</span>
            @else
                <a class="page-btn" href="{{ $paginator->url($p) }}">{{ $p }}</a>
            @endif
        @endfor

        @if ($paginator->hasMorePages())
            <a class="page-btn page-next" href="{{ $paginator->nextPageUrl() }}">
                Sonraki <x-icon name="chevron-right" />
            </a>
        @endif
    </nav>
@endif