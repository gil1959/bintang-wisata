<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$order = App\Models\Order::where('invoice_number', 'INV-202604171127212688')->first();
if ($order) {
    file_put_contents('test_addons.log', json_encode([
        'supplier_booking_detail' => $order->meta['supplier_booking_detail'] ?? [],
        'addons_response' => $order->meta['addons_response'] ?? null
    ], JSON_PRETTY_PRINT));
} else {
    file_put_contents('test_addons.log', 'Order not found');
}
