<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Models\{User,Order,AffiliateLink,AffiliateUserCoupon,AffiliateWithdrawalRequest,TabunganUmrahAccount,TabunganUmrahDeposit,PartnerWithdrawalRequest,PaymentMethod,Promo};
use App\Services\Mobile\{ProductForms,WebAction};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB,Cache};
use Illuminate\Validation\ValidationException;

class MemberServicesController extends Controller
{
    private function guard(Request $request,string $module):void
    {
        abort_unless(in_array($module,['affiliate','savings','wallet']),404);
        abort_unless($request->user()->hasRole($module==='wallet'?'partner':'user'),403);
        if($module!=='wallet')abort_unless($request->user()->hasVerifiedEmail(),403,'Verifikasi email terlebih dahulu.');
    }
    private function affiliate($user):void {abort_unless($user->is_affiliate && $user->affiliate_status==='approved',403,'Afiliasi belum disetujui.');}
    private function money($value):string {return 'Rp '.number_format((float)$value,0,',','.');}
    private function table($query,$fields,$title):array
    {
        $rows=$query->latest('id')->paginate(15);
        return ['title'=>$title,'rows'=>$rows->getCollection()->map(fn($row)=>$row->only($fields))->values(),'page'=>$rows->currentPage(),'last_page'=>$rows->lastPage()];
    }
    public function index(Request $request,string $module)
    {
        $this->guard($request,$module);$user=$request->user();$summary=[];$tables=[];$actions=[];
        if($module==='wallet') {
            $summary=['Saldo tersedia'=>$this->money($user->partner_balance_available),'Sedang dicairkan'=>$this->money($user->partner_balance_pending),'Sudah dicairkan'=>$this->money($user->partner_balance_withdrawn),'Pajak'=>($user->partner_tax_percent??0).'%'];
            $tables[]=$this->table(PartnerWithdrawalRequest::where('partner_id',$user->id),['id','amount','email','bank_name','account_number','account_holder','status','admin_note','created_at'],'Riwayat pencairan');$actions=['withdraw'=>'Ajukan pencairan'];
        } elseif($module==='savings') {
            $account=TabunganUmrahAccount::where('user_id',$user->id)->latest()->first();
            $summary=['Status'=>$account->status??'Belum terdaftar'];
            if(!$account || $account->status==='rejected')$actions=['register'=>'Daftar tabungan'];
            if($account){$summary['Nama']=$account->full_name;$summary['Jenis']=$account->saving_type;$summary['Saldo disetujui']=$this->money($account->approved_total);$summary['Target']=$this->money($account->target_amount);if($account->rejected_reason)$summary['Catatan']=$account->rejected_reason;
                $tables[]=$this->table($account->deposits(),['id','amount','status','note','proof_image','submitted_at','verified_at'],'Riwayat setoran');
                if($account->status==='verified')$actions=['deposit'=>'Kirim setoran','statement'=>'Laporan tabungan'];
            }
        } else {
            $summary=['Status'=>$user->affiliate_status?:'Belum mendaftar'];
            if(!$user->is_affiliate || $user->affiliate_status!=='approved') {
                if($user->affiliate_review_note)$summary['Catatan']=$user->affiliate_review_note;
                if($user->affiliate_status!=='pending')$actions=['apply'=>'Daftar afiliasi'];
            }else{
                foreach(['pending'=>'Komisi menunggu','approved'=>'Komisi tersedia','paid'=>'Komisi dibayar'] as $status=>$label)$summary[$label]=$this->money(Order::where('affiliate_user_id',$user->id)->where('affiliate_commission_status',$status)->sum('affiliate_commission_amount'));
                $summary['Klik']=(string)AffiliateLink::where('user_id',$user->id)->sum('clicks');$summary['Konversi']=(string)AffiliateLink::where('user_id',$user->id)->sum('conversions');
                $tables[]=$this->table(AffiliateLink::where('user_id',$user->id),['id','name','product_name','sales_url','checkout_url','clicks','conversions'],'Tautan afiliasi');
                $tables[]=$this->table(AffiliateUserCoupon::where('user_id',$user->id),['id','alias_name','promo_id'],'Kupon saya');
                $tables[]=$this->table(Order::where('affiliate_user_id',$user->id),['id','invoice_number','product_name','affiliate_commission_amount','affiliate_commission_status','order_status'],'Pesanan dan komisi');
                $tables[]=$this->table(AffiliateWithdrawalRequest::where('user_id',$user->id),['id','amount','status','payout_method','payout_provider','account_name','account_number','created_at'],'Riwayat pencairan');
                $actions=['link'=>'Buat tautan','coupon'=>'Tambahkan kupon','withdraw'=>'Cairkan komisi'];
            }
        }
        return response()->json(['data'=>['summary'=>$summary,'tables'=>$tables,'actions'=>(object)$actions]]);
    }
    public function form(Request $request,string $module,string $action)
    {
        $this->guard($request,$module);$user=$request->user();$fields=[];
        $f=fn($key,$label,$type='text',$extra=[])=>ProductForms::field($key,$label,$type,$extra);
        $select=fn($key,$label,$options)=>$f($key,$label,'select',['options'=>collect($options)->map(fn($label,$id)=>['id'=>(string)$id,'label'=>$label])->values()->all()]);
        if($module==='wallet'&&$action==='withdraw')$fields=[$f('amount','Nominal','number'),$f('email','Email'),$f('bank_name','Nama bank'),$f('account_number','Nomor rekening'),$f('account_holder','Pemilik rekening'),$f('password','Sandi akun','password')];
        elseif($module==='savings'&&$action==='register')$fields=[$f('full_name','Nama lengkap'),$f('whatsapp','WhatsApp'),$select('saving_type','Jenis tabungan',['umroh_reguler'=>'Umrah reguler','haji_furoda'=>'Haji Furoda'])];
        elseif($module==='savings'&&$action==='deposit') {
            $methods=PaymentMethod::where('type','manual')->where('is_active',true)->get();
            $fields=[$select('payment_method_id','Rekening tujuan',$methods->mapWithKeys(fn($m)=>[$m->id=>$m->bank_name.' '.$m->account_number.' a.n. '.$m->account_holder])->all()),$f('amount','Nominal setoran','number'),$f('proof_image','Bukti transfer','file')];
        }elseif($module==='savings'&&$action==='statement')$fields=[$f('from','Dari tanggal','date'),$f('to','Sampai tanggal','date')];
        elseif($module==='affiliate') {
            if($action==='apply')$fields=[$f('reason','Alasan bergabung','multiline'),$f('channel','Media promosi')];
            else {
                $this->affiliate($user);
                if($action==='withdraw')$fields=[$f('amount','Nominal komisi yang dicairkan','number'),$select('payout_method','Metode pencairan',['bank'=>'Bank','ewallet'=>'Dompet digital']),$f('payout_provider','Bank atau dompet digital'),$f('account_name','Nama pemilik'),$f('account_number','Nomor rekening')];
                elseif($action==='coupon')$fields=[$select('promo_id','Promo',Promo::where('is_active',true)->pluck('code','id')->all()),$f('alias_name','Nama kupon')];
                elseif($action==='link') {
                    $products=[];foreach(['tour','umrah','rent_car','ship'] as $type){$class='App\\Models\\'.ProductForms::TYPES[$type][0];foreach($class::where('is_active',true)->orderBy('title')->get(['id','title']) as $p)$products[$type.':'.$p->id]=ProductForms::TYPES[$type][2].' • '.$p->title;}
                    $fields=[$f('name','Nama tautan'),$select('product','Paket',$products),$select('user_coupon_id','Kupon saya',AffiliateUserCoupon::where('user_id',$user->id)->pluck('alias_name','id')->all())];
                    foreach(['platform'=>'Platform','platform_id'=>'ID platform','utm_source'=>'Sumber kampanye','utm_medium'=>'Media kampanye','utm_campaign'=>'Nama kampanye','utm_content'=>'Konten kampanye','utm_term'=>'Kata kunci kampanye'] as $key=>$label)$fields[]=$f($key,$label);
                }
            }
        }
        abort_unless($fields,404);
        $values=['email'=>$user->email,'full_name'=>$user->name,'whatsapp'=>$user->phone,'bank_name'=>$user->partner_bank_name,'account_number'=>$user->partner_bank_account_number,'account_holder'=>$user->partner_bank_account_holder,'from'=>now()->startOfMonth()->toDateString(),'to'=>now()->toDateString()];
        return response()->json(['data'=>compact('fields','values')]);
    }
    public function action(Request $request,string $module,string $action)
    {
        $this->guard($request,$module);$user=$request->user();
        if($request->has('payload')){$request->validate(['payload'=>'required|json']);$payload=json_decode($request->payload,true);abort_unless(is_array($payload),422);$request->replace($payload);}
        if($module==='savings'&&$action==='statement') {
            $request->validate(['from'=>'required|date_format:Y-m-d','to'=>'required|date_format:Y-m-d|after_or_equal:from']);
            $request->query->add($request->only(['from','to']));
            $view=app(WebAction::class)->run($request,\App\Http\Controllers\User\TabunganUmrahController::class,'printStatement');$data=$view->getData();
            return response()->json(['message'=>'Laporan tabungan','data'=>['rows'=>$data['rows'],'totals'=>$data['totals'],'period'=>$request->only(['from','to']),'opening_balance'=>$data['openingBalance'],'account'=>$data['account']->only(['id','full_name','saving_type']),'statement_number'=>$data['statementNo']]]);
        }
        $controller=null;$method=null;
        if($module==='wallet'&&$action==='withdraw'){$controller=\App\Http\Controllers\Partner\WithdrawController::class;$method='store';}
        if($module==='savings'){$controller=\App\Http\Controllers\User\TabunganUmrahController::class;$method=['register'=>'storeRegistration','deposit'=>'storeDeposit'][$action]??null;}
        if($module==='affiliate'){
            $controller=\App\Http\Controllers\User\AffiliateController::class;$method=['apply'=>'submitApplication','link'=>'storeLink','coupon'=>'storeUserCoupon','withdraw'=>'submitWithdrawal'][$action]??null;
            if($action!=='apply')$this->affiliate($user);else abort_if($user->is_affiliate&&$user->affiliate_status==='approved',409,'Afiliasi sudah aktif.');
            if($action==='link'){$request->validate(['product'=>'required|regex:/^(tour|umrah|rent_car|ship):[0-9]+$/']);[$type,$id]=explode(':',$request->product);$request->merge(['product_type'=>$type,'product_id'=>$id]);}
        }
        abort_unless($controller&&$method,404);
        return app(\App\Services\Mobile\IdempotentAction::class)->run($request,$module.':'.$action,function()use($request,$user,$module,$action,$controller,$method){
            // Serialize requests per user, including balance and registration checks.
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            if($module==='savings'&&$action==='register')abort_if(TabunganUmrahAccount::where('user_id',$user->id)->whereIn('status',['pending','verified','suspended'])->exists(),409,'Akun tabungan sudah terdaftar.');
            app(WebAction::class)->run($request,$controller,$method);
            return ['message'=>'Permintaan berhasil disimpan.'];
        });
    }
    public function deleteWithdrawal(Request $request,int $id)
    {
        $this->guard($request,'wallet');$row=PartnerWithdrawalRequest::where('partner_id',$request->user()->id)->findOrFail($id);
        app(WebAction::class)->run($request,\App\Http\Controllers\Partner\WithdrawController::class,'destroy',$row);
        return response()->json(['message'=>'Permintaan pencairan dihapus.']);
    }
}
