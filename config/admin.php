<?php

return [
    /*
    |--------------------------------------------------------------------------
    | İlk admin kullanıcısı (seed)
    |--------------------------------------------------------------------------
    |
    | Bu değerler yalnızca `db:seed` sırasında ilk admin kullanıcısını
    | oluşturmak için kullanılır. Parola repoya yazılmaz; ortam değişkeni
    | (ADMIN_PASSWORD) üzerinden verilir. `config:cache` sonrası değer
    | önbelleğe alındığı için env'e doğrudan bağımlı kalmaz.
    |
    */

    'name' => env('ADMIN_NAME', 'Site Admin'),

    'email' => env('ADMIN_EMAIL', 'admin@coinhit.local'),

    'password' => env('ADMIN_PASSWORD'),
];