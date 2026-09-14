<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Services\Darmawisata\DarmawisataClient;

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

        if ($request->filled('search')) {
            $search = trim($request->search);
            $q->where(function ($sub) use ($search) {
                $sub->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('product_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $q->where('type', $request->type);
        }

        if ($request->filled('order_status')) {
            $q->where('order_status', $request->order_status);
        }

        if ($request->filled('payment_status')) {
            $q->where('payment_status', $request->payment_status);
        }

        $orders = $q->latest()->paginate(10)->withQueryString();

        return view('user.orders', compact('orders'));
    }

    public function show(Order $order, DarmawisataClient $dw)
    {
        $this->authorizeOrder($order);

        $order->load('payments');
        $this->syncFlightTicketDetail($order, $dw);

        return view('user.orders.show', compact('order'));
    }

    public function confirmAdmin(Order $order)
    {
        $this->authorizeOrder($order);

        $partner = \App\Support\OrderPartnerResolver::resolvePartnerUser($order);

        $targetName = 'Admin';
        $targetWa = null;

        if ($partner && $partner->phone) {
            $targetName = $partner->name ?: 'Partner';
            $targetWa = \App\Support\OrderPartnerResolver::normalizeWhatsapp($partner->phone);
        }

        if (!$targetWa) {
            $rawWa = (string) Setting::where('key', 'footer_whatsapp')->value('value');
            $targetWa = \App\Support\OrderPartnerResolver::normalizeWhatsapp($rawWa);
            $targetName = 'Admin';
        }

        $isEn = app()->getLocale() === 'en';

        abort_if(
            empty($targetWa),
            404,
            $isEn
                ? 'Destination WhatsApp number is not configured (partner/admin).'
                : 'Nomor WhatsApp tujuan belum diset (partner/admin).'
        );

        $total = $order->payable_amount ?? $order->final_price;

        if ($isEn) {
            $msg =
                "Hello {$targetName},\n"
                . "I'd like to confirm an order:\n\n"
                . "Invoice: {$order->invoice_number}\n"
                . "Name: {$order->customer_name}\n"
                . "Email: {$order->customer_email}\n"
                . "Customer WhatsApp: {$order->customer_phone}\n"
                . "Product: {$order->product_name}\n"
                . "Total: Rp " . number_format((int)$total, 0, ',', '.') . "\n\n"
                . "Thank you.";
        } else {
            $msg =
                "Halo {$targetName},\n"
                . "Saya ingin konfirmasi order:\n\n"
                . "Invoice: {$order->invoice_number}\n"
                . "Nama: {$order->customer_name}\n"
                . "Email: {$order->customer_email}\n"
                . "WA Customer: {$order->customer_phone}\n"
                . "Produk: {$order->product_name}\n"
                . "Total: Rp " . number_format((int)$total, 0, ',', '.') . "\n\n"
                . "Terima kasih.";
        }

        return redirect()->away(\App\Support\OrderPartnerResolver::buildWaLink($targetWa, $msg));
    }

    public function printInvoice(Order $order)
    {
        $this->authorizeOrder($order);

        $order->load('payments');

        return view('shared.invoice-print', compact('order'));
    }

    public function printFlightTicket(Order $order, DarmawisataClient $dw)
    {
        $this->authorizeOrder($order);
        abort_unless($order->type === 'flight', 404);

        $this->syncFlightTicketDetail($order, $dw);

        $meta = (array) ($order->meta ?? []);
        $supplierBook = (array) ($meta['supplier_booking'] ?? []);
        $supplierDetail = (array) ($meta['supplier_booking_detail'] ?? []);
        $supplierStatus = (array) ($meta['supplier_status'] ?? []);

        return view('user.orders.print-ticket', compact(
            'order',
            'supplierBook',
            'supplierDetail',
            'supplierStatus'
        ));
    }

    protected function authorizeOrder(Order $order): void
    {
        $authUser = auth()->user();

        $canView =
            ($order->user_id !== null && $order->user_id === $authUser->id)
            || ($order->user_id === null
                && !empty($order->customer_email)
                && $order->customer_email === $authUser->email);

        abort_unless($canView, 403);
    }

    protected function syncFlightTicketDetail(Order $order, DarmawisataClient $dw): void
    {
        if ($order->type !== 'flight') {
            return;
        }

        $meta = (array) ($order->meta ?? []);
        $supplierBook = (array) ($meta['supplier_booking'] ?? []);
        $supplierDetail = (array) ($meta['supplier_booking_detail'] ?? []);

        if (empty($supplierBook['bookingCode']) || empty($supplierBook['bookingDate'])) {
            return;
        }

        $hasUsefulDetail =
            !empty($supplierDetail['ticketDetail']) ||
            !empty($supplierDetail['flightDeparts']) ||
            !empty($supplierDetail['passengers']);

        if ($hasUsefulDetail) {
            return;
        }

        try {
            $detailResp = $dw->bookingDetailAirline([
                'bookingCode' => (string) $supplierBook['bookingCode'],
                'referenceNo' => (string) ($supplierBook['referenceNo'] ?? ''),
                'bookingDate' => (string) $supplierBook['bookingDate'],
            ]);

            $meta['supplier_booking_detail'] = $detailResp;
            $meta['supplier_status'] = array_merge((array) ($meta['supplier_status'] ?? []), [
                'ticket_status' => (string) ($detailResp['ticketStatus'] ?? ''),
                'ticket_detail' => (string) ($detailResp['ticketDetail'] ?? ''),
            ]);

            $order->meta = $meta;
            $order->save();
        } catch (\Throwable $e) {
            \Log::warning('User flight ticket detail sync failed', [
                'order_id' => $order->id,
                'invoice' => $order->invoice_number,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
