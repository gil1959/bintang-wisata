<?php
namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\{Payment,PaymentGateway};
use App\Services\Payments\{MidtransService,VerifiedPaymentStatus};
use Illuminate\Http\Request;

class MidtransWebhookController extends Controller
{
    public function __invoke(Request $request,MidtransService $midtrans)
    {
        $gateway=PaymentGateway::where('name','midtrans')->first();
        abort_unless($gateway,403,'Gateway unavailable.');
        $payload=$request->all();
        abort_unless($midtrans->verifyWebhookSignature($gateway->credentials??[],$payload),401,'Invalid signature.');
        $reference=$payload['order_id']??'';
        abort_unless(is_string($reference)&&$reference!==''&&is_numeric($payload['gross_amount']??null),422,'Invalid transaction.');
        abort_if(isset($payload['currency'])&&$payload['currency']!=='IDR',422,'Currency mismatch.');
        $payment=Payment::where('gateway_name','midtrans')->where('gateway_reference',$reference)->latest('id')->firstOrFail();
        $transaction=$payload['transaction_status']??'';$fraud=$payload['fraud_status']??null;
        if(in_array($transaction,['capture','settlement'],true))$status=$fraud===null||$fraud==='accept'?'paid':($fraud==='deny'?'failed':'waiting_payment');
        elseif(in_array($transaction,['deny','cancel','expire','failure'],true))$status='failed';
        elseif($transaction==='pending')$status='waiting_payment';
        else return response()->json(['message'=>'ignored']);
        app(VerifiedPaymentStatus::class)->apply($payment,$status,$payload,(float)$payload['gross_amount']);
        return response()->json(['message'=>'ok']);
    }
}
