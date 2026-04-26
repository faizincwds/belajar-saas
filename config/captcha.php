<?php

return [
    'secret' => env('NOCAPTCHA_SECRET'),
    'sitekey' => env('NOCAPTCHA_SITEKEY'),
    'options' => [
        'timeout' => 30,
    ],
    // TAMBAH INI BARIS INI
    'enabled' => env('NOCAPTCHA_ENABLED', true),
];
