<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Mail\OrderInvoiceMail;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class FlightOrderController extends Controller
{
    public function draft(Request $request, string $key)
    {
        $payload = Cache::get("flight_quote_detail:{$key}");
        if (!$payload || !is_array($payload)) {
            $fallback = Cache::get("flight_quote:{$key}");
            if (is_array($fallback)) {
                $payload = [
                    'search' => (array) ($fallback['search'] ?? []),
                    'journey' => (array) ($fallback['journey'] ?? []),
                    'journey_return' => (array) ($fallback['journey_return'] ?? []),
                    'price' => [],
                ];
            }
        }

        if (!$payload || !is_array($payload)) {
            return response()->json([
                'error' => 'Data penerbangan sudah kadaluarsa. Silakan cari ulang.'
            ], 422);
        }

        $data = $request->validate([
            'name'  => 'required|string|max:120',
            'email' => 'required|email',
            'phone' => 'required|string|max:50',
        ]);

        $search = (array)($payload['search'] ?? []);
        $journey = (array)($payload['journey'] ?? []);
        $price = (array)($payload['price'] ?? []);

        $origin = (string)($search['origin'] ?? '');
        $destination = (string)($search['destination'] ?? '');
        $departDate = (string)($search['departDate'] ?? null);

        $paxAdult = (int)($search['paxAdult'] ?? 1);
        $paxChild = (int)($search['paxChild'] ?? 0);
        $paxInfant = (int)($search['paxInfant'] ?? 0);
        $participants = max(1, $paxAdult + $paxChild + $paxInfant);

        $sumFare = (float)($price['sumFare'] ?? $journey['sumPrice'] ?? 0);
        $subtotal = (int) round($sumFare);
        $discount = 0;
        $final = max(0, $subtotal - $discount);

        $userId = auth()->id() ?: User::where('email', $data['email'])->value('id');

        $orderData = [
            'invoice_number' => 'INV-' . date('YmdHis') . rand(1000, 9999),
            'type' => 'flight',
            'product_id' => 0,
            'product_name' => trim("Tiket Pesawat {$origin}-{$destination}"),
            'user_id' => $userId,
            'customer_name' => $data['name'],
            'customer_email' => $data['email'],
            'customer_phone' => $data['phone'],
            'departure_date' => $departDate,
            'participants' => $participants,
            'total_days' => null,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'final_price' => $final,
            'payment_status' => 'waiting_payment',
            'order_status' => 'pending',
        ];

        if (Schema::hasColumn('orders', 'meta')) {
            $orderData['meta'] = [
                'provider' => 'darmawisata',
                'quote_key' => $key,
                'search' => $search,
                'journey' => $journey,
                'price' => $price,
                'journey_return' => (array) ($payload['journey_return'] ?? []),
                'airline_booking_ready' => [
                    'airlineID' => (string) data_get($journey, 'airlineID', ''),
                    'journeyReference' => (string) data_get($journey, 'journeyReference', ''),
                    'journeyReturnReference' => (string) data_get($payload, 'journey_return.journeyReference', ''),
                    'airlineAccessCode' => (string) (
                        data_get($journey, 'airlineAccessCode', '')
                        ?: data_get($payload, 'journey_return.airlineAccessCode', '')
                    ),
                    'searchKey' => (string) data_get($price, 'searchKey', ''),
                ],
            ];
        }

        $order = Order::create($orderData);

        try {
            if (!empty($order->customer_email)) {
                Mail::to($order->customer_email)->send(new OrderInvoiceMail($order, false));
            }

            $adminEmail = Setting::invoiceAdminEmail();
            if (!empty($adminEmail) && $adminEmail !== $order->customer_email) {
                Mail::to($adminEmail)->send(new OrderInvoiceMail($order, true));
            }
        } catch (\Throwable $e) {
            Log::warning('Invoice email gagal dikirim (flight)', [
                'invoice' => $order->invoice_number,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'redirect' => route('checkout.show', $order->id)
        ]);
    }
}
