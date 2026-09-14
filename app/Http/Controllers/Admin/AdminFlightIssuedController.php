<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Darmawisata\DarmawisataClient;
use Illuminate\Support\Facades\Log;

class AdminFlightIssuedController extends Controller
{
    public function issue(Order $order, DarmawisataClient $dw)
    {
        if ($order->type !== 'flight') {
            return back()->with('error', 'Order ini bukan tipe flight.');
        }

        $meta     = (array) ($order->meta ?? []);
        $booking  = (array) ($meta['supplier_booking'] ?? []);
        $search   = (array) ($meta['search'] ?? []);
        $journey  = (array) ($meta['journey'] ?? []);

        if (empty($booking['bookingCode']) || empty($booking['bookingDate'])) {
            return back()->with('error', 'Order belum punya bookingCode/bookingDate dari supplier.');
        }

        try {
            $issuedResp = $dw->issuedAirline([
                'airlineID'         => (string) data_get($booking, 'airlineID', data_get($journey, 'airlineID', '')),
                'origin'            => strtoupper((string) ($search['origin'] ?? data_get($journey, 'jiOrigin', ''))),
                'destination'       => strtoupper((string) ($search['destination'] ?? data_get($journey, 'jiDestination', ''))),
                'tripType'          => (string) ($search['tripType'] ?? 'OneWay'),
                'departDate'        => (string) ($search['departDate'] ?? ''),
                'returnDate'        => (string) ($search['returnDate'] ?? ''),
                'bookingCode'       => (string) $booking['bookingCode'],
                'bookingDate'       => (string) $booking['bookingDate'],
                'airlineAccessCode' => (string) data_get(
                    $meta,
                    'price.airlineAccessCode',
                    data_get($journey, 'airlineAccessCode', '')
                ),
            ]);

            // cek BookingDetail sampai ticketStatus jelas
            $detailResp   = [];
            $ticketStatus = '';
            for ($i = 0; $i < 4; $i++) {
                $detailResp = $dw->bookingDetailAirline([
                    'bookingCode' => (string) ($booking['bookingCode'] ?? ''),
                    'referenceNo' => (string) ($booking['referenceNo'] ?? ''),
                    'bookingDate' => (string) ($booking['bookingDate'] ?? ''),
                ]);
                $ticketStatus = strtoupper(trim((string) ($detailResp['ticketStatus'] ?? '')));
                if (in_array($ticketStatus, ['ISSUED', 'TICKETED', 'CANCELLED', 'HOLD'], true)) {
                    break;
                }
                usleep(500000);
            }

            $meta['supplier_issued']         = $issuedResp;
            $meta['supplier_booking_detail'] = $detailResp;
            $meta['supplier_status'] = [
                'issued_status'       => (string) ($issuedResp['status'] ?? ''),
                'issued_resp_message' => (string) ($issuedResp['respMessage'] ?? ''),
                'booking_status'      => (string) ($issuedResp['bookingStatus'] ?? ''),
                'ticket_status'       => $ticketStatus,
                'ticket_detail'       => (string) ($detailResp['ticketDetail'] ?? ''),
            ];

            $order->meta = $meta;

            if (in_array($ticketStatus, ['ISSUED', 'TICKETED'], true)) {
                $order->order_status = 'approved';
            } elseif ($ticketStatus === 'CANCELLED') {
                $order->order_status = 'cancelled';
            }
            $order->save();

            Log::info('Admin flight issued', [
                'order_id'     => $order->id,
                'bookingCode'  => $booking['bookingCode'],
                'issuedStatus' => $issuedResp['status'] ?? null,
                'ticketStatus' => $ticketStatus,
            ]);

            $msg = 'Issued berhasil dipanggil.';
            if ($ticketStatus) $msg .= ' Ticket status: ' . $ticketStatus;

            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            Log::error('Admin flight issued failed', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
            return back()->with('error', 'Issued gagal: ' . $e->getMessage());
        }
    }
}
