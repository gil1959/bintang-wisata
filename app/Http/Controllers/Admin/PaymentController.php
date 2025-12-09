<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\PaymentGateway; // ❗ WAJIB kalau gateway disimpan di tabel ini

class PaymentController extends Controller
{
    public function index()
    {
        // Ambil bank manual (transfer manual)
        $methods = PaymentMethod::where('type', 'manual')->orderBy('id', 'desc')->get();

        // Ambil gateway online (xendit, tripay, duitku)
        $gateways = PaymentGateway::orderBy('id', 'asc')->get();

        return view('admin.payments.index', compact('methods', 'gateways'));
    }

    /**
     * TAMBAH BANK MANUAL
     */
    public function addBank(Request $request)
    {
        $request->validate([
            'bank_name'      => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_holder' => 'required|string|max:100',
        ]);

        PaymentMethod::create([
            'method_name'    => $request->bank_name . ' (' . $request->account_number . ')',
            'slug'           => 'manual-' . strtolower(preg_replace('/\s+/', '-', $request->bank_name)),
            'type'           => 'manual',

            'bank_name'      => $request->bank_name,
            'account_number' => $request->account_number,
            'account_holder' => $request->account_holder,

            'gateway_name'   => null,
            'is_active'      => 1,
        ]);

        return back()->with('success', 'Metode pembayaran manual berhasil ditambahkan.');
    }

    /**
     * UPDATE PAYMENT GATEWAY STATUS & CREDENTIALS
     */
    public function updateGateway(Request $request, PaymentGateway $gateway)
    {
        $gateway->update([
            'is_active'   => $request->has('is_active') ? 1 : 0,
            'credentials' => $request->credentials ?? []
        ]);

        return back()->with('success', 'Gateway berhasil diperbarui.');
    }

    /**
     * DELETE BANK TRANSFER
     */
    public function deleteBank(PaymentMethod $bank)
    {
        $bank->delete();
        return back()->with('success', 'Metode pembayaran manual berhasil dihapus.');
    }
}
