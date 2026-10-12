<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\Mobile\IdempotentAction;
use Illuminate\Http\Exceptions\HttpResponseException;

class BookingController extends Controller
{
    public function store(Request $request,string $type,string $slug)
    {
        $controllers=[
            'tour'=>'TourOrderController','rent_car'=>'RentCarOrderController','ship'=>'ShipOrderController',
            'hotel'=>'HotelOrderController','restoran'=>'RestoranOrderController','umrah'=>'UmrahOrderController','mice'=>'MiceOrderController',
        ];
        abort_unless(isset($controllers[$type]),404);
        $class=CatalogController::TYPES[$type][0];
        $package=$class::where('slug',$slug)->where('is_active',true)->firstOrFail();
        if($package->created_by_partner_id) abort_unless($package->partner_review_status==='approved',404);
        if($type==='hotel' && $request->filled('room_id')) abort_unless($package->rooms()->whereKey($request->room_id)->exists(),422,'Pilihan kamar tidak tersedia.');
        $request->validate(['idempotency_key'=>'required|uuid','promo_code'=>'nullable|string|max:100']);
        $request->merge(['email'=>$request->user()->email]);
        $request->merge(['request_id'=>$request->input('idempotency_key')]);
        return app(IdempotentAction::class)->run($request,'booking:'.$type.':'.$slug,function()use($request,$controllers,$type,$slug){
            // Resolve codes on the server. A client-supplied promo ID cannot bypass this check.
            $request->merge(['promo_id'=>null]);
            if($request->filled('promo_code')) {
                $promo=\App\Models\Promo::where('code',strtoupper(trim($request->promo_code)))->where('is_active',true)->first();
                abort_unless($promo&&(!$promo->start_date||!now()->startOfDay()->lt($promo->start_date))&&(!$promo->end_date||!now()->startOfDay()->gt($promo->end_date)),422,'Kode promo tidak aktif atau sudah berakhir.');
                abort_if(Order::where('customer_email',$request->user()->email)->where('promo_id',$promo->id)->exists(),422,'Kode promo ini sudah pernah digunakan untuk email ini.');
                $request->merge(['promo_id'=>$promo->id]);
            }
            $response=app('App\\Http\\Controllers\\Front\\'.$controllers[$type])->draft($request,$slug);
            if($response->getStatusCode()>=400) throw new HttpResponseException($response);
            $payload=$response->getData(true);
            $id=(int)basename((string)parse_url($payload['redirect']??'',PHP_URL_PATH));
            $order=Order::where('user_id',$request->user()->id)->findOrFail($id);
            $data=['id'=>$order->id,'invoice_number'=>$order->invoice_number,'subtotal'=>$order->subtotal,'discount'=>$order->discount,'final_price'=>$order->final_price];
            return ['data'=>$data];
        })->setStatusCode(201);
    }
}
