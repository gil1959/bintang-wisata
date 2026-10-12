<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\Mobile\{ProductForms,WebAction};
use Illuminate\Http\Request;
class SiteSettingsController extends Controller
{
    public const MODULES=[
        'general'=>['Pengaturan umum','SettingController','saveGeneral','admin.dashboard.view'],
        'home'=>['Tampilan beranda','HomeSettingController','update','admin.dashboard.view'],
        'home_promos'=>['Paket promo beranda','HomePromoToursController','update','admin.dashboard.view'],
        'legal'=>['Privasi, ketentuan dan kontak','LegalPagesController','update','admin.dashboard.view'],
        'travel'=>['Dokumen perjalanan','TravelDocumentPageController','update','admin.legal-pages.manage'],
        'seo'=>['SEO situs','SeoController','update','admin.dashboard.view'],
        'popup'=>['Popup promosi','PopupWidgetController','update','admin.dashboard.view'],
        'notifications'=>['Kirim notifikasi','NotificationController','store','admin.notifications.manage'],
        'unique_code'=>['Kode unik pembayaran','PaymentController','updateUniqueCodeSetting','admin.dashboard.view'],
        'bank_add'=>['Tambah rekening pembayaran','PaymentController','addBank','admin.dashboard.view'],
        'bank_delete'=>['Hapus rekening pembayaran','PaymentController','deleteBank','admin.dashboard.view'],
        'gateway_tripay'=>['Payment gateway TriPay','PaymentController','toggleGateway','admin.dashboard.view'],
        'gateway_doku'=>['Payment gateway DOKU','PaymentController','toggleGateway','admin.dashboard.view'],
        'gateway_midtrans'=>['Payment gateway Midtrans','PaymentController','toggleGateway','admin.dashboard.view'],
        'gateway_xendit'=>['Payment gateway Xendit','PaymentController','toggleGateway','admin.dashboard.view'],
        'gateway_ipaymu'=>['Payment gateway iPaymu','PaymentController','toggleGateway','admin.dashboard.view'],
        'gateway_paypal'=>['Payment gateway PayPal','PaymentController','toggleGateway','admin.dashboard.view'],
        'system'=>['Bersihkan cache situs','SystemController','clearCache','admin.dashboard.view'],
    ];
    private const CREDENTIALS=['tripay'=>['api_key','private_key','merchant_code'],'doku'=>['client_id','secret_key'],'midtrans'=>['server_key','client_key'],'xendit'=>['secret_key','callback_token'],'ipaymu'=>['va','api_key'],'paypal'=>['client_id','client_secret','webhook_id']];
    private function spec(Request $request,string $module):array {
        abort_unless(isset(self::MODULES[$module]),404);$spec=self::MODULES[$module];abort_unless($request->user()->hasAnyRole(['admin','site_moderator'])&&$request->user()->can($spec[3]),403);return $spec;
    }
    public function types(Request $request){abort_unless($request->user()->hasAnyRole(['admin','site_moderator']),403);return response()->json(['data'=>collect(self::MODULES)->filter(fn($s)=>$request->user()->can($s[3]))->map(fn($s,$key)=>['key'=>$key,'title'=>$s[0]])->values()]);}
    private function fields(string $module):array
    {
        $f=fn($key,$label,$type='text',$extra=[])=>ProductForms::field($key,$label,$type,$extra);
        $fields=json_decode(file_get_contents(resource_path('mobile/settings-forms.json')),true)[$module]??[];
        if($module==='system')return [];
        if($module==='bank_add')return [$f('bank_name','Nama bank'),$f('account_number','Nomor rekening'),$f('account_holder','Pemilik rekening'),$f('swift_code','Kode SWIFT')];
        if($module==='bank_delete')return [$f('bank','Rekening','select',['options'=>\App\Models\PaymentMethod::where('type','manual')->get()->map(fn($m)=>['id'=>(string)$m->id,'label'=>$m->bank_name.' '.$m->account_number.' '.$m->account_holder])->all()])];
        if(str_starts_with($module,'gateway_')){
            $name=substr($module,8);$fields=[$f('enable','Aktif','boolean'),$f('mode','Mode','select',['options'=>[['id'=>'sandbox','label'=>'Sandbox'],['id'=>'production','label'=>'Production']]])];
            foreach(self::CREDENTIALS[$name] as $key)$fields[]=$f($key,ucwords(str_replace('_',' ',$key)).' (kosongkan untuk mempertahankan)','password');return $fields;
        }
        if($module==='unique_code')return [$f('manual_unique_code_min','Kode unik minimal','number'),$f('manual_unique_code_max','Kode unik maksimal','number')];
        if($module==='notifications')return [$f('target_role','Penerima','select',['options'=>[['id'=>'user','label'=>'User'],['id'=>'partner','label'=>'Partner']]]),$f('send_mode','Kirim kepada','select',['options'=>[['id'=>'all','label'=>'Semua'],['id'=>'selected','label'=>'Pilihan']]]),$f('recipient_ids','Pilih penerima','multiselect',['options'=>\App\Models\User::role(['user','partner'])->orderBy('name')->get(['id','name','email'])->map(fn($u)=>['id'=>(string)$u->id,'label'=>$u->name.' ('.$u->email.')'])->all()]),$f('message','Pesan','multiline')];
        if($module==='general'&&!in_array('site_logo',array_column($fields,'key')))$fields[]=$f('site_logo','Logo situs','file');
        foreach($fields as &$field){
            $key=$field['key'];
            if($module==='popup'&&in_array($key,['is_enabled','show_on_mobile','show_on_desktop']))$field['type']='boolean';
            if($module==='home'&&$key==='tabs')$field=$f('tabs','Menu beranda','repeat',['fields'=>[$f('label','Nama menu'),$f('icon','Nama ikon'),$f('url','Tautan'),$f('icon_image','Gambar ikon','file'),$f('icon_image_existing','','hidden')]]);
            if($key==='home_articles_custom_ids')$field=$f($key,'Pilih artikel','multiselect',['options'=>\App\Models\Article::orderBy('title')->get(['id','title'])->map(fn($p)=>['id'=>(string)$p->id,'label'=>$p->title])->all()]);
            if($module==='home_promos'&&str_ends_with($key,'_mode'))$field=$f($key,$field['label'],'select',['options'=>[['id'=>'auto','label'=>'Otomatis'],['id'=>'custom','label'=>'Pilihan sendiri']]]);
            if($module==='home_promos'&&str_ends_with($key,'_custom_ids')){
                $type=str_contains($key,'ship')?'ship':(str_contains($key,'umrah')?'umrah':(str_contains($key,'mice')?'mice':'tour'));$class='App\\Models\\'.ProductForms::TYPES[$type][0];
                $field=$f($key,'Pilih '.ProductForms::TYPES[$type][2],'multiselect',['options'=>$class::where('is_active',true)->where('label','PROMO')->orderBy('title')->get(['id','title'])->map(fn($p)=>['id'=>(string)$p->id,'label'=>$p->title])->all()]);
            }
        }unset($field);
        return $fields;
    }
    public function form(Request $request,string $module)
    {
        $spec=$this->spec($request,$module);$fields=$this->fields($module);$values=Setting::whereIn('key',array_column($fields,'key'))->pluck('value','key')->all();
        if(str_starts_with($module,'gateway_')){$name=substr($module,8);$gateway=\App\Models\PaymentGateway::where('name',$name)->first();$values=['enable'=>$gateway?->is_active?'1':'0','mode'=>$gateway?->credentials['mode']??'sandbox'];}
        if($module==='popup'){
            $widget=app(WebAction::class)->run($request,\App\Http\Controllers\Admin\PopupWidgetController::class,'edit')->getData()['widget'];$values=$widget->only(array_column($fields,'key'));$values['include_paths_text']=implode("\n",$widget->include_paths??[]);$values['exclude_paths_text']=implode("\n",$widget->exclude_paths??[]);
        }
        if($module==='home'){$values['tabs']=json_decode(Setting::getValue('home_tabs','[]'),true)?:[];foreach($values['tabs'] as &$tab)$tab['icon_image_existing']=$tab['icon_image']??null;unset($tab);}
        foreach($fields as $field)if($field['type']==='multiselect'&&isset($values[$field['key']]))$values[$field['key']]=array_map('strval',json_decode($values[$field['key']],true)?:[]);
        if($module==='travel'){$downloads=json_decode(Setting::getValue('travel_docs_downloads','[]'),true)?:[];$values['download_label']=array_column($downloads,'label');$values['download_url']=array_column($downloads,'url');}
        return response()->json(['data'=>['title'=>$spec[0],'fields'=>$fields,'values'=>(object)$values]]);
    }
    public function save(Request $request,string $module)
    {
        $spec=$this->spec($request,$module);$fields=$this->fields($module);
        if($request->has('payload')){$request->validate(['payload'=>'required|json']);$payload=json_decode($request->payload,true);abort_unless(is_array($payload),422);$request->replace($payload);}
        $data=\Illuminate\Support\Arr::only($request->input(),array_column($fields,'key'));foreach($data as &$v)if($v==='')$v=null;unset($v);$request->replace($data);
        if($module==='home')$request->merge(['home_articles_custom_ids'=>json_encode($request->input('home_articles_custom_ids',[]))]);
        if($module==='general')$request->validate(['site_logo'=>'nullable|image|max:2048']);
        $extra=[];
        if($module==='bank_delete'){$request->validate(['bank'=>'required|integer']);$extra['bank']=$request->bank;}
        if(str_starts_with($module,'gateway_')){
            $name=substr($module,8);$gateway=\App\Models\PaymentGateway::firstOrCreate(['name'=>$name],['label'=>strtoupper($name),'is_active'=>false]);
            foreach(self::CREDENTIALS[$name] as $key)if(!$request->filled($key))$request->merge([$key=>$gateway->credentials[$key]??'']);$extra['id']=$gateway->id;
        }
        app(WebAction::class)->run($request,'App\\Http\\Controllers\\Admin\\'.$spec[1],$spec[2],null,$extra);
        return response()->json(['message'=>$module==='notifications'?'Notifikasi berhasil dikirim.':'Pengaturan berhasil disimpan.']);
    }
}
