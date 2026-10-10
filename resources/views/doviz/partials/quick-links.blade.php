{{-- Döviz alt-hızlı bağlantıları (spec §15.4: Dolar|Euro|Sterlin|Frank|Pariteler) --}}
<nav class="borsa-quick-links doviz-quick-links" aria-label="Döviz hızlı bağlantılar">
    @foreach ($quickLinks as $link)
        <a href="{{ $link['url'] }}">{{ $link['label'] }}</a>
    @endforeach
</nav>

@include('borsa.partials.updated')