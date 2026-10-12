<?php
return [
    'firebase_service_account'=>env('FIREBASE_SERVICE_ACCOUNT_PATH'),
    'android_fingerprints'=>array_values(array_filter(array_map('trim',explode(',',env('ANDROID_APP_SHA256',''))))),
];
