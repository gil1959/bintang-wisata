<?php
namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\{Order,Payment,PaymentGateway};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DokuWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $gateway=PaymentGateway::where('name','doku')->first();
        abort_unless($gateway&&$gateway->is_active,403,'Gateway inactive.');
        $credentials=$gateway->credentials??[];
        $client=(string)$request->header('Client-Id','');
        $requestId=(string)$request->header('Request-Id','');
        $timestamp=(string)$request->header('Request-Timestamp','');
        $secret=trim((string)($credentials['secret_key']??''));
        abort_unless($secret!==''&&$client!==''&&hash_equals(trim((string)($credentials['client_id']??'')),$client)&&$requestId!==''&&$timestamp!=='',401,'Invalid notification signature.');
        // DOKU signs the actual raw body and merchant notification path, not parsed JSON.
        $digest=base64_encode(hash('sha256',$request->getContent(),true));
        $canonical="Client-Id:{$client}\nRequest-Id:{$requestId}\nRequest-Timestamp:{$timestamp}\nRequest-Target:".$request->getPathInfo()."\nDigest:{$digest}";
        $expected='HMACSHA256='.base64_encode(hash_hmac('sha256',$canonical,$secret,true));
        abort_unless(hash_equals($expected,(string)$request->header('Signature','')),401,'Invalid notification signature.');
        $payload=$request->json()->all();
        $invoice=data_get($payload,'order.invoice_number');
        $amount=data_get($payload,'order.amount');
        abort_unless(is_string($invoice)&&$invoice!==''&&is_numeric($amount),422,'Invalid payment notification.');
        $payment=Payment::where('gateway_name','doku')->where('gateway_reference',$invoice)->latest('id')->firstOrFail();
        abort_unless(abs((float)$payment->amount-(float)$amount)<0.01,422,'Amount mismatch.');
        // Checkout allows another payment method after a failed attempt. Do not reject the order.
        if(strtoupper((string)data_get($payload,'transaction.status'))!=='SUCCESS')return response()->json(['message'=>'ignored']);
        $changed=DB::transaction(function()use($payment,$payload,$amount){
            $order=Order::whereKey($payment->order_id)->lockForUpdate()->firstOrFail();
            $locked=Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
            abort_unless(abs((float)$locked->amount-(float)$amount)<0.01,422,'Amount mismatch.');
            if($locked->status==='paid')return null;
            $locked->update(['status'=>'paid','gateway_payload'=>$payload]);
            if($order->payment_status==='paid')return null;
            $order->payment_status='paid';$order->order_status='approved';
            if($order->affiliate_user_id&&$order->affiliate_commission_amount!==null)$order->affiliate_commission_status='approved';
            $order->save();
            return $order;
        },3);
        if($changed)app(\App\Services\OrderNotificationService::class)->sendVerificationEmail($changed,'approved');
        return response()->json(['message'=>'ok']);
    }
}
