<?php
namespace App\Services\Payments;

use App\Models\{Order,Payment};
use Illuminate\Support\Facades\DB;

/** Called only after the provider signature and transaction identity are verified. */
class VerifiedPaymentStatus
{
    public function apply(Payment $payment,string $status,array $payload,float $amount): void
    {
        abort_unless(in_array($status,['paid','failed','waiting_payment'],true),422);
        $changed=DB::transaction(function()use($payment,$status,$payload,$amount){
            $order=Order::whereKey($payment->order_id)->lockForUpdate()->firstOrFail();
            $locked=Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
            abort_unless(abs((float)$locked->amount-$amount)<0.01,422,'Amount mismatch.');
            if($locked->status==='paid'||($locked->status==='failed'&&$status==='waiting_payment'))return null;
            $locked->update(['status'=>$status,'gateway_payload'=>array_merge((array)$locked->gateway_payload,['notification'=>$payload])]);
            if($order->payment_status==='paid')return null;
            if($status!=='paid'&&$order->payments()->where('id','>',$locked->id)->exists())return null;
            $previous=$order->order_status;
            $order->payment_status=$status;
            if($status==='paid')$order->order_status='approved';
            elseif($status==='failed')$order->order_status='rejected';
            if($order->affiliate_user_id&&$order->affiliate_commission_amount!==null&&$status!=='waiting_payment')$order->affiliate_commission_status=$status==='paid'?'approved':'cancelled';
            $order->save();
            return $previous!==$order->order_status&&$status!=='waiting_payment'?$order:null;
        },3);
        if($changed)app(\App\Services\OrderNotificationService::class)->sendVerificationEmail($changed,$changed->order_status);
    }
}
