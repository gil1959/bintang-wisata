<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Models\PaymentMethod;
use App\Services\Payments\TripayService;
use App\Services\Payments\DokuService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function index()
    {

        $defaults = [
            ['name' => 'doku', 'label' => 'DOKU'],
            ['name' => 'tripay', 'label' => 'TriPay'],
            ['name' => 'midtrans', 'label' => 'Midtrans'],
        ];

        foreach ($defaults as $d) {
            PaymentGateway::firstOrCreate(
                ['name' => $d['name']],
                [
                    'label' => $d['label'],
                    'is_active' => false,
                    'credentials' => null,
                    'channels' => null,
                    'channels_synced_at' => null,
                ]
            );
        }

        $methods = PaymentMethod::orderBy('id', 'desc')->get();

        // Tampilkan HANYA 3 gateway
        $gateways = PaymentGateway::whereIn('name', ['doku', 'tripay', 'midtrans'])
            ->orderBy('id')
            ->get();

        return view('admin.payments.index', compact('methods', 'gateways'));
    }

    public function addBank(Request $request)
    {
        $request->validate([
            'bank_name'      => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_holder' => 'required|string|max:100',
        ]);

        PaymentMethod::create([
            'method_name'     => $request->bank_name,
            'slug'            => 'manual-' . Str::slug($request->bank_name) . '-' . time(),
            'type'            => 'manual',
            'bank_name'       => $request->bank_name,
            'account_number'  => $request->account_number,
            'account_holder'  => $request->account_holder,
            'is_active'       => true,
        ]);

        return back()->with('success', 'Bank manual berhasil ditambahkan.');
    }
    public function deleteBank($bank)
    {
        // $bank itu id dari payment_methods
        $method = PaymentMethod::where('id', (int) $bank)
            ->where('type', 'manual')
            ->first();

        if (!$method) {
            return back()->with('error', 'Rekening manual tidak ditemukan.');
        }

        $method->delete();

        return back()->with('success', 'Rekening manual berhasil dihapus.');
    }


    public function toggleGateway(Request $request, $id, TripayService $tripay, DokuService $doku)
    {
        $gateway = PaymentGateway::findOrFail($id);

        if (!in_array($gateway->name, ['doku', 'tripay', 'midtrans'], true)) {
            abort(404);
        }

        $enable = (bool) $request->input('enable');

        // DISABLE
        if (!$enable) {
            $gateway->is_active = false;
            $gateway->save();

            return back()->with('success', strtoupper($gateway->name) . ' nonaktif.');
        }

        // ENABLE
        $mode = $request->input('mode', 'sandbox');
        if (!in_array($mode, ['sandbox', 'production'], true)) {
            return back()->with('error', 'Mode harus sandbox atau production.');
        }

        $credentials = ['mode' => $mode];

        // =========================
        // TRIPAY
        // =========================
        if ($gateway->name === 'tripay') {
            $credentials['api_key']       = trim((string) $request->input('api_key', ''));
            $credentials['private_key']   = trim((string) $request->input('private_key', ''));
            $credentials['merchant_code'] = trim((string) $request->input('merchant_code', ''));

            foreach (['api_key', 'private_key', 'merchant_code'] as $k) {
                if ($credentials[$k] === '') {
                    return back()->with('error', "TriPay: field {$k} wajib.");
                }
            }

            try {
                $channels = $tripay->fetchChannels($credentials);

                // simpan hanya active (biar list checkout clean)
                $channels = array_values(array_filter($channels, function ($c) {
                    return ($c['active'] ?? true) === true;
                }));
            } catch (\Throwable $e) {
                return back()->with('error', 'Gagal sync channel TriPay: ' . $e->getMessage());
            }
        }

        // =========================
        // DOKU
        // =========================
        elseif ($gateway->name === 'doku') {
            $credentials['client_id']  = trim((string) $request->input('client_id', ''));
            $credentials['secret_key'] = trim((string) $request->input('secret_key', ''));

            foreach (['client_id', 'secret_key'] as $k) {
                if ($credentials[$k] === '') {
                    return back()->with('error', "DOKU: field {$k} wajib.");
                }
            }

            $channels = $doku->staticChannels();
        }

        // =========================
        // MIDTRANS
        // =========================
        else { // midtrans
            $credentials['server_key'] = trim((string) $request->input('server_key', ''));
            $credentials['client_key'] = trim((string) $request->input('client_key', ''));

            foreach (['server_key', 'client_key'] as $k) {
                if ($credentials[$k] === '') {
                    return back()->with('error', "Midtrans: field {$k} wajib.");
                }
            }

            $channels = [
                ['channel_code' => 'snap', 'name' => 'Midtrans Snap (All Methods)'],
            ];
        }

        // SAVE
        $gateway->is_active = true;
        $gateway->credentials = $credentials;
        $gateway->channels = $channels;
        $gateway->channels_synced_at = now();
        $gateway->save();

        return back()->with('success', strtoupper($gateway->name) . ' aktif.');
    }
}
