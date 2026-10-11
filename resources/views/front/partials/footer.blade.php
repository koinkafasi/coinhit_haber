@php
    $settings = $siteSettings ?? [];
    $columns = config('navigation.footer_columns', []);
    $badges = config('navigation.app_badges', []);
    $social = config('navigation.social', []);
    $cinerSites = config('navigation.ciner_sites', []);
    $newWindow = 'target="_blank" rel="noopener noreferrer"';
@endphp

{{-- ============================================================
     FOOTER (CH-24: referans yapısı — bg-[#121212] + overlay resim,
     5 sütun grid, rozet/sosyal/marka şeritleri, yasal metinler)
     ============================================================ --}}
<footer class="bg-[#121212] relative z-400">
    <div class="mx-auto bg-[#384552] text-white bg-opacity-[0.9] bg-no-repeat bg-cover bg-center bg-blend-hard-light px-4 overflow-hidden"
         style="background-image:url('/img/placeholders/bg-desktop-footer.png')">
        <div class="container m-auto overflow-hidden">

            {{-- Logo üst şerit --}}
            <div class="w-full flex border-b-2 border-[#707070] py-8">
                <a href="{{ \App\Support\Locale::route('home') }}" class="block h-16" aria-label="{{ $settings['logo_alt'] ?? 'Bloomberg HT' }}">
                    <img src="{{ asset($settings['logo_white_path'] ?? '/brand/logo-white.svg') }}" alt="{{ $settings['logo_alt'] ?? 'Bloomberg HT' }}" class="w-full h-full">
                </a>
            </div>

            {{-- 5 sütun link grid'i --}}
            <div id="footer-menu" class="grid grid-cols-2 sm:grid-cols-5 gap-4 items-start text-sm pt-8 pb-4 border-b-2 border-[#707070]">
                @foreach ($columns as $column)
                    <ul data-footer-col>
                        @foreach ($column as $link)
                            <li class="mb-4">
                                <a href="{{ \App\Support\Locale::navUrl($link['url']) }}"
                                   title="{{ \App\Support\Locale::navLabel($link['label']) }}"
                                   @if (str_starts_with($link['url'], 'http')) {!! $newWindow !!} @endif>{{ \App\Support\Locale::navLabel($link['label']) }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            </div>

            {{-- Mobil uygulama rozetleri + sosyal ağlar --}}
            <div class="w-full flex flex-wrap gap-8 items-center pt-8 pb-8 border-b-2 border-[#707070]">
                <ul id="footer-stores" class="flex gap-2 md:gap-8 flex-wrap">
                    @foreach ($badges as $badge)
                        <li>
                            <a href="{{ $badge['url'] }}" title="{{ $badge['label'] }}" {!! $newWindow !!}>
                                <div class="w-full h-auto">
                                    <span class="block h-10 flex items-center justify-center border border-[#707070] rounded px-4 text-xs text-white bg-black/10">{{ $badge['label'] }}</span>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <ul id="footer-social" class="flex gap-8 text-xl sm:text-2xl ml-auto">
                    @foreach ($social as $item)
                        <li>
                            <a href="{{ $settings[$item['setting']] ?? $item['url'] }}" aria-label="{{ $item['label'] }}" title="{{ $item['label'] }}" {!! $newWindow !!}>
                                <i class="bloomberght-icon-font-{{ $item['name'] }}"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Arama kutusu: "BİRKAÇ KELİME YAZARAK..." + Listele → /arama/{q} --}}
            <div class="w-full flex flex-wrap items-center gap-4 py-6 border-b-2 border-[#707070]">
                <form class="footer-search flex-1 min-w-[260px]" action="{{ \App\Support\Locale::route('search.index') }}" method="get" role="search" data-search-form>
                    <label class="sr-only" for="footer-search-input">{{ __('ui.search') }}</label>
                    <input id="footer-search-input" name="q" type="search" autocomplete="off"
                           placeholder="{{ __('ui.search_placeholder') }}">
                    <button type="submit">{{ __('ui.list') }}</button>
                </form>
            </div>

            {{-- Ciner grubu site şeridi --}}
            <ul id="footer-brandList" class="w-full flex flex-wrap md:justify-between gap-4 gap-y-2 py-8 items-center">
                @foreach ($cinerSites as $site)
                    <li>
                        <a href="{{ $site['url'] }}" title="{{ $site['label'] }}" class="block py-2 text-xs uppercase tracking-wide text-white/70 hover:text-white"
                           @if (str_starts_with($site['url'], 'http')) {!! $newWindow !!} @endif>{{ $site['label'] }}</a>
                    </li>
                @endforeach
            </ul>

            {{-- Can Medya Grubu şeridi --}}
            <div class="w-full flex items-center justify-center text-sm lg:pt-8 pb-8 border-b-2 border-[#707070]">
                <div class="w-full flex items-center justify-center">
                    <span class="text-base font-bold tracking-[.2em] text-white">{{ __('ui.can_medya_grubu') }}</span>
                </div>
            </div>

            {{-- Yasal uyarı / telif --}}
            <div class="text-xxs py-8 border-b-2 border-[#707070] leading-relaxed">
                <p class="legal-notice">{{ $settings['legal_notice'] ?? 'Sitemizdeki veriler Foreks tarafından sağlanmaktadır. NASDAQ, Dow Jones Industrial Average, SHCOM, FTSE 100, CAC 40, DAX 30, Hang Seng, BOVESPA, VİOP, Tahvil-bono verileri 15 dk; CME, NYMEX ve S&P verileri 10 dk gecikmelidir.' }}</p>
                <p class="legal-notice cme-notice">{{ $settings['data_license'] ?? 'CME verilerinin izinsiz kullanımı yasaktır.' }} {{ $settings['no_ai_training'] ?? 'Bu sitedeki içerik, hiçbir yapay zeka modelinin eğitiminde kullanılamaz.' }}</p>
                <p class="legal-notice">
                    {{ $settings['copyright'] ?? 'YASAL UYARI © 2026' }}
                    · KEP: <a href="mailto:{{ $settings['kep_address'] ?? 'cgorsel@hs03.kep.tr' }}" class="underline">{{ $settings['kep_address'] ?? 'cgorsel@hs03.kep.tr' }}</a>
                </p>
            </div>
        </div>
    </div>
</footer>
