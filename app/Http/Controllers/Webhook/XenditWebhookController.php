<?php
namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\{Payment,PaymentGateway};
use App\Services\Payments\VerifiedPaymentStatus;
use Illuminate\Http\Request;

class XenditWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $gateway=PaymentGateway::where('name','xendit')->firstOrFail();
        $token=(string)($gateway->credentials['callback_token']??'');
        abort_unless($token!=='' && hash_equals($token,(string)$request->header('x-callback-token')),403);
        $data=$request->validate([
            'id'=>'required|string','external_id'=>'required|string','status'=>'required|string',
            'amount'=>'required|numeric|min:0','currency'=>'required|in:IDR','paid_amount'=>'nullable|numeric|min:0',
        ]);
        $payment=Payment::where('gateway_name','xendit')->where('gateway_reference',$data['id'])->firstOrFail();
        abort_unless($payment->order && hash_equals((string)$payment->order->invoice_number,$data['external_id']),422,'Invoice mismatch.');
        abort_unless(abs((float)$payment->amount-(float)$data['amount'])<0.01,422,'Amount mismatch.');
        $status=strtoupper($data['status']);
        if(in_array($status,['PAID','SETTLED'],true)) {
            abort_unless(isset($data['paid_amount']) && (float)$data['paid_amount'] >= (float)$payment->amount,422,'Payment is not fully paid.');
            $next='paid';
        } elseif($status==='EXPIRED') $next='failed';
        else return response()->json(['success'=>true]);
        app(VerifiedPaymentStatus::class)->apply($payment,$next,$data,(float)$data['amount']);
        return response()->json(['success'=>true]);
    }
}
