<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\RentCarPackage;
use Illuminate\Http\Request;

class RentCarController extends Controller
{
    /**
     * Menampilkan semua paket rent car
     */
    public function index()
    {
        $packages = RentCarPackage::where('is_active', 1)->get();

        return view('front.rentcar.index', compact('packages'));
    }

    /**
     * Menampilkan detail satu paket berdasarkan slug
     */
    public function show($slug)
    {
        $package = RentCarPackage::where('slug', $slug)->firstOrFail();

        return view('front.rentcar.show', compact('package'));
    }

    /**
     * POST setelah user pilih tanggal (TIDAK ke checkout)
     * cuma validasi & return data ke page lu sendiri.
     */
    public function postBooking(Request $request, $slug)
    {
        $request->validate([
            'pickup_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:pickup_date',
        ]);

        $package = RentCarPackage::where('slug', $slug)->firstOrFail();

        // hitung hari
        $start = new \Carbon\Carbon($request->pickup_date);
        $end   = new \Carbon\Carbon($request->return_date);

        $days = $start->diffInDays($end) + 1;
        $total = $days * $package->price_per_day;

        // UNTUK SEKARANG:
        // CUMA RETURN VIEW SEMENTARA (belum buat halaman pembayaran)
        return view('front.rentcar.temp-booking', [
            'package' => $package,
            'pickup'  => $request->pickup_date,
            'return'  => $request->return_date,
            'days'    => $days,
            'total'   => $total,
        ]);
    }
}
