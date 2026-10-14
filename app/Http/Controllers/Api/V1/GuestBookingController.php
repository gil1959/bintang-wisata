<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;
class GuestBookingController extends Controller
{
    private function hash(Request $request):string
    {
        $token=(string)$request->header('X-Guest-Token');
        abort_unless(preg_match('/^[a-f0-9]{64}$/',$token),403,'Akses pesanan tamu tidak valid.');
        return hash('sha256',$token);
    }
    public function store(Request $request,string $type,string $slug)
    {
        $controllers=['tour'=>'TourOrderController','rent_car'=>'RentCarOrderController','ship'=>'ShipOrderController','hotel'=>'HotelOrderController','restoran'=>'RestoranOrderController','umrah'=>'UmrahOrderController','mice'=>'MiceOrderController','travel_bus'=>'TravelBusOrderController'];
        abort_unless(isset($controllers[$type]),404);
        $request->validate(['email'=>'required|email|max:255','idempotency_key'=>'required|uuid','promo_code'=>'nullable|string|max:100']);
        $hash=$this->hash($request);$fingerprint=hash('sha256',$type.':'.$slug.':'.json_encode($request->all()));
        return DB::transaction(function()use($request,$type,$slug,$controllers,$hash,$fingerprint){
            DB::table('mobile_guest_bookings')->insertOrIgnore(['token_hash'=>$hash,'fingerprint'=>$fingerprint,'expires_at'=>now()->addDays(30),'created_at'=>now(),'updated_at'=>now()]);
            $entry=DB::table('mobile_guest_bookings')->where('token_hash',$hash)->lockForUpdate()->first();
            abort_unless(hash_equals($entry->fingerprint,$fingerprint),409,'Gunakan permintaan baru untuk data pemesanan yang berbeda.');
            abort_if(now()->gt($entry->expires_at),403,'Akses tamu sudah berakhir.');
            if($entry->order_id)return $this->result(Order::findOrFail($entry->order_id));
            $model=CatalogController::TYPES[$type][0];$product=$model::where('slug',$slug)->where('is_active',true)->firstOrFail();
            abort_if($type!=='hotel'&&$product->created_by_partner_id&&$product->partner_review_status!=='approved',404);
            abort_if($type==='travel_bus'&&$product->partner_review_status!=='approved',404);
            if($type==='hotel'&&$request->filled('room_id'))abort_unless($product->rooms()->whereKey($request->room_id)->exists(),422);
            $request->merge(['promo_id'=>null]);
            if($request->filled('promo_code')){
                $promo=\App\Models\Promo::where('code',strtoupper(trim($request->promo_code)))->where('is_active',true)->first();
                abort_unless($promo&&(!$promo->start_date||!now()->startOfDay()->lt($promo->start_date))&&(!$promo->end_date||!now()->startOfDay()->gt($promo->end_date)),422,'Kode promo tidak aktif.');
                abort_if(Order::where('customer_email',$request->email)->where('promo_id',$promo->id)->exists(),422,'Kode promo sudah digunakan.');
                $request->merge(['promo_id'=>$promo->id]);
            }
            $response=app(\App\Services\Mobile\WebAction::class)->run($request,'App\\Http\\Controllers\\Front\\'.$controllers[$type],'draft',null,['slug'=>$slug]);
            if($response->getStatusCode()>=400)throw new HttpResponseException($response);
            $payload=$response->getData(true);$id=(int)basename((string)parse_url($payload['redirect']??'',PHP_URL_PATH));
            $order=Order::where('customer_email',$request->email)->findOrFail($id);
            // A guest capability grants access to this booking only, never to an email's account.
            $order->user_id=null;$order->save();
            DB::table('mobile_guest_bookings')->where('id',$entry->id)->update(['order_id'=>$order->id]);
            return $this->result($order);
        },3);
    }
    private function result(Order $order){return response()->json(['data'=>$order->only(['id','invoice_number','product_name','subtotal','discount','final_price'])],201);}
    private function authorizeOrder(Request $request,$id=null):Order
    {
        $entry=DB::table('mobile_guest_bookings')->where('token_hash',$this->hash($request))->where('expires_at','>',now())->first();
        abort_unless($entry&&$entry->order_id&&($id===null||(int)$id===(int)$entry->order_id),403);
        $order=Order::findOrFail($entry->order_id);
        $request->attributes->set('mobile_guest_order_id',$order->id);
        return $order;
    }
    public function resume(Request $request){return $this->result($this->authorizeOrder($request))->setStatusCode(200);}
    public function checkout(Request $request,$id,string $action='show')
    {
        $order=$this->authorizeOrder($request,$id);
        abort_unless(in_array($action,['show','process','gateway','proof'],true),404);
        if($action==='process')return DB::transaction(function()use($request,$id,$order){
            $order=Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $request->validate(['billing_first_name'=>'required|string|max:120','billing_last_name'=>'required|string|max:120','billing_country'=>'required|string','billing_address'=>'required|string','billing_city'=>'required|string','billing_state'=>'required|string','billing_postal'=>'required|string|max:20','billing_phone'=>'required|string|max:20','payment_method'=>'required|string']);
            $session=null;
            if(!$order->user_id){
                if(\App\Models\User::where('email',$order->customer_email)->exists())throw \Illuminate\Validation\ValidationException::withMessages(['email'=>'Email sudah terdaftar. Silakan login untuk melanjutkan pembayaran dari menu Pesanan.']);
                $user=\App\Models\User::create(['name'=>trim($request->billing_first_name.' '.$request->billing_last_name),'email'=>$order->customer_email,'password'=>\Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(32)),'phone'=>$request->billing_phone,'address'=>implode(', ',[$request->billing_address,$request->billing_city,$request->billing_state,$request->billing_postal,$request->billing_country]),'email_verified_at'=>now()]);
                $user->assignRole('user');
                $order->update(['user_id'=>$user->id]);
                $session=['token'=>$user->createToken('android-checkout')->plainTextToken,'user'=>['id'=>$user->id,'name'=>$user->name,'email'=>$user->email,'phone'=>$user->phone,'address'=>$user->address,'email_verified'=>true,'roles'=>['user'],'permissions'=>[]]];
            }
            $response=app(CheckoutController::class)->process($request,$id);
            $data=$response->getData(true);
            if($session){$data['data']['session']=$session;$data['data']['account_created']=true;$response->setData($data);}
            return $response;
        });
        return app(CheckoutController::class)->$action($request,$id);
    }
}
