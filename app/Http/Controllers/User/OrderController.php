<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Setting;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $authUser = auth()->user();

$q = Order::query()
    ->where(function ($sub) use ($authUser) {
        $sub->where('user_id', $authUser->id)
            ->orWhere(function ($q2) use ($authUser) {
                $q2->whereNull('user_id')
                   ->where('customer_email', $authUser->email);
            });
    });


        // Search: invoice / product
        if ($request->filled('search')) {
            $search = trim($request->search);
            $q->where(function ($sub) use ($search) {
                $sub->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('product_name', 'like', "%{$search}%");
            });
        }

        // Filter: type
        if ($request->filled('type')) {
            $q->where('type', $request->type);
        }

        // Filter: order_status
        if ($request->filled('order_status')) {
            $q->where('order_status', $request->order_status);
        }

        // Filter: payment_status
        if ($request->filled('payment_status')) {
            $q->where('payment_status', $request->payment_status);
        }

        $orders = $q->latest()->paginate(10)->withQueryString();

        return view('user.orders', compact('orders'));
    }

    public function show(Order $order)
    {
        // Security: jangan sampai user bisa buka order orang lain
        $authUser = auth()->user();

$canView =
    ($order->user_id !== null && $order->user_id === $authUser->id)
    || ($order->user_id === null
        && !empty($order->customer_email)
        && $order->customer_email === $authUser->email);

abort_unless($canView, 403);


        $order->load('payments');

        return view('user.orders.show', compact('order'));
    }
    public function confirmAdmin(Order $order)
{
    $authUser = auth()->user();

$canView =
    ($order->user_id !== null && $order->user_id === $authUser->id)
    || ($order->user_id === null
        && !empty($order->customer_email)
        && $order->customer_email === $authUser->email);

abort_unless($canView, 403);


    $rawWa = (string) Setting::where('key', 'footer_whatsapp')->value('value');
    $wa = preg_replace('/\D+/', '', $rawWa);

    if (str_starts_with($wa, '0')) {
        $wa = '62' . substr($wa, 1);
    }

    abort_if(empty($wa), 404, 'Nomor WhatsApp admin belum diset.');

    $total = $order->payable_amount ?? $order->final_price;

    $msg =
        "Halo Admin,\n"
        . "Saya ingin konfirmasi order:\n\n"
        . "Invoice: {$order->invoice_number}\n"
        . "Nama: {$order->customer_name}\n"
        . "Email: {$order->customer_email}\n"
        . "WA Customer: {$order->customer_phone}\n"
        . "Produk: {$order->product_name}\n"
        . "Total: Rp " . number_format((int)$total, 0, ',', '.') . "\n\n"
        . "Terima kasih.";

    return redirect()->away("https://wa.me/{$wa}?text=" . urlencode($msg));
}

}
