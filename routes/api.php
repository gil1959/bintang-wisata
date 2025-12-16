<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Webhook\TripayWebhookController;
use App\Http\Controllers\Webhook\DokuWebhookController;
use App\Http\Controllers\Webhook\MidtransWebhookController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Webhooks (NO AUTH middleware)
 * URL yang kepake:
 * - /api/webhooks/tripay
 * - /api/webhooks/doku
 * - /api/webhooks/midtrans
 */
Route::post('/webhooks/tripay', TripayWebhookController::class);
Route::post('/webhooks/doku', DokuWebhookController::class);
Route::post('/webhooks/midtrans', MidtransWebhookController::class);
