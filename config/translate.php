<?php

return [
    'driver' => env('TRANSLATE_DRIVER', 'deepl'),

    'deepl' => [
        'key' => env('DEEPL_API_KEY'),
        'endpoint' => env('DEEPL_ENDPOINT', 'https://api-free.deepl.com/v2/translate'),
    ],

    'libretranslate' => [
        // contoh: https://translate.dailyplanner.cloud
        'url' => env('LIBRETRANSLATE_URL', 'http://localhost:5000'),
        // optional (kalau server lu pakai key)
        'key' => env('LIBRETRANSLATE_API_KEY'),
        'timeout' => (int) env('LIBRETRANSLATE_TIMEOUT', 25),
    ],
];
