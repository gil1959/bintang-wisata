<?php
namespace App\Services\Mobile;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceDelivery
{
    public function send(Request $request, Closure $send): void
    {
        if (!$request->is('api/v1/*')) {
            $send();
            return;
        }
        // Register only after the order and its idempotency record are committed.
        // Shared hosting does not need a separate queue worker for this callback.
        DB::afterCommit(function () use ($send) {
            $sent = false;
            app()->terminating(function () use ($send, &$sent) {
                if (!$sent) {
                    $sent = true;
                    $send();
                }
            });
        });
    }
}
