<?php
namespace App\Http\Controllers\Webhook;
use App\Http\Controllers\Controller;
use App\Models\{Payment,PaymentGateway};
use App\Services\Payments\{PayPalService,PayPalSettlement};
use Illuminate\Http\Request;

class PayPalWebhookController extends Controller
{
    public function handle(Request $request,PayPalService $paypal)
    {
        $gateway=PaymentGateway::where('name','paypal')->firstOrFail();
        $event=json_decode($request->getContent(),true);
        abort_unless(is_array($event),422);
        abort_unless($paypal->verifyWebhookSignature($gateway->credentials??[],$request->headers->all(),$event),403,'Invalid signature.');
        if(($event['event_type']??'')!=='PAYMENT.CAPTURE.COMPLETED')return response()->json(['ok'=>true]);
        $capture=$event['resource']??[];
        $id=data_get($capture,'supplementary_data.related_ids.order_id');
        abort_unless(is_string($id)&&$id!=='',422,'Missing PayPal order.');
        $payment=Payment::where('gateway_name','paypal')->where('gateway_reference',$id)->firstOrFail();
        app(PayPalSettlement::class)->capture($payment,$capture,$event);
        return response()->json(['ok'=>true]);
    }
}
