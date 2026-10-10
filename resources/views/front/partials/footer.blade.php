@php
    $settings = $siteSettings ?? [];
    $columns = config('navigation.footer_columns', []);
    $badges = config('navigation.app_badges', []);
    $social = config('navigation.social', []);
    $cinerSites = config('navigation.ciner_sites', []);
    $newWindow = 'target="_blank" rel="noopener noreferrer"';
@endphp

<footer class="site-footer bht-footer">
    <div class="container">
        <div class="footer-top">
            <a class="logo" href="{{ route('home') }}" aria-label="{{ $settings['logo_alt'] ?? 'Bloomberg HT' }}">
                <img src="{{ asset($settings['logo_path'] ?? '/brand/logo.svg') }}" alt="{{ $settings['logo_alt'] ?? 'Bloomberg HT' }}" width="150" height="44">
            </a>
            <p class="tagline">{{ $settings['site_tagline'] ?? "Türkiye'nin ekonomi platformu" }}</p>
        </div>

        <nav class="bht-footer-grid" id="footer-menu" aria-label="Alt navigasyon">
            @foreach ($columns as $column)
                <div class="footer-col">
                    <ul>
                        @foreach ($column as $link)
                            <li>
                                <a href="{{ $link['url'] }}"
                                   @if (str_starts_with($link['url'], 'http')) {!! $newWindow !!} @endif>{{ $link['label'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </nav>

        {{-- Arama kutusu: "BİRKAÇ KELİME YAZARAK..." + Listele → /arama/{q} --}}
        <form class="footer-search" action="{{ route('search.index') }}" method="get" role="search" data-search-form>
            <label class="sr-only" for="footer-search-input">Arama</label>
            <input id="footer-search-input" name="q" type="search" autocomplete="off"
                   placeholder="BİRKAÇ KELİME YAZARAK SİZE YARDIMCI OLABİLİRİZ!">
            <button type="submit">Listele</button>
        </form>

        <div class="footer-badges">
            @foreach ($badges as $badge)
                <a class="footer-badge" href="{{ $badge['url'] }}" {!! $newWindow !!}>{{ $badge['label'] }}</a>
            @endforeach
        </div>

        <div class="footer-social">
            @foreach ($social as $item)
                <a href="{{ $settings[$item['setting']] ?? $item['url'] }}" aria-label="{{ $item['label'] }}" {!! $newWindow !!}>
                    <x-icon name="{{ $item['icon'] }}" title="{{ $item['label'] }}" />
                </a>
            @endforeach
        </div>

        <div class="brand-strip ciner-strip">
            @foreach ($cinerSites as $site)
                <a href="{{ $site['url'] }}"
                   @if (str_starts_with($site['url'], 'http')) {!! $newWindow !!} @endif>{{ $site['label'] }}</a>
            @endforeach
        </div>

        <p class="legal-notice">{{ $settings['legal_notice'] ?? 'Sitemizdeki veriler Foreks tarafından sağlanmaktadır. NASDAQ, Dow Jones Industrial Average, SHCOM, FTSE 100, CAC 40, DAX 30, Hang Seng, BOVESPA, VİOP, Tahvil-bono verileri 15 dk; CME, NYMEX ve S&P verileri 10 dk gecikmelidir.' }}</p>
        <p class="legal-notice cme-notice">{{ $settings['data_license'] ?? 'CME verilerinin izinsiz kullanımı yasaktır.' }} {{ $settings['no_ai_training'] ?? 'Bu sitedeki içerik, hiçbir yapay zeka modelinin eğitiminde kullanılamaz.' }}</p>
        <p class="copyright">
            {{ $settings['copyright'] ?? 'YASAL UYARI © 2026' }}
            · KEP: <a href="mailto:{{ $settings['kep_address'] ?? 'cgorsel@hs03.kep.tr' }}">{{ $settings['kep_address'] ?? 'cgorsel@hs03.kep.tr' }}</a>
        </p>
    </div>
</footer>