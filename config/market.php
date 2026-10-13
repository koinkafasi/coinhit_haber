<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Canlı güncelleme / polling (Bölüm 5, 10)
    |--------------------------------------------------------------------------
    |
    | Pano arayüzü bu aralıklarla `/piyasa/veri` ucundan güncel fiyatları
    | çeker. Hisse senetleri daha sık güncellenir (BIST ~3-5sn).
    |
    */

    'poll_seconds' => 5,

    'poll_seconds_stock' => 3,

    'timestamp_format' => 'Y/m/d H:i:s',

    /*
    |--------------------------------------------------------------------------
    | Veri kaynağı / gecikme etiketleri (Bölüm 5, 11.6, 14.6)
    |--------------------------------------------------------------------------
    |
    | Foreks ve CME kaynak notları, gecikme süreleri birebir taşınır.
    |
    */

    'sources' => [
        'foreks' => [
            'label' => 'Foreks',
            'delay_minutes' => 15,
            'delay_label' => '15 dk gecikmeli',
            // Gerçek veri snapshot'ı yoksa/bayatsa simülatöre düşmeden önceki tazelik penceresi.
            'max_age_minutes' => 30,
            'note' => 'Sitemizdeki veriler Foreks tarafından sağlanmaktadır. NASDAQ, Dow Jones Industrial Average, FTSE 100, CAC 40, DAX 30, Hang Seng, BOVESPA, VİOP, Tahvil-bono verileri 15 dk gecikmelidir.',
        ],
        'cme' => [
            'label' => 'CME',
            'delay_minutes' => 10,
            'delay_label' => '10 dk gecikmeli',
            'max_age_minutes' => 30,
            'note' => 'CME, NYMEX ve S&P verileri 10 dk gecikmelidir. CME verilerinin izinsiz kullanımı yasaktır ve bu veriler hiçbir yapay zeka/öğrenme modelinin eğitiminde kullanılamaz (no AI/ML training use).',
            'license' => 'Bu sayfada yer alan piyasa verileri, Chicago Mercantile Exchange Inc. veya onun lisans verenlerine ait özel bilgileri içermektedir. Özel olarak yetki verilmedikçe kopyalanamaz, satılamaz veya dağıtılamaz. Yatırım tavsiyesi teşkil etmez. Yalnızca bilgilendirme amacıyla sağlanmaktadır. Hiçbir ticari amaçla kullanılmamaktadır. Tam, kesin veya zamanında olduğuna dair herhangi bir garanti verilmemektedir.',
        ],
        'forex' => [
            'label' => 'Forex',
            'delay_minutes' => 15,
            'delay_label' => '15 dk gecikmeli',
            'max_age_minutes' => 30,
            'note' => 'Döviz ve parite verileri 15 dk gecikmelidir.',
        ],
        'crypto' => [
            'label' => 'Kripto',
            'delay_minutes' => 0,
            'delay_label' => '7/24 anlık',
            'max_age_minutes' => 15,
            'note' => 'Kripto para verileri 7/24 anlıktır.',
        ],
        // Gerçek veri toplayıcı kaynakları (market-collector Go aracı)
        'tcmb' => [
            'label' => 'TCMB',
            'delay_minutes' => 0,
            'delay_label' => 'Resmi kur',
            'max_age_minutes' => 300, // TCMB bülteni gün içinde birkaç kez güncellenir
            'note' => 'Döviz kurları Türkiye Cumhuriyet Merkez Bankası resmi gösterge kurlarıdır.',
        ],
        'yahoo' => [
            'label' => 'Yahoo Finance',
            'delay_minutes' => 15,
            'delay_label' => '15 dk gecikmeli',
            'max_age_minutes' => 30,
            'note' => 'Borsa, parite ve emtia verileri Yahoo Finance üzerinden sağlanmaktadır (15 dk gecikmeli).',
        ],
        'goldprice' => [
            'label' => 'GoldPrice.dev',
            'delay_minutes' => 0,
            'delay_label' => 'Spot',
            'max_age_minutes' => 60,
            'note' => 'Altın/Gümüş spot fiyatı goldprice.dev üzerinden sağlanmaktadır.',
        ],
        'evds' => [
            'label' => 'TCMB EVDS',
            'delay_minutes' => 0,
            'delay_label' => 'Resmi',
            'max_age_minutes' => 1440, // iş günü bazlı resmi seri (repo faizi)
            'note' => 'Repo faiz oranları Türkiye Cumhuriyet Merkez Bankası EVDS resmi verileridir.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sayı biçimlendirme (Bölüm 5)
    |--------------------------------------------------------------------------
    |
    | TRY kurları 4 ondalık, JPY 2 ondalık; altın serbest piyasa fiyatları
    | virgüllü gösterim. Binlik ayraç nokta, ondalık ayraç virgül.
    |
    */

    'format' => [
        'decimal_separator' => ',',
        'thousands_separator' => '.',
    ],

    'currency_decimals' => [
        'TRY' => 4,
        'JPY' => 2,
        'USD' => 2,
        'EUR' => 2,
    ],

    /*
    |--------------------------------------------------------------------------
    | CoinGecko (CH-2 — kripto piyasa veri motoru)
    |--------------------------------------------------------------------------
    |
    | `market:snapshot` komutu CoinGecko /coins/markets ucundan kripto
    | fiyatlarını çeker ve market_snapshots tablosuna yazar. Taze snapshot
    | (max_age dakika içinde) varsa MarketDataService kripto quote'larını
    | bundan üretir; yoksa deterministik simülatöre düşer — site hiçbir
    | koşulda kırılmaz. Anahtar .env'dedir (COINGECKO_API_KEY) ve repoya girmez.
    |
    */

    'coingecko' => [
        'api_key' => env('COINGECKO_API_KEY', ''),
        'vs_currency' => 'usd',
        // Snapshot bu dakika sayısından eskiyse "taze" sayılmaz (simülatöre dön).
        'max_age_minutes' => (int) env('COINGECKO_MAX_AGE_MINUTES', 15),
        // sparkline_7d dizisindeki yaklaşık nokta sayısı (saatlik, 7 gün).
        'sparkline_points' => 168,
    ],

];