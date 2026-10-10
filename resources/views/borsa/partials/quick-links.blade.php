{{-- Borsa alt-hızlı bağlantıları (spec §15.3) --}}
<nav class="borsa-quick-links" aria-label="{{ __('market.borsa_quick_links') }}">
    @foreach ($quickLinks as $link)
        <a href="{{ \App\Support\Locale::url($link['url']) }}">{{ $link['label'] }}</a>
    @endforeach
</nav>

@include('borsa.partials.updated')