<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Models\{Order,PaymentMethod,PaymentGateway};
use App\Services\OrderExpiryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Cache,DB};
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    private function order(Request $request,$id)
    {
        if((int)$request->attributes->get('mobile_guest_order_id')===(int)$id) {
            $order = Order::findOrFail($id);
            $order->cancelIfExpired();
            return $order;
        }
        $user=$request->user();
        $order = Order::where(fn($q)=>$q->where('user_id',$user->id)->orWhere(fn($g)=>$g->whereNull('user_id')->where('customer_email',$user->email)))->findOrFail($id);
        $order->cancelIfExpired();
        return $order;
    }
    private function editable($order): void
    {
        $order->cancelIfExpired();
        abort_if($order->is_payment_expired || $order->order_status === 'rejected', 410, 'Batas waktu pembayaran (6 jam) telah berakhir. Pesanan otomatis dibatalkan.');
        abort_if(in_array($order->payment_status,['paid','waiting_verification'],true)||in_array($order->order_status,['approved','rejected'],true),409,'Pesanan ini tidak dapat dibayar ulang.');
        abort_if($order->payments()->whereIn('status',['paid','waiting_verification'])->exists(),409,'Pembayaran sedang diverifikasi atau sudah lunas.');
    }
    private function checkRedirect($response): void
    {
        $session=$response instanceof \Illuminate\Http\RedirectResponse ? $response->getSession() : null;
        if($errors=$session?->pull('errors')) throw ValidationException::withMessages($errors->getBag('default')->getMessages());
        if($error=$session?->pull('error')) throw ValidationException::withMessages(['payment_method'=>$error]);
    }
    public function show(Request $request,$id)
    {
        $order=$this->order($request,$id);
        $view=app()->call([app(\App\Http\Controllers\Front\CheckoutController::class),'show'],['orderId'=>$order->id]);
        $data=$view->getData();
        $manual=collect($data['manualMethods'])->map(fn($m)=>['value'=>'manual:'.$m->id,'label'=>$m->bank_name?:$m->method_name,'bank_name'=>$m->bank_name,'account_number'=>$m->account_number,'account_holder'=>$m->account_holder]);
        $orderData = $order->only(['id','invoice_number','product_name','customer_name','customer_phone','type','departure_date','pickup_date','return_date','participants','total_days','order_items','subtotal','discount','final_price','unique_code','payable_amount','payment_method','payment_status','order_status','billing_first_name','billing_last_name','billing_address','billing_country','billing_city','billing_state','billing_postal','billing_phone','created_at']);
        $orderData['payment_deadline'] = $order->payment_deadline?->toIso8601String();
        $orderData['payment_remaining_seconds'] = $order->payment_remaining_seconds;
        $orderData['is_payment_expired'] = $order->is_payment_expired;
        $orderData['payment_expiry_hours'] = OrderExpiryService::EXPIRY_HOURS;
        return response()->json(['data'=>[
            'order'=>$orderData,
            'methods'=>$manual->concat($data['gatewayOptions'])->values(),
            'payments'=>$order->payments()->latest()->get()->map(fn($p)=>$p->only(['id','method','status','amount','payment_url','created_at'])),
        ]]);
    }
    public function process(Request $request,$id)
    {
        return Cache::lock('mobile-checkout:'.$id,60)->block(5,function()use($request,$id){
            $order=$this->order($request,$id);$this->editable($order);
            $raw=(string)$request->input('payment_method');
            if(str_starts_with($raw,'gateway:')) {
                $parts=explode(':',$raw,3);
                abort_unless(count($parts)===3,422,'Metode pembayaran tidak valid.');
                $gateway=PaymentGateway::where('name',$parts[1])->where('is_active',true)->firstOrFail();
                abort_unless(collect($gateway->channels??[])->contains(fn($c)=>($c['channel_code']??null)===$parts[2]&&($c['active']??true)),422,'Metode pembayaran tidak aktif.');
            }
            $response=app(\App\Services\Mobile\WebAction::class)->run($request,\App\Http\Controllers\Front\CheckoutController::class,'process',null,['orderId'=>$order->id]);
            $this->checkRedirect($response);
            $order->refresh();
            return response()->json(['data'=>[
                'id'=>$order->id,
                'payment_method'=>$order->payment_method,
                'payable_amount'=>$order->payable_amount,
                'payment_status'=>$order->payment_status,
                'payment_deadline'=>$order->payment_deadline?->toIso8601String(),
                'payment_remaining_seconds'=>$order->payment_remaining_seconds,
                'is_payment_expired'=>$order->is_payment_expired,
            ]]);
        });
    }
    public function gateway(Request $request,$id)
    {
        return Cache::lock('mobile-checkout:'.$id,60)->block(5,function()use($request,$id){
            $order=$this->order($request,$id);$this->editable($order);
            $parts=explode(':',(string)$order->payment_method,3);
            abort_unless(count($parts)===3&&$parts[0]==='gateway',422,'Pilih payment gateway terlebih dahulu.');
            $gateway=PaymentGateway::where('name',$parts[1])->where('is_active',true)->firstOrFail();
            abort_unless(collect($gateway->channels??[])->contains(fn($c)=>($c['channel_code']??null)===$parts[2]&&($c['active']??true)),422,'Metode pembayaran tidak aktif.');
            $existing=$order->payments()->where('method','gateway')->where('gateway_name',$parts[1])->where('channel_code',$parts[2])->where('status','waiting_payment')->latest()->first();
            if($existing?->payment_url && parse_url($existing->payment_url,PHP_URL_SCHEME)==='https') return response()->json(['data'=>['payment_url'=>$existing->payment_url,'id'=>$order->id]]);
            $response=app(\App\Services\Mobile\WebAction::class)->run($request,\App\Http\Controllers\Front\PaymentController::class,'startGateway',$order);
            $this->checkRedirect($response);
            $url=$response instanceof \Illuminate\Http\RedirectResponse?$response->getTargetUrl():null;
            abort_unless($url && parse_url($url,PHP_URL_SCHEME)==='https' && parse_url($url,PHP_URL_HOST)!==parse_url(config('app.url'),PHP_URL_HOST),502,'Gateway belum memberikan tautan pembayaran.');
            return response()->json(['data'=>['payment_url'=>$url,'id'=>$order->id]]);
        });
    }
    public function proof(Request $request,$id)
    {
        return Cache::lock('mobile-checkout:'.$id,60)->block(5,function()use($request,$id){
            $order=$this->order($request,$id);$this->editable($order);
            abort_unless(str_starts_with((string)$order->payment_method,'manual:'),422,'Pilih transfer manual terlebih dahulu.');
            $response=app(\App\Services\Mobile\WebAction::class)->run($request,\App\Http\Controllers\Front\PaymentController::class,'submitManual',$order);
            $this->checkRedirect($response);
            return response()->json(['message'=>'Bukti transfer diterima dan menunggu verifikasi admin.','data'=>['payment_status'=>$order->fresh()->payment_status]]);
        });
    }
}
