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
            'note' => 'Sitemizdeki veriler Foreks tarafından sağlanmaktadır. NASDAQ, Dow Jones Industrial Average, FTSE 100, CAC 40, DAX 30, Hang Seng, BOVESPA, VİOP, Tahvil-bono verileri 15 dk gecikmelidir.',
        ],
        'cme' => [
            'label' => 'CME',
            'delay_minutes' => 10,
            'delay_label' => '10 dk gecikmeli',
            'note' => 'CME, NYMEX ve S&P verileri 10 dk gecikmelidir. CME verilerinin izinsiz kullanımı yasaktır ve bu veriler hiçbir yapay zeka modelinin eğitiminde kullanılamaz.',
            'license' => 'Bu sayfada yer alan piyasa verileri, Chicago Mercantile Exchange Inc. veya onun lisans verenlerine ait özel bilgileri içermektedir. Özel olarak yetki verilmedikçe kopyalanamaz, satılamaz veya dağıtılamaz. Yatırım tavsiyesi teşkil etmez. Yalnızca bilgilendirme amacıyla sağlanmaktadır. Hiçbir ticari amaçla kullanılmamaktadır. Tam, kesin veya zamanında olduğuna dair herhangi bir garanti verilmemektedir.',
        ],
        'forex' => [
            'label' => 'Forex',
            'delay_minutes' => 15,
            'delay_label' => '15 dk gecikmeli',
            'note' => 'Döviz ve parite verileri 15 dk gecikmelidir.',
        ],
        'crypto' => [
            'label' => 'Kripto',
            'delay_minutes' => 0,
            'delay_label' => '7/24 anlık',
            'note' => 'Kripto para verileri 7/24 anlıktır.',
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

];