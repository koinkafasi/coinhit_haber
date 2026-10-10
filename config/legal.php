<?php

/*
|--------------------------------------------------------------------------
| Yasal statik sayfalar (master spec §2.7, §3, §11.6, §14.4)
|--------------------------------------------------------------------------
| Gerçek slug'lar ve künye/iletişim verisi birebir taşınır.
*/

return [

    'company' => [
        'name' => 'C Görsel Yayınlar A.Ş.',
        'representative' => 'Ergün Örentel',
        'editor' => 'editor@bloomberght.com',
        'ads' => 'dijitalreklam@cyh.com.tr',
        'phone' => '+90 212 313 6000',
        'address' => 'Abdülhakhamit Cad. No:25 Beyoğlu/İstanbul',
        'kep' => 'cgorsel@hs03.kep.tr',
        'broadcast_type' => 'U-TV, K-TV · Uydu + Kablo Ortamı',
        'logout' => 'Bloomberg HT',
    ],

    'pages' => [

        'kunye' => [
            'title' => 'Künye',
            'description' => 'Bloomberg HT yayın kuruluşu künyesi ve iletişim bilgileri.',
            'sections' => [
                [
                    'heading' => 'Yayın Kuruluşu',
                    'body' => 'C Görsel Yayınlar A.Ş. — Logo/Çağrı İşareti Bloomberg HT. Lisans Tipi: U-TV, K-TV. Yayın Ortamı: Uydu + Kablo Ortamı.',
                ],
                [
                    'heading' => 'Tüzel Kişi Temsilcisi',
                    'body' => 'Ergün Örentel',
                ],
                [
                    'heading' => 'İletişim',
                    'body' => 'Telefon: +90 212 313 6000 · Adres: Abdülhakhamit Cad. No:25 Beyoğlu/İstanbul · Editör: editor@bloomberght.com · Reklam: dijitalreklam@cyh.com.tr · KEP: cgorsel@hs03.kep.tr',
                ],
            ],
        ],

        'bizeulasin' => [
            'title' => 'İletişim',
            'description' => 'Bloomberg HT ile iletişime geçin: öneri, istek, şikayet ve yardım.',
            'type' => 'contact',
            'sections' => [
                [
                    'heading' => 'Bize Ulaşın',
                    'body' => 'Öneri, istek, şikayet ve yardım taleplerinizi aşağıdaki form üzerinden iletebilirsiniz. Adres: Abdülhakhamit Cad. No:25 Beyoğlu/İstanbul · Tel: +90 212 313 6000 · Editör: editor@bloomberght.com',
                ],
            ],
        ],

        'bizeulasin/izleyicitemsilcisi' => [
            'title' => 'İzleyici Temsilcisi',
            'description' => 'Bloomberg HT izleyici temsilcisi iletişim bilgileri.',
            'sections' => [
                [
                    'heading' => 'İzleyici Temsilcisi',
                    'body' => 'Radyo ve Televizyon Üst Kurulu düzenlemeleri kapsamında izleyici temsilcisi, yayınlarla ilgili görüş ve şikayetleri kabul eder.',
                ],
                [
                    'heading' => 'İletişim',
                    'body' => 'C Görsel Yayınlar A.Ş. — Abdülhakhamit Cad. No:25 Beyoğlu/İstanbul · Tel: +90 212 313 6000 · E-posta: editor@bloomberght.com',
                ],
            ],
        ],

        'kullanimkosullari' => [
            'title' => 'Kullanım Koşulları',
            'description' => 'Bloomberg HT web sitesi kullanım koşulları.',
            'sections' => [
                [
                    'heading' => 'Kabul',
                    'body' => 'Bu web sitesini kullanarak aşağıdaki koşulları kabul etmiş sayılırsınız. Koşullar C Görsel Yayınlar A.Ş. tarafından önceden haber verilmeksizin güncellenebilir.',
                ],
                [
                    'heading' => 'İçerik ve Telif',
                    'body' => 'Sitede yer alan tüm metin, görsel, logo ve içerikler C Görsel Yayınlar A.Ş.\'ye aittir; kaynak gösterilmeden kullanılamaz, çoğaltılamaz ve yayınlanamaz.',
                ],
                [
                    'heading' => 'Sorumluluk',
                    'body' => 'Sitedeki piyasa verileri Foreks ve CME gibi üçüncü taraf sağlayıcılardan gecikmeli olarak alınmaktadır ve yatırım tavsiyesi niteliği taşımaz.',
                ],
            ],
        ],

        'aydinlatma-metni' => [
            'title' => 'Aydınlatma Metni',
            'description' => '6698 sayılı Kişisel Verilerin Korunması Kanunu kapsamında aydınlatma metni.',
            'sections' => [
                [
                    'heading' => 'Veri Sorumlusu',
                    'body' => 'Veri sorumlusu C Görsel Yayınlar A.Ş.\'dir. Kişisel verileriniz 6698 sayılı Kanun kapsamında işlenmektedir.',
                ],
                [
                    'heading' => 'İşlenme Amaçları',
                    'body' => 'Kişisel verileriniz; hizmet sunumu, iletişim, istatistik ve yasal yükümlülüklerin yerine getirilmesi amaçlarıyla işlenir.',
                ],
                [
                    'heading' => 'Haklarınız',
                    'body' => 'Kanun\'un 11. maddesi kapsamındaki haklarınız için cgorsel@hs03.kep.tr adresine başvurabilirsiniz.',
                ],
            ],
        ],

        'cerez-politikasi' => [
            'title' => 'Çerez Politikası',
            'description' => 'Bloomberg HT çerez politikası ve çerez tercihleri.',
            'sections' => [
                [
                    'heading' => 'Çerez Nedir?',
                    'body' => 'Çerezler, ziyaret ettiğiniz web sitesi tarafından tarayıcınıza kaydedilen küçük metin dosyalarıdır.',
                ],
                [
                    'heading' => 'Kullanılan Çerezler',
                    'body' => 'Sitemizde zorunlu çerezler, performans/analitik çerezleri (Ogezio, Gemius, Google Analytics) ve reklam çerezleri kullanılmaktadır.',
                ],
                [
                    'heading' => 'Tercihleriniz',
                    'body' => 'Tarayıcı ayarlarınızdan çerezleri reddedebilir veya silebilirsiniz; çerez banner\'ı üzerinden tercihlerinizi yönetebilirsiniz.',
                ],
            ],
        ],

        'privacy' => [
            'title' => 'Kişisel Verilerin Korunması Politikası',
            'description' => 'Bloomberg HT kişisel verilerin korunması politikası.',
            'sections' => [
                [
                    'heading' => 'Politika',
                    'body' => 'C Görsel Yayınlar A.Ş., kişisel verilerin korunmasına azami özen gösterir ve 6698 sayılı Kanun\'a uygun hareket eder.',
                ],
                [
                    'heading' => 'Veri Güvenliği',
                    'body' => 'Kişisel veriler, yetkisiz erişime karşı teknik ve idari tedbirlerle korunur; üçüncü kişilerle yalnızca yasal çerçevede paylaşılır.',
                ],
                [
                    'heading' => 'Başvuru',
                    'body' => 'Politikaya ilişkin sorularınız için cgorsel@hs03.kep.tr adresine yazabilirsiniz.',
                ],
            ],
        ],

        'biyografi' => [
            'title' => 'Biyografi',
            'description' => 'Bloomberg HT yayın kuruluşu ve ekibi hakkında biyografi bilgileri.',
            'sections' => [
                [
                    'heading' => 'Bloomberg HT',
                    'body' => 'Bloomberg HT, Türkiye\'nin ekonomi ve finans odaklı televizyon kanalıdır. C Görsel Yayınlar A.Ş. bünyesinde yayın yapmaktadır.',
                ],
                [
                    'heading' => 'Yayın İlkeleri',
                    'body' => 'Bağımsız, tarafsız ve doğrulanmış ekonomi haberciliği ilkesiyle yayın yapar; piyasa verilerini gerçek zamanlıya yakın sunar.',
                ],
                [
                    'heading' => 'İletişim',
                    'body' => 'Abdülhakhamit Cad. No:25 Beyoğlu/İstanbul · Tel: +90 212 313 6000 · editor@bloomberght.com',
                ],
            ],
        ],

    ],
];
