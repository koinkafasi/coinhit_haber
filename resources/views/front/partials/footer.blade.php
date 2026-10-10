@php
    $settings = $siteSettings ?? [];

    // Footer 5 sütun iskeleti — tam bağlantı listesi CH-4'te birebir doldurulur (Bölüm 2.7).
    $columns = [
        ['title' => 'Kurumsal', 'links' => ['Businessweek', 'Canlı Yayın', 'Ekonomi Haberleri', 'Künye']],
        ['title' => 'Altın', 'links' => ['Altın Fiyatları', 'Gram Altın Fiyatı', 'Çeyrek Altın Fiyatı', 'Altın ONS Fiyatı']],
        ['title' => 'Döviz', 'links' => ['Döviz Kurları', 'Dolar TL', 'Euro TL', 'Kanada Doları']],
        ['title' => 'Piyasalar', 'links' => ['Canlı Borsa', 'BIST 100 Endeksi', 'Brent Petrol Fiyatı', 'Altın Hesaplama']],
        ['title' => 'Yasal', 'links' => ['Kullanım Koşulları', 'Aydınlatma Metni', 'Çerez Politikası', 'İletişim']],
    ];

    $social = ['twitter-x', 'facebook', 'instagram', 'youtube'];
    $badges = ['AppStore', 'GooglePlay', 'AppGallery'];
@endphp

<footer class="site-footer bht-footer">
    <div class="container">
        <div class="footer-top">
            <a class="logo" href="{{ route('home') }}" aria-label="{{ $settings['logo_alt'] ?? 'Bloomberg HT' }}">
                <img src="{{ asset($settings['logo_path'] ?? '/brand/logo.svg') }}" alt="" width="150" height="44">
            </a>
            <p class="tagline">{{ $settings['site_tagline'] ?? '' }}</p>
        </div>

        <nav class="bht-footer-grid" id="footer-menu" aria-label="Alt navigasyon">
            @foreach ($columns as $column)
                <div class="footer-col">
                    <p class="footer-col-title">{{ $column['title'] }}</p>
                    <ul>
                        @foreach ($column['links'] as $link)
                            <li><a href="#">{{ $link }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </nav>

        <div class="footer-social">
            @foreach ($social as $network)
                <a href="#" aria-label="{{ $network }}"><x-icon name="{{ $network }}" /></a>
            @endforeach
        </div>

        <div class="footer-badges">
            @foreach ($badges as $badge)
                <a class="footer-badge" href="#">{{ $badge }}</a>
            @endforeach
        </div>

        <div class="brand-strip">
            {{ $settings['brand_list'] ?? 'Habertürk · Habertürk TV · HT Spor · Bloomberg HT' }}
        </div>
        <p class="legal-notice">{{ $settings['legal_notice'] ?? '' }}</p>
        <p class="copyright">{{ $settings['copyright'] ?? 'YASAL UYARI © 2026' }} · KEP: {{ $settings['kep_address'] ?? '' }}</p>
    </div>
</footer>