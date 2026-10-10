<?php

/*
|--------------------------------------------------------------------------
| i18n — Desteklenen diller (master spec §18)
|--------------------------------------------------------------------------
|
| tr varsayılan ve prefixsizdir; en yalnızca `/en` prefix'i ile erişilir.
| Her dil için native ad, yazı yönü, etkinlik ve OG locale değeri tanımlıdır.
|
*/

return [

    'default' => 'tr',

    'fallback' => 'tr',

    'supported' => [
        'tr' => [
            'code' => 'tr',
            'native' => 'Türkçe',
            'direction' => 'ltr',
            'enabled' => true,
            'og' => 'tr_TR',
        ],
        'en' => [
            'code' => 'en',
            'native' => 'English',
            'direction' => 'ltr',
            'enabled' => true,
            'og' => 'en_US',
        ],
    ],
];
