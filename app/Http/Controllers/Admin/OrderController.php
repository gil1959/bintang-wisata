<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private function buildOrdersQuery(Request $request, ?string $status = null)
    {
        // Ambil keyword dari query string ?q=...
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $keyword = isset($validated['q']) ? trim($validated['q']) : null;

        $query = Order::query();

        // Filter status kalau tab approved/rejected
        if ($status) {
            $query->where('order_status', $status);
        }

        // Filter pencarian
        if ($keyword !== null && $keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('customer_name', 'like', "%{$keyword}%")
                    ->orWhere('invoice_number', 'like', "%{$keyword}%");
            });
        }

        return $query;
    }

    public function index(Request $request)
    {
        $orders = $this->buildOrdersQuery($request)
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString(); // penting: pagination tetap bawa ?q=

        $currentFilter = 'all';

        return view('admin.orders.index', compact('orders', 'currentFilter'));
    }


    public function approved(Request $request)
    {
        $orders = $this->buildOrdersQuery($request, 'approved')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $currentFilter = 'approved';

        return view('admin.orders.index', compact('orders', 'currentFilter'));
    }


    public function rejected(Request $request)
    {
        $orders = $this->buildOrdersQuery($request, 'rejected')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

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
