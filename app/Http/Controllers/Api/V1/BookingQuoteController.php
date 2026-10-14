<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Models\{Order,Promo};
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class BookingQuoteController extends Controller
{
    public function show(Request $request,string $type,string $slug)
    {
        abort_unless(isset(CatalogController::TYPES[$type]),404);
        $model=CatalogController::TYPES[$type][0];
        $relations=match($type){'hotel'=>['rooms'],'restoran'=>['menus'],'rent_car'=>[],default=>['tiers']};
        $product=$model::with($relations)->where('slug',$slug)->where('is_active',true)->firstOrFail();
        abort_if($type!=='hotel'&&$product->created_by_partner_id&&$product->partner_review_status!=='approved',404);
        $request->validate(['promo_code'=>'nullable|string|max:100','email'=>'nullable|email']);
        if(in_array($type,['tour','ship','umrah','mice'])){
            $request->validate(['tier_id'=>'required|integer','participants'=>$type==='ship'?'nullable|integer|min:1|max:9999':('required|integer|min:1|max:'.($type==='umrah'?'999':'9999')),'qty'=>'nullable|integer|min:1|max:999']);
            $tier=$product->tiers->firstWhere('id',(int)$request->tier_id);
            abort_unless($tier,422,'Pilihan harga tidak tersedia.');
            $count=$type==='ship'?(int)$request->input('qty',1):(int)$request->participants;
            if($type==='tour'&&($count<(int)$tier->min_people||($tier->max_people!==null&&$count>(int)$tier->max_people)))throw ValidationException::withMessages(['participants'=>'Jumlah peserta tidak sesuai pilihan paket.']);
            $subtotal=(float)$tier->price*$count;
        }elseif($type==='hotel'){
            $request->validate(['checkin_date'=>'required|date|after_or_equal:tomorrow','checkout_date'=>'required|date|after:checkin_date','room_id'=>'nullable|integer','room_count'=>'required|integer|min:1','with_breakfast'=>'nullable|boolean']);
            $room=$request->filled('room_id')?$product->rooms->firstWhere('id',(int)$request->room_id):null;
            abort_if($request->filled('room_id')&&!$room,422,'Kamar tidak tersedia.');
            $rate=$room?($request->boolean('with_breakfast')&&($room->has_breakfast||$room->price_with_breakfast)?($room->price_with_breakfast?:$room->price+85000):$room->price):$product->price_per_night;
            $subtotal=(float)$rate*Carbon::parse($request->checkin_date)->diffInDays(Carbon::parse($request->checkout_date))*(int)$request->room_count;
        }elseif($type==='rent_car'){
            $request->validate(['pickup_date'=>'required|date','return_date'=>'required|date|after_or_equal:pickup_date','duration_type'=>'required|in:12_hours,24_hours,custom']);
            $hours=max(12,(int)ceil(Carbon::parse($request->pickup_date)->diffInMinutes(Carbon::parse($request->return_date))/60));
            $subtotal=match($request->duration_type){'12_hours'=>(float)$product->price_per_12_hours,'24_hours'=>(float)$product->price_per_24_hours,default=>$hours*round($product->price_per_12_hours/12)};
        }else{
            $request->validate(['menus'=>'required|array|min:1','menus.*.id'=>'required|integer','menus.*.qty'=>'required|integer|min:1|max:999']);
            $subtotal=0;
            foreach($request->menus as $entry){$menu=$product->menus->firstWhere('id',(int)$entry['id']);abort_unless($menu&&$menu->is_ready,422,'Menu tidak tersedia.');$subtotal+=(float)$menu->price*(int)$entry['qty'];}
        }
        $discount=0;$promo=null;
        if($request->filled('promo_code')){
            $promo=Promo::where('code',strtoupper(trim($request->promo_code)))->first();
            abort_unless($promo&&$promo->is_valid_for($subtotal),422,'Promo tidak aktif, sudah berakhir, atau minimum pembelian belum terpenuhi.');
            abort_if($request->filled('email')&&Order::where('customer_email',$request->email)->where('promo_id',$promo->id)->exists(),422,'Promo sudah digunakan untuk email ini.');
            $discount=$promo->calculate_discount($subtotal);
        }
        return response()->json(['data'=>['subtotal'=>$subtotal,'discount'=>$discount,'total'=>max(0,$subtotal-$discount),'promo_code'=>$promo?->code]]);
    }
}
