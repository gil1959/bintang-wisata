<?php

return [
    'driver' => env('TRANSLATE_DRIVER', 'deepl'),

    'deepl' => [
        'key' => env('DEEPL_API_KEY'),
        'endpoint' => env('DEEPL_ENDPOINT', 'https://api-free.deepl.com/v2/translate'),
    ],
];
