<?php

$serviceAccount = env('FIREBASE_SERVICE_ACCOUNT_PATH');
if ($serviceAccount && !file_exists($serviceAccount) && file_exists(base_path($serviceAccount))) {
    $serviceAccount = base_path($serviceAccount);
}

return [
    'firebase_service_account' => $serviceAccount,
    'android_fingerprints' => array_values(array_filter(array_map('trim', explode(',', env('ANDROID_APP_SHA256', ''))))),
    'onesignal_app_id' => env('ONESIGNAL_APP_ID'),
    'onesignal_rest_api_key' => env('ONESIGNAL_REST_API_KEY'),
];
