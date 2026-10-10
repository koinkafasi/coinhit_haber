{{-- Yatırım fonları alt-tool nav (3 buton) — spec §8, §15.8 --}}
<nav class="fund-tool-nav" aria-label="Yatırım fonları araçları">
    @foreach ($toolLinks as $link)
        <a class="fund-tool-link {{ ! empty($link['anchor']) ? 'is-anchor' : '' }}"
           href="{{ \App\Support\Locale::url($link['url']) }}">
            <span class="bloomberght-icon-font bloomberght-icon-font-icon-compare" aria-hidden="true">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path d="M10 3h4v2h-4zM12 2a1 1 0 011 1v1h1.5a1 1 0 011 1v14a1 1 0 01-1 1h-9a1 1 0 01-1-1V5a1 1 0 011-1H7V3a1 1 0 011-1zm1 2v1h-2V4zm-2 3v12h6V7z"/></svg>
            </span>
            {{ $link['label'] }}
        </a>
    @endforeach
</nav>