<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function orders(Request $request)
    {
        abort_unless($request->user()->hasAnyRole(['admin','site_moderator']) && $request->user()->can('admin.dashboard.view'),403);
        $data=$request->validate([
            'from'=>'required|date_format:Y-m-d', 'to'=>'required|date_format:Y-m-d|after_or_equal:from',
            'paid'=>'nullable|boolean','page'=>'nullable|integer|min:1',
            'order_ids'=>'nullable|array|max:500','order_ids.*'=>'integer|min:1',
        ]);
        $query=Order::whereBetween('created_at',[$data['from'].' 00:00:00',$data['to'].' 23:59:59']);
        if($request->boolean('paid')) $query->where('payment_status','paid');
        if(array_key_exists('order_ids',$data)) $query->whereIn('id',$data['order_ids']);
        $summary=['total_orders'=>(clone $query)->count(),'total_amount'=>(float)(clone $query)->sum('final_price')];
        $rows=$query->orderBy('created_at')->orderBy('id')->paginate(100);
        return response()->json(['data'=>$rows->getCollection()->map(fn($order)=>$order->only([
            'id','invoice_number','product_name','customer_name','customer_email','type','payment_status','order_status','final_price','created_at'
        ])),'summary'=>$summary,'meta'=>['page'=>$rows->currentPage(),'last_page'=>$rows->lastPage()]]);
    }
}
