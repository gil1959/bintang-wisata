<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // Basic order stats
        $totalOrders = Order::where('user_id', $userId)->count();

        $pendingOrders = Order::where('user_id', $userId)
            ->where('order_status', 'pending')
            ->count();

        $approvedOrders = Order::where('user_id', $userId)
            ->where('order_status', 'approved')
            ->count();

        $rejectedOrders = Order::where('user_id', $userId)
            ->where('order_status', 'rejected')
            ->count();

        // Payment stats (optional tapi useful)
        $waitingPayment = Order::where('user_id', $userId)
            ->where('payment_status', 'waiting_payment')
            ->count();

        $waitingVerification = Order::where('user_id', $userId)
            ->where('payment_status', 'waiting_verification')
            ->count();

        $paidOrders = Order::where('user_id', $userId)
            ->where('payment_status', 'paid')
            ->count();

        // Spend (pakai final_price; lu udah punya field ini)
        $totalSpend = Order::where('user_id', $userId)
            ->where('payment_status', 'paid')
            ->sum('final_price');

        // Recent orders
        $recentOrders = Order::where('user_id', $userId)
            ->latest()
            ->limit(5)
            ->get();

        return view('user.dashboard', compact(
            'totalOrders',
            'pendingOrders',
            'approvedOrders',
            'rejectedOrders',
            'waitingPayment',
            'waitingVerification',
            'paidOrders',
            'totalSpend',
            'recentOrders'
        ));
    }
}
