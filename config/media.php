<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Lazy-load placeholder
    |--------------------------------------------------------------------------
    |
    | Bloomberg HT canlı sitede tüm görseller 1x1 şeffaf GIF ile başlar ve
    | gerçek URL `data-src` içinde taşınır (Bölüm 1, 16 — blazy deseni).
    |
    */

    'transparent_gif' => '/img/transparent.gif',

    /*
    |--------------------------------------------------------------------------
    | Görsel boyut sözleşmeleri (birebir)
    |--------------------------------------------------------------------------
    |
    | Spec Bölüm 1 "Görsel boyut sözleşmeleri" tablosunun makine-okur hali.
    | `srcset` anahtarı, genişlik varyantlarını (`?w=`) üretir.
    |
    */

    'sizes' => [
        'news_card' => ['width' => 640, 'height' => 360, 'srcset' => [320, 400, 640, 1280]],
        'news_card_square' => ['width' => 640, 'height' => 640],
        'video_thumb' => ['width' => 360, 'height' => 203],
        'program' => ['width' => 500, 'height' => null],
        'magazine' => ['width' => 260, 'height' => 342],
        'businessweek_lead' => ['width' => 750, 'height' => 425],
        'businessweek_side' => ['width' => 400, 'height' => 400],
        'og' => ['width' => 960, 'height' => 540],
    ],

];
