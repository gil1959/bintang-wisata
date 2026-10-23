<?php

namespace App\Services;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OrderExpiryService
{
    /**
     * Expiry duration in hours.
     */
    public const EXPIRY_HOURS = 6;

    /**
     * Check if an order is unpaid and has exceeded the 6-hour limit, then cancel it.
     *
     * @param Order $order
     * @return bool True if order was expired and cancelled, false otherwise.
     */
    public function cancelIfExpired(Order $order): bool
    {
        // Only orders that are waiting for payment can expire
        if (!in_array($order->payment_status, ['waiting_payment', 'unpaid'], true)) {
            return false;
        }

        // If order was already approved or rejected, no action
        if (in_array($order->order_status, ['approved', 'rejected'], true)) {
            return false;
        }

        if (!$order->created_at) {
            return false;
        }

        $deadline = $order->created_at->copy()->addHours(self::EXPIRY_HOURS);

        if (now()->greaterThanOrEqualTo($deadline)) {
            $order->update([
                'payment_status' => 'failed',
                'order_status'   => 'rejected',
            ]);

            // If affiliate exists, mark commission as cancelled
            if ($order->affiliate_user_id) {
                $order->update([
                    'affiliate_commission_status' => 'cancelled',
                ]);
            }

            // Also update any pending payment attempt
            $order->payments()
                ->whereIn('status', ['waiting_payment', 'unpaid'])
                ->update(['status' => 'failed']);

            Log::info("Order {$order->invoice_number} (ID: {$order->id}) auto-cancelled due to 6-hour payment timeout.");

            return true;
        }

        return false;
    }

    /**
     * Cancel all orders that have remained unpaid for more than 6 hours.
     *
     * @return int Number of orders cancelled.
     */
    public function cancelAllExpiredOrders(): int
    {
        $cutoff = now()->subHours(self::EXPIRY_HOURS);

        $expiredOrders = Order::whereIn('payment_status', ['waiting_payment', 'unpaid'])
            ->whereNotIn('order_status', ['approved', 'rejected'])
            ->where('created_at', '<=', $cutoff)
            ->get();

        $count = 0;
        foreach ($expiredOrders as $order) {
            if ($this->cancelIfExpired($order)) {
                $count++;
            }
        }

        if ($count > 0) {
            Log::info("OrderExpiryService: Cancelled {$count} expired orders.");
        }

        return $count;
    }

    /**
     * Get remaining payment time in seconds for an order.
     *
     * @param Order $order
     * @return int
     */
    public function getRemainingSeconds(Order $order): int
    {
        if (!in_array($order->payment_status, ['waiting_payment', 'unpaid'], true)) {
            return 0;
        }

        if (in_array($order->order_status, ['approved', 'rejected'], true)) {
            return 0;
        }

        if (!$order->created_at) {
            return 0;
        }

        $deadline = $order->created_at->copy()->addHours(self::EXPIRY_HOURS);

        return max(0, (int) now()->diffInSeconds($deadline, false));
    }
}
