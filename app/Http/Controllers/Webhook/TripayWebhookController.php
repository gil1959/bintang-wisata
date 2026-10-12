<?php
namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\{Payment,PaymentGateway};
use App\Services\Payments\VerifiedPaymentStatus;
use Illuminate\Http\Request;

class TripayWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $gateway=PaymentGateway::where('name','tripay')->first();
        $key=(string)($gateway?->credentials['private_key']??'');
        abort_unless($key!=='',503,'Gateway unavailable.');
        abort_unless(hash_equals(hash_hmac('sha256',$request->getContent(),$key),(string)$request->header('X-Callback-Signature','')),403,'Invalid signature.');
        $event=(string)$request->header('X-Callback-Event','');
        if($event!==''&&$event!=='payment_status')return response()->json(['success'=>true]);
        // Official callbacks are flat JSON; accept the former wrapper only after signature validation.
        $data=$request->input('data');if(!is_array($data))$data=$request->all();
        $reference=$data['reference']??null;
        abort_unless(is_string($reference)&&$reference!==''&&(int)($data['is_closed_payment']??0)===1,422,'Invalid closed payment.');
        $payment=Payment::where('gateway_name','tripay')->where('gateway_reference',$reference)->latest('id')->firstOrFail();
        abort_unless(($data['merchant_ref']??null)===$payment->order?->invoice_number,422,'Invoice mismatch.');
        abort_unless(is_numeric($data['total_amount']??null)&&is_numeric($data['fee_customer']??null)&&(float)$data['fee_customer']>=0,422,'Invalid amount.');
        // Customer fees are additional to the merchant order amount.
        $amount=(float)$data['total_amount']-(float)$data['fee_customer'];
        $status=match(strtoupper((string)($data['status']??''))){'PAID'=>'paid','FAILED','EXPIRED'=>'failed',default=>null};
        if($status!==null)app(VerifiedPaymentStatus::class)->apply($payment,$status,$data,$amount);
        return response()->json(['success'=>true]);
    }
}
