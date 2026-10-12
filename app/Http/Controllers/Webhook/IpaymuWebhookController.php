<?php
namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\{Order,Payment,PaymentGateway};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB,Http};

class IpaymuWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $gateway=PaymentGateway::where('name','ipaymu')->first();
        abort_unless($gateway&&$gateway->is_active,403,'Gateway inactive.');
        $credentials=$gateway->credentials??[];
        $va=trim((string)($credentials['va']??''));$apiKey=trim((string)($credentials['api_key']??''));
        abort_unless($va!==''&&$apiKey!=='',503,'Gateway configuration unavailable.');
        // Read raw payload before Laravel's TrimStrings and ConvertEmptyStringsToNull transforms.
        if($request->isJson())$data=json_decode($request->getContent(),true);
        else parse_str($request->getContent(),$data);
        abort_unless(is_array($data),422,'Invalid callback.');
        $normalized=[];
        foreach($data as $key=>$value){
            if($key==='signature')continue;
            if(in_array($key,['trx_id','status_code','transaction_status_code','paid_off'],true))$normalized[$key]=(int)$value;
            elseif($key==='is_escrow')$normalized[$key]=in_array($value,[true,1,'1','true'],true);
            elseif($key==='additional_info')$normalized[$key]=$value==='[]'?[]:$value;
            else $normalized[$key]=is_array($value)?$value:($value===null?'null':(is_bool($value)?($value?'true':'false'):(string)$value));
        }
        $normalized['additional_info']=$normalized['additional_info']??[];
        ksort($normalized,SORT_STRING);
        $expected=hash_hmac('sha256',json_encode($normalized),$va);
        abort_unless(hash_equals($expected,(string)$request->header('X-Signature','')),401,'Invalid callback signature.');
        $transactionId=$normalized['trx_id']??0;$sid=$normalized['sid']??'';
        abort_unless($transactionId>0&&is_string($sid)&&$sid!=='',422,'Transaction reference is missing.');
        $payment=Payment::where('gateway_name','ipaymu')->where('gateway_reference',$sid)->latest('id')->firstOrFail();
        // The signed callback is only a trigger: verify status, session and amount against iPaymu.
        $base=($credentials['mode']??'sandbox')==='production'?'https://my.ipaymu.com':'https://sandbox.ipaymu.com';
        $body=json_encode(['transactionId'=>(string)$transactionId]);
        $signature=hash_hmac('sha256','POST:'.$va.':'.hash('sha256',$body).':'.$apiKey,$apiKey);
        $response=Http::timeout(15)->withHeaders(['va'=>$va,'signature'=>$signature,'timestamp'=>now()->format('YmdHis')])->withBody($body,'application/json')->post($base.'/api/v2/transaction');
        abort_unless($response->successful()&&(int)$response->json('Status')===200,503,'Transaction verification unavailable.');
        $verified=$response->json('Data');
        abort_unless(is_array($verified)&&(string)($verified['SessionId']??'')===$sid&&(int)($verified['TransactionId']??0)===$transactionId,422,'Transaction reference mismatch.');
        $amount=$verified['Amount']??null;
        abort_unless(is_numeric($amount)&&abs((float)$amount-(float)$payment->amount)<0.01,422,'Amount mismatch.');
        if(!in_array((int)($verified['Status']??0),[1,6],true))return response()->json(['success'=>true,'message'=>'not paid']);
        $changed=DB::transaction(function()use($payment,$verified,$amount){
            $order=Order::whereKey($payment->order_id)->lockForUpdate()->firstOrFail();
            $locked=Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
            abort_unless(abs((float)$locked->amount-(float)$amount)<0.01,422,'Amount mismatch.');
            if($locked->status==='paid')return null;
            $locked->update(['status'=>'paid','gateway_payload'=>$verified]);
            if($order->payment_status==='paid')return null;
            $order->payment_status='paid';$order->order_status='approved';
            if($order->affiliate_user_id&&$order->affiliate_commission_amount!==null)$order->affiliate_commission_status='approved';
            $order->save();return $order;
        },3);
        if($changed)app(\App\Services\OrderNotificationService::class)->sendVerificationEmail($changed,'approved');
        return response()->json(['success'=>true]);
    }
}
