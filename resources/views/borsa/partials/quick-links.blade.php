{{-- Borsa alt-hızlı bağlantıları (spec §15.3) --}}
<nav class="borsa-quick-links" aria-label="Borsa hızlı bağlantılar">
    @foreach ($quickLinks as $link)
        <a href="{{ $link['url'] }}">{{ $link['label'] }}</a>
    @endforeach
</nav>

@include('borsa.partials.updated')