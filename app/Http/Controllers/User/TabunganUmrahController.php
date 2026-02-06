<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\TabunganUmrahAccount;
use App\Models\TabunganUmrahDeposit;
use App\Mail\TabunganUmrahAccountSubmittedUserMail;
use App\Mail\TabunganUmrahAccountSubmittedAdminMail;
use App\Mail\TabunganUmrahDepositSubmittedUserMail;
use App\Mail\TabunganUmrahDepositSubmittedAdminMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Models\PaymentMethod;


class TabunganUmrahController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $account = TabunganUmrahAccount::where('user_id', $user->id)
            ->latest()
            ->first();

        // Belum pernah daftar, atau terakhir rejected (boleh daftar ulang)
        if (!$account || $account->status === 'rejected') {
            return view('user.tabungan-umrah.register', compact('account'));
        }

        // Masih pending / suspended
        if (in_array($account->status, ['pending','suspended'], true)) {
            return view('user.tabungan-umrah.pending', compact('account'));
        }

        // Verified -> dashboard tabungan
        $approvedTotal = $account->approved_total;
        $target = (int) ($account->target_amount ?? 0);
        $progress = $target > 0 ? min(100, (int) round(($approvedTotal / $target) * 100)) : 0;

        $lastDeposit = $account->deposits()->latest()->first();
        $deposits = $account->deposits()->latest()->paginate(10);

        return view('user.tabungan-umrah.show', compact(
            'account',
            'approvedTotal',
            'target',
            'progress',
            'lastDeposit',
            'deposits'
        ));
    }

    public function storeRegistration(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'full_name' => ['required','string','max:191'],
            'whatsapp' => ['required','string','max:50'],
            'saving_type' => ['required','in:umroh_reguler,haji_furoda'],
        ]);

        $account = TabunganUmrahAccount::create([
            'user_id' => $user->id,
            'full_name' => $request->full_name,
            'whatsapp' => $request->whatsapp,
            'saving_type' => $request->saving_type,
            'status' => 'pending',
        ]);

        // email ke user + admin
        Mail::to($user->email)->send(new TabunganUmrahAccountSubmittedUserMail($account));
        $adminEmail = config('mail.from.address'); // pola di project ini sering pakai config/mail; kalau lu punya setting lain, ganti di sini.
        Mail::to($adminEmail)->send(new TabunganUmrahAccountSubmittedAdminMail($account));

        return redirect()->route('user.tabungan-umrah.index')
            ->with('success', 'Registrasi tabungan umrah berhasil. Menunggu verifikasi admin.');
    }

    public function createDeposit()
    {
        $user = auth()->user();
        $account = TabunganUmrahAccount::where('user_id', $user->id)->latest()->firstOrFail();

        if ($account->status !== 'verified') {
            abort(403, 'Akun tabungan umrah belum terverifikasi.');
        }

        $methods = PaymentMethod::where('type', 'manual')
    ->where('is_active', 1)
    ->orderBy('id', 'desc')
    ->get();

return view('user.tabungan-umrah.deposit-create', compact('account', 'methods'));

    }

    public function storeDeposit(Request $request)
    {
        $user = auth()->user();
        $account = TabunganUmrahAccount::where('user_id', $user->id)->latest()->firstOrFail();

        if ($account->status !== 'verified') {
            abort(403, 'Akun tabungan umrah belum terverifikasi.');
        }

        $request->validate([
            'payment_method_id' => ['required','exists:payment_methods,id'],
            'amount' => ['required','integer','min:1000'],
            'proof_image' => ['required','image','max:4096'],
        ]);
$pm = PaymentMethod::where('id', $request->payment_method_id)
    ->where('type', 'manual')
    ->where('is_active', 1)
    ->first();

if (!$pm) {
    return back()->withErrors(['payment_method_id' => 'Rekening tujuan tidak valid / nonaktif.'])->withInput();
}

        $path = $request->file('proof_image')->store('tabungan_umrah/proofs', 'public');

        $deposit = TabunganUmrahDeposit::create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'payment_method_id' => $pm->id,
            'amount' => $request->amount,
            'proof_image' => $path,
            'status' => 'waiting_verification',
            'submitted_at' => now(),
        ]);

        Mail::to($user->email)->send(new TabunganUmrahDepositSubmittedUserMail($deposit));
        $adminEmail = config('mail.from.address');
        Mail::to($adminEmail)->send(new TabunganUmrahDepositSubmittedAdminMail($deposit));

        return redirect()->route('user.tabungan-umrah.index')
            ->with('success', 'Setoran berhasil dikirim. Menunggu verifikasi admin.');
    }

    public function showDeposit(TabunganUmrahDeposit $deposit)
{
    $user = auth()->user();

    if ((int)$deposit->user_id !== (int)$user->id) {
        abort(403);
    }

    $deposit->load(['paymentMethod','verifier']);

    return view('user.tabungan-umrah.deposit-show', compact('deposit'));
}

}
