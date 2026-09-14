<?php

return [

    'base_url' => 'https://uat-backup.darmawisataindonesiah2h.co.id:7080/H2H',

    'user_id'  => env('DARMAWISATA_USER_ID'),
    'password' => env('DARMAWISATA_PASSWORD'),

    'language' => (int) env('DARMAWISATA_LANGUAGE', 1),

    'security_scheme' => env('DARMAWISATA_SECURITY_SCHEME', 'auto'),

    'security_schemes' => array_values(array_filter(array_map('trim', explode(',', env(
        'DARMAWISATA_SECURITY_SCHEMES',
        'md5_password_token,md5_token_password,md5_userid_password_token,md5_userid_token_password,md5_md5password_token,md5_token_md5password,md5_userid_md5password_token,md5_userid_token_md5password'
    ))))),

    'timeout' => (int) env('DARMAWISATA_TIMEOUT', 30),

    'token_ttl_minutes' => (int) env('DARMAWISATA_TOKEN_TTL_MINUTES', 50),

    'cities_ttl_minutes' => (int) env('DARMAWISATA_CITIES_TTL_MINUTES', 10080),
];
