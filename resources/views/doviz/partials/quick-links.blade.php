{{-- Döviz alt-hızlı bağlantıları (spec §15.4: Dolar|Euro|Sterlin|Frank|Pariteler) --}}
<nav class="borsa-quick-links doviz-quick-links" aria-label="{{ __('market.doviz_quick_links') }}">
    @foreach ($quickLinks as $link)
        <a href="{{ \App\Support\Locale::url($link['url']) }}">{{ $link['label'] }}</a>
    @endforeach
</nav>

@include('borsa.partials.updated')