<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Models\{Order,TourPackage,RentCarPackage,ShipPackage,HotelPackage,RestoranPackage};
use App\Notifications\MobileVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\{Rule,ValidationException};

class AccountController extends Controller
{
    private function ordersQuery(Request $request)
    {
        $user=$request->user();
        if($user->hasAnyRole(['admin','site_moderator'])) {
            abort_unless($user->can('admin.dashboard.view'),403);
            return Order::query();
        }
        if($user->hasRole('partner')) {
            return Order::where(function($q)use($user){
                foreach(['tour'=>TourPackage::class,'rent_car'=>RentCarPackage::class,'ship'=>ShipPackage::class,'hotel'=>HotelPackage::class,'restoran'=>RestoranPackage::class,'travel_bus'=>\App\Models\TravelBusPackage::class] as $type=>$model) {
                    $q->orWhere(fn($w)=>$w->where('type',$type)->whereIn('product_id',$model::select('id')->where('created_by_partner_id',$user->id)));
                }
            });
        }
        abort_unless($user->hasRole('user')&&$user->hasVerifiedEmail(),403,'Verifikasi email terlebih dahulu.');
        return Order::where(fn($q)=>$q->where('user_id',$user->id)->orWhere(fn($g)=>$g->whereNull('user_id')->where('customer_email',$user->email)));
    }
    private function orderData($order): array
    {
        return $order->only(['id','invoice_number','type','product_name','customer_name','customer_email','customer_phone','departure_date','pickup_date','return_date','participants','total_days','total_hours','order_items','subtotal','discount','unique_code','promo_code','final_price','payable_amount','payment_method','payment_status','order_status','created_at']);
    }
    public function orders(Request $request)
    {
        $data=$request->validate(['q'=>'nullable|string|max:100','page'=>'nullable|integer|min:1','status'=>'nullable|in:pending,approved,rejected','type'=>'nullable|in:tour,rent_car,ship,hotel,restoran,umrah,mice,travel_bus','payment_status'=>'nullable|in:unpaid,pending,waiting_verification,paid,failed,expired']);
        $query=$this->ordersQuery($request);
        foreach(['type','payment_status'] as $field) if(!empty($data[$field])) $query->where($field,$data[$field]);
        if(!empty($data['status'])) $query->where('order_status',$data['status']);
        if(!empty($data['q'])) $query->where(fn($q)=>$q->where('invoice_number','like','%'.$data['q'].'%')->orWhere('product_name','like','%'.$data['q'].'%'));
        $orders=$query->latest()->paginate(20);
        return response()->json(['data'=>$orders->getCollection()->map(fn($order)=>$this->orderData($order)),'meta'=>['page'=>$orders->currentPage(),'last_page'=>$orders->lastPage()]]);
    }
    public function order(Request $request,$id)
    {
        return response()->json(['data'=>$this->orderData($this->ordersQuery($request)->findOrFail($id))]);
    }
    public function invoice(Request $request,$id)
    {
        $order=$this->ordersQuery($request)->findOrFail($id);
        $data=$this->orderData($order);
        $data['payments']=$order->payments()->latest()->get()->map(fn($payment)=>$payment->only(['id','method','amount','status','gateway_name','channel_code','created_at']));
        $data['bank']=null;
        if (str_starts_with((string)$order->payment_method,'manual:')) {
            $bank=\App\Models\PaymentMethod::where('type','manual')->find((int)substr($order->payment_method,7));
            $data['bank']=$bank?$bank->only(['bank_name','account_holder','account_number']):null;
        }
        $data['billing']=$order->only(['billing_first_name','billing_last_name','billing_country','billing_address','billing_city','billing_state','billing_postal','billing_phone']);
        return response()->json(['data'=>$data]);
    }
    public function contact(Request $request,$id)
    {
        abort_unless($request->user()->hasRole('user'),403);
        $order=$this->ordersQuery($request)->findOrFail($id);
        $result=app(\App\Services\Mobile\WebAction::class)->run($request,\App\Http\Controllers\User\OrderController::class,'confirmAdmin',$order);
        $url=$result->getTargetUrl();
        abort_unless(parse_url($url,PHP_URL_SCHEME)==='https'&&in_array(parse_url($url,PHP_URL_HOST),['wa.me','api.whatsapp.com'],true),422,'Tautan kontak tidak valid.');
        return response()->json(['data'=>['url'=>$url]]);
    }
    public function deletePartnerOrder(Request $request,$id)
    {
        abort_unless($request->user()->hasRole('partner'),403);
        \Illuminate\Support\Facades\DB::transaction(function()use($request,$id){
            $order=$this->ordersQuery($request)->lockForUpdate()->findOrFail($id);
            app(\App\Services\Mobile\WebAction::class)->run($request,\App\Http\Controllers\Partner\OrderController::class,'destroy',$order);
        });
        return response()->json(['message'=>'Pesanan berhasil dihapus.']);
    }
    public function dashboard(Request $request)
    {
        $query=$this->ordersQuery($request);
        $paid=(clone $query)->where('payment_status','paid')->where('order_status','approved');
        $series=[];
        for($i=5;$i>=0;$i--){
            $month=now()->startOfMonth()->subMonths($i);
            $series[]=['label'=>$month->format('m/Y'),'amount'=>(float)(clone $paid)->whereBetween('created_at',[$month,$month->copy()->endOfMonth()])->sum('final_price')];
        }
        return response()->json(['data'=>[
            'orders'=>(clone $query)->count(),'pending'=>(clone $query)->where('order_status','pending')->count(),
            'paid_total'=>(float)$paid->sum('final_price'),'monthly'=>$series,
        ]]);
    }
    public function profile(Request $request)
    {
        $user=$request->user();
        $rules=['name'=>'required|string|max:255','email'=>['required','email','max:255',Rule::unique('users')->ignore($user->id)],'phone'=>'nullable|string|max:30','address'=>'nullable|string|max:5000','full_address'=>'nullable|string|max:5000','sub_district'=>'nullable|string|max:120'];
        if($user->hasRole('partner')) $rules+=['partner_bank_name'=>'nullable|string|max:100','partner_bank_account_number'=>'nullable|string|max:50','partner_bank_account_holder'=>'nullable|string|max:100'];
        $data=$request->validate($rules);
        $changed=$data['email']!==$user->email;
        $user->fill($data);
        if($changed) $user->email_verified_at=null;
        $user->save();
        if($changed) $user->notify(new MobileVerifyEmail());
        return response()->json(['data'=>AuthController::account($user),'message'=>'Profil berhasil diperbarui.']);
    }
    public function password(Request $request)
    {
        $data=$request->validate(['current_password'=>'required|string','password'=>'required|string|min:8|confirmed']);
        if(!Hash::check($data['current_password'],$request->user()->password)) throw ValidationException::withMessages(['current_password'=>'Kata sandi lama tidak sesuai.']);
        $request->user()->update(['password'=>Hash::make($data['password'])]);
        $request->user()->tokens()->where('id','!=',$request->user()->currentAccessToken()->id)->delete();
        return response()->json(['message'=>'Kata sandi berhasil diperbarui.']);
    }
    public function notifications(Request $request)
    {
        $items=$request->user()->notifications()->latest()->paginate(20);
        return response()->json(['data'=>$items->getCollection()->map(fn($n)=>['id'=>$n->id,'data'=>$n->data,'read_at'=>$n->read_at,'created_at'=>$n->created_at]),'meta'=>['page'=>$items->currentPage(),'last_page'=>$items->lastPage()]]);
    }
    public function readNotification(Request $request,string $id)
    {
        $request->user()->notifications()->where('id',$id)->firstOrFail()->markAsRead();
        return response()->json(['message'=>'Notifikasi ditandai sudah dibaca.']);
    }
}
