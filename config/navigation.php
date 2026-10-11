<?php

/*
|--------------------------------------------------------------------------
| Global navigasyon — mega menü ve footer (Bölüm 2.2 / 2.7, birebir)
|--------------------------------------------------------------------------
|
| Bloomberg HT canlı sitesindeki üst mega navigasyon ağacı ve footer
| bağlantı sütunları burada sabit tanımlıdır. URL/etiket eşleşmesi spec'e
| birebir uyar; menşe (menü verisi) veritabanına bağlı DEĞİLDİR.
|
*/

return [

    /*
    | Mega navigasyon: her öğe ya düz bağlantı ya da çocuklu dropdown'dur.
    | 'external' => true ise hedef yeni sekmede açılır (BusinessWeek).
    */
    'mega' => [
        ['label' => 'PİYASALAR', 'url' => '/piyasalar'],
        [
            'label' => 'BORSA',
            'url' => '/borsa',
            'children' => [
                ['label' => 'Hisse Senetleri', 'url' => '/borsa/hisseler'],
                ['label' => 'Yükselen/Düşen Hisseler', 'url' => '/borsa/en-cok'],
                ['label' => 'Kap Haberleri', 'url' => '/borsa/hisseler/kap-haberleri'],
                ['label' => 'Dünya Borsaları', 'url' => '/borsa/dunya-borsalari'],
                ['label' => 'Endeksler', 'url' => '/borsa/endeksler'],
                ['label' => 'Borsa Kapanış', 'url' => '/borsa/borsa-kapanis'],
                ['label' => 'Alternatif Piyasalar', 'url' => '/alternatif'],
            ],
        ],
        [
            'label' => 'DÖVİZ',
            'url' => '/doviz',
            'children' => [
                ['label' => 'Dolar', 'url' => '/doviz/dolar'],
                ['label' => 'Euro', 'url' => '/doviz/euro'],
                ['label' => 'Sterlin', 'url' => '/doviz/ingiliz-sterlini'],
                ['label' => 'İsviçre Frangı', 'url' => '/doviz/isvicre-frangi'],
                ['label' => 'Pariteler', 'url' => '/doviz/pariteler'],
            ],
        ],
        [
            'label' => 'ALTIN',
            'url' => '/altin',
            'children' => [
                ['label' => 'Ons Altın', 'url' => '/altin/altin-ons'],
                ['label' => 'Gram Altın', 'url' => '/altin/gram-altin'],
                ['label' => 'Çeyrek Altın', 'url' => '/altin/ceyrek-altin'],
                ['label' => 'Cumhuriyet Altını', 'url' => '/altin/cumhuriyet-altini'],
                ['label' => 'Ziynet Altını', 'url' => '/altin/ziynet-altini'],
                ['label' => 'Yarım Altın', 'url' => '/altin/yarim-altin'],
                ['label' => '14 Ayar Bilezik', 'url' => '/altin/14-ay-bilezik-gr'],
                ['label' => '22 Ayar Bilezik', 'url' => '/altin/22-ay-bilezik-gr'],
                ['label' => 'Diğer Emtia', 'url' => '/emtia'],
            ],
        ],
        ['label' => 'YATIRIM FONLARI', 'url' => '/yatirim-fonlari'],
        [
            'label' => 'HABERLER',
            'url' => '/haberler',
            'children' => [
                ['label' => 'Fintech', 'url' => '/finansal-teknoloji'],
                ['label' => 'Enerji', 'url' => '/enerji'],
                ['label' => 'Makro', 'url' => '/ekonomik-veriler-ve-gundem'],
                ['label' => 'Halka Arz Takvimi', 'url' => '/halka-arz-takvimi'],
                ['label' => 'Görüş', 'url' => '/gorus'],
            ],
        ],
        ['label' => 'TARIM', 'url' => '/tarim'],
        ['label' => 'ZİRVE', 'url' => '/zirve'],
        ['label' => 'VİDEO', 'url' => '/video'],
        ['label' => 'YAYIN AKIŞI', 'url' => '/yayinakisi'],
        ['label' => 'BusinessWeek', 'url' => 'https://www.businessweek.com.tr/', 'external' => true, 'logo' => 'businessweek-logo-white.svg'],
    ],

    /*
    | Footer 5 bağlantı sütunu (Bölüm 2.7 — sıra ve metinler birebir).
    | "Sterin Kaç TL" canlı sitedeki typo'dur; hedefi İngiliz Sterlini sayfasıdır.
    */
    'footer_columns' => [
        [
            ['label' => 'Businessweek', 'url' => 'https://www.businessweek.com.tr/'],
            ['label' => 'Canlı Yayın', 'url' => '/tv'],
            ['label' => 'Ekonomi Haberleri', 'url' => '/haberler'],
            ['label' => 'Borsa Haberleri', 'url' => '/borsa'],
            ['label' => 'Dünya Borsaları', 'url' => '/borsa/dunya-borsalari'],
            ['label' => 'Kripto Fiyatları', 'url' => '/kripto'],
            ['label' => 'Künye', 'url' => '/kunye'],
        ],
        [
            ['label' => 'Altın Fiyatları', 'url' => '/altin'],
            ['label' => 'Gram Altın Fiyatı', 'url' => '/altin/gram-altin'],
            ['label' => 'Çeyrek Altın Fiyatı', 'url' => '/altin/ceyrek-altin'],
            ['label' => 'Tam Altın Fiyatı', 'url' => '/altin'],
            ['label' => 'Yarım Altın Fiyatı', 'url' => '/altin/yarim-altin'],
            ['label' => 'Ata Altın Fiyatı', 'url' => '/altin'],
            ['label' => 'Altın ONS Fiyatı', 'url' => '/altin/altin-ons'],
        ],
        [
            ['label' => 'Döviz Kurları', 'url' => '/doviz'],
            ['label' => 'Döviz Çevirici', 'url' => '/doviz-cevirici'],
            ['label' => 'Dolar TL', 'url' => '/doviz/dolar'],
            ['label' => 'Euro TL', 'url' => '/doviz/euro'],
            ['label' => 'Sterin Kaç TL', 'url' => '/doviz/ingiliz-sterlini'],
            ['label' => 'Riyal Kaç TL', 'url' => '/doviz'],
            ['label' => 'Kanada Doları', 'url' => '/doviz'],
        ],
        [
            ['label' => 'Canlı Borsa', 'url' => '/borsa'],
            ['label' => 'Gümüş Fiyatları', 'url' => '/emtia'],
            ['label' => 'Brent Petrol Fiyatı', 'url' => '/emtia/petrol'],
            ['label' => 'Bilezik Fiyatları', 'url' => '/altin/22-ay-bilezik-gr'],
            ['label' => 'BIST 100 Endeksi', 'url' => '/borsa/endeks/bist-100'],
            ['label' => 'Altın Hesaplama', 'url' => '/altin-cevirici'],
            ['label' => 'Yüzde Hesaplama', 'url' => '/yuzde-hesaplama'],
        ],
        [
            ['label' => 'Kişisel Verilerin Korunması Politikası', 'url' => '/privacy'],
            ['label' => 'İzleyici Temsilcisi', 'url' => '/bizeulasin/izleyicitemsilcisi'],
            ['label' => 'Kullanım Koşulları', 'url' => '/kullanimkosullari'],
            ['label' => 'Aydınlatma Metni', 'url' => '/aydinlatma-metni'],
            ['label' => 'Çerez Politikası', 'url' => '/cerez-politikasi'],
            ['label' => 'Biyografi', 'url' => '/biyografi'],
            ['label' => 'İletişim', 'url' => '/bizeulasin'],
        ],
    ],

    /*
    | Mobil uygulama rozetleri (Bölüm 2.7).
    */
    'app_badges' => [
        ['label' => 'AppStore', 'url' => 'https://apps.apple.com/tr/app/bloomberg-ht/id587170896'],
        ['label' => 'GooglePlay', 'url' => 'https://play.google.com/store/apps/details?id=com.bloomberght.app'],
        ['label' => 'AppGallery', 'url' => 'https://appgallery.huawei.com/app/C104331421'],
    ],

    /*
    | Sosyal ağlar — görünen ad → ikon sınıfı eşlemesi; gerçek URL'ler
    | SiteSetting (social_*) üzerinden gelir, yoksa bu varsayılan kullanılır.
    */
    'social' => [
        ['name' => 'x', 'icon' => 'twitter-x', 'label' => 'X', 'setting' => 'social_twitter', 'url' => 'https://x.com/bloomberght'],
        ['name' => 'facebook', 'icon' => 'facebook', 'label' => 'Facebook', 'setting' => 'social_facebook', 'url' => 'https://www.facebook.com/bloomberght'],
        ['name' => 'instagram', 'icon' => 'instagram', 'label' => 'Instagram', 'setting' => 'social_instagram', 'url' => 'https://www.instagram.com/bloomberght'],
        ['name' => 'youtube', 'icon' => 'youtube', 'label' => 'YouTube', 'setting' => 'social_youtube', 'url' => 'https://www.youtube.com/user/bloomberghttv'],
    ],

    /*
    | Ciner grubu site şeridi (Bölüm 2.7).
    */
    'ciner_sites' => [
        ['label' => 'Habertürk', 'url' => 'https://www.haberturk.com/'],
        ['label' => 'Habertürk TV', 'url' => 'https://tv.haberturk.com/'],
        ['label' => 'HT Spor', 'url' => 'https://www.htspor.com/'],
        ['label' => 'Bloomberg HT', 'url' => '/'],
        ['label' => 'Businessweek', 'url' => 'https://www.businessweek.com.tr/'],
        ['label' => 'Show TV', 'url' => 'https://www.showtv.com.tr/'],
        ['label' => 'Show Türk', 'url' => 'https://www.showturk.com.tr/'],
        ['label' => 'Show Max', 'url' => 'https://www.showmax.com/'],
        ['label' => 'Can Medya Grubu', 'url' => 'https://www.canmedya.com.tr/'],
    ],
];