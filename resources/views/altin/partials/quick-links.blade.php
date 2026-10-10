{{-- Altın alt-hızlı bağlantıları (spec §15.5: Ons|Gram|Çeyrek|Cumhuriyet|Ziynet|14 Ayar|22 Ayar|Diğer Emtia) --}}
<nav class="borsa-quick-links altin-quick-links" aria-label="Altın hızlı bağlantılar">
    @foreach ($quickLinks as $link)
        <a href="{{ \App\Support\Locale::url($link['url']) }}">{{ $link['label'] }}</a>
    @endforeach
</nav>

@include('borsa.partials.updated')