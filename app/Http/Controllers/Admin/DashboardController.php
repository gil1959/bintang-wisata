<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\TourPackage;
use App\Models\RentCarPackage;
use App\Models\Visitor;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // TOTAL PENDAPATAN
        $totalRevenue = 0;
        $totalOrders = 0;
        $totalPackages = 0;

        try {
            $totalRevenue = Order::where('payment_status', 'paid')
                ->where('order_status', 'approved')
                ->sum('final_price');

            // TOTAL PESANAN (paid saja)
            $totalOrders = Order::where('payment_status', 'paid')->count();

            // PAKET AKTIF
            $activeTours = TourPackage::where('is_active', 1)->count();
            $activeRentCars = RentCarPackage::where('is_active', 1)->count();
            $totalPackages = $activeTours + $activeRentCars;
        } catch (\Throwable $e) {
            // Safe fallback
        }

        // STATISTIK PENGUNJUNG UNIK IP
        $todayVisitors = 0;
        $weekVisitors = 0;
        $totalVisitors = 0;
        $chartLabels = [];
        $chartData = [];

        try {
            $today = Carbon::today();
            $todayVisitors = Visitor::whereDate('date', $today)->count();
            $weekVisitors = Visitor::whereDate('date', '>=', $today->copy()->subDays(6))->count();
            $totalVisitors = Visitor::count();

            // Data grafik 7 hari terakhir
            for ($i = 6; $i >= 0; $i--) {
                $date = $today->copy()->subDays($i);
                $dateStr = $date->toDateString();
                $chartLabels[] = $date->translatedFormat('d M');
                $chartData[] = Visitor::whereDate('date', $dateStr)->count();
            }
        } catch (\Throwable $e) {
            // Default 7 days empty series
            for ($i = 6; $i >= 0; $i--) {
                $chartLabels[] = Carbon::today()->subDays($i)->format('d M');
                $chartData[] = 0;
            }
        }

        // 5 PENGGUNA TERBARU (BUKAN ADMIN)
        $latestUsers = collect();
        try {
            $latestUsers = User::query()
                ->whereDoesntHave('roles', function ($r) {
                    $r->where('name', 'admin');
                })
                ->orderByDesc('created_at')
                ->take(5)
                ->get();
        } catch (\Throwable $e) {
            // Safe fallback
        }

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'totalPackages',
            'todayVisitors',
            'weekVisitors',
            'totalVisitors',
            'chartLabels',
            'chartData',
            'latestUsers'
        ));
    }
}
