<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class FlightAffiliateOrderController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $status = trim((string) $request->get('status'));
        $origin = strtoupper(trim((string) $request->get('origin')));
        $destination = strtoupper(trim((string) $request->get('destination')));

        $orders = Order::query()
            ->where('type', 'flight')
            ->whereNotNull('affiliate_user_id')
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('invoice_number', 'like', "%{$q}%")
                        ->orWhere('customer_name', 'like', "%{$q}%")
                        ->orWhere('customer_email', 'like', "%{$q}%")
                        ->orWhere('affiliate_ref', 'like', "%{$q}%")
                        ->orWhere('product_name', 'like', "%{$q}%");
                });
            })
            ->when($origin !== '', function ($qq) use ($origin) {
                $qq->where('product_name', 'like', "%{$origin}%");
            })
            ->when($destination !== '', function ($qq) use ($destination) {
                $qq->where('product_name', 'like', "%{$destination}%");
            })
            ->when(in_array($status, ['pending', 'approved', 'paid', 'cancelled'], true), function ($qq) use ($status) {
                $qq->where('affiliate_commission_status', $status);
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.flights.affiliate-orders.index', compact('orders', 'q', 'status', 'origin', 'destination'));
    }

    public function show(Order $order)
    {
        abort_unless($order->type === 'flight' && $order->affiliate_user_id, 404);

        return view('admin.flights.affiliate-orders.show', compact('order'));
    }

    public function setCommission(Request $request, Order $order)
    {
        abort_unless($order->type === 'flight' && $order->affiliate_user_id, 404);

        $data = $request->validate([
            'affiliate_commission_type' => ['required', 'in:fixed,percent'],
            'affiliate_commission_value' => ['required', 'numeric', 'min:0'],
            'affiliate_commission_status' => ['required', 'in:pending,approved,paid,cancelled'],
        ]);

        if ($data['affiliate_commission_type'] === 'percent' && (float) $data['affiliate_commission_value'] > 100) {
            return back()->withInput()->with('error', 'Percent tidak boleh lebih dari 100.');
        }

        $type = $data['affiliate_commission_type'];
        $value = (float) $data['affiliate_commission_value'];

        if ($type === 'percent') {
            $amount = round(((float) $order->final_price * $value) / 100, 2);
        } else {
            $amount = round($value, 2);
        }

        $order->affiliate_commission_type = $type;
        $order->affiliate_commission_value = $value;
        $order->affiliate_commission_amount = $amount;
        $order->affiliate_commission_status = $data['affiliate_commission_status'];
        $order->affiliate_commission_set_by = auth()->id();
        $order->affiliate_commission_set_at = now();
        $order->save();

        return redirect()
            ->route('admin.flights.affiliate-orders.show', $order->id)
            ->with('success', 'Komisi affiliate tiket pesawat berhasil disimpan.');
    }
}
