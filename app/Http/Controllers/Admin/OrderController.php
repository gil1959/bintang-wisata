<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Semua order
        $orders = Order::query()
            ->orderByDesc('id')
            ->paginate(20);

        $currentFilter = 'all';

        return view('admin.orders.index', compact('orders', 'currentFilter'));
    }

    public function approved(Request $request)
    {
        // Hanya yang sudah approved
        $orders = Order::where('order_status', 'approved')
            ->orderByDesc('id')
            ->paginate(20);

        $currentFilter = 'approved';

        return view('admin.orders.index', compact('orders', 'currentFilter'));
    }

    public function rejected(Request $request)
    {
        // Hanya yang sudah rejected
        $orders = Order::where('order_status', 'rejected')
            ->orderByDesc('id')
            ->paginate(20);

        $currentFilter = 'rejected';

        return view('admin.orders.index', compact('orders', 'currentFilter'));
    }

    public function show(Order $order)
    {
        $order->load('payments');

        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        $payment = $order->payments()->latest()->first();

        if ($data['action'] === 'approve') {
            $order->update([
                'payment_status' => 'paid',
                'order_status'   => 'approved',
            ]);

            if ($payment && $payment->status === 'waiting_verification') {
                $payment->update(['status' => 'paid']);
            }
        } else {
            $order->update([
                'payment_status' => 'failed',
                'order_status'   => 'rejected',
            ]);

            if ($payment && $payment->status === 'waiting_verification') {
                $payment->update(['status' => 'failed']);
            }
        }

        return back()->with('success', 'Status pesanan diperbarui.');
    }

    public function destroy(Order $order)
    {
        // Opsi keamanan: jangan hapus order yang sudah dibayar
        if ($order->payment_status === 'paid') {
            return back()->with('error', 'Tidak dapat menghapus order yang sudah dibayar.');
        }

        // Hapus semua payment terkait dulu
        $order->payments()->delete();
        $order->delete();

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Order berhasil dihapus.');
    }
}
