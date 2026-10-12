<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Services\Mobile\{AdminWorkflows,WebAction,IdempotentAction};
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class AdminWorkflowController extends Controller
{
    private function guard(Request $request):void {abort_unless($request->user()->hasAnyRole(['admin','site_moderator'])&&$request->user()->can('admin.dashboard.view'),403);}
    private function spec(Request $request,string $module):array {$this->guard($request);abort_unless(isset(AdminWorkflows::MODULES[$module]),404);return AdminWorkflows::MODULES[$module];}
    private function query(string $module,array $spec)
    {
        $class='App\\Models\\'.$spec[0];$q=$class::query();
        if($spec[0]==='User')$q->whereDoesntHave('roles',fn($r)=>$r->where('name','admin'));
        if($module==='partners')$q->role('partner');
        if($module==='affiliate_requests')$q->where('affiliate_status','pending');
        if($module==='affiliate_orders')$q->whereNotNull('affiliate_user_id');
        return $q;
    }
    public function types(Request $request){$this->guard($request);return response()->json(['data'=>collect(AdminWorkflows::MODULES)->map(fn($spec,$key)=>['key'=>$key,'title'=>$spec[2],'create'=>in_array($key,['users','reviews'])])->values()]);}
    public function index(Request $request,string $module)
    {
        $spec=$this->spec($request,$module);$request->validate(['q'=>'nullable|string|max:150','status'=>'nullable|string|max:40','page'=>'nullable|integer|min:1']);$q=$this->query($module,$spec);
        if($request->filled('q'))$q->where($spec[3],'like','%'.$request->q.'%');
        if($request->filled('status')&&in_array('status',$spec[4]))$q->where('status',$request->status);
        $rows=$q->latest('id')->paginate(15);
        return response()->json(['data'=>$rows->getCollection()->map(fn($row)=>['id'=>$row->id,'title'=>$row->{$spec[3]}?:$spec[2].' '.$row->id,'status'=>$row->status??$row->order_status??$row->affiliate_status]),'meta'=>['page'=>$rows->currentPage(),'last_page'=>$rows->lastPage(),'total'=>$rows->total()]]);
    }
    public function show(Request $request,string $module,int $id)
    {
        $spec=$this->spec($request,$module);$model=$this->query($module,$spec)->findOrFail($id);$values=$model->only($spec[4]);$tables=[];
        if($module==='orders')$tables[]=['title'=>'Pembayaran','rows'=>$model->payments()->latest()->get()->map(fn($p)=>$p->only(['id','amount','status','method','proof_image','created_at']))];
        if($module==='savings_accounts'){$values['approved_total']=$model->approved_total;$tables[]=['title'=>'Setoran','rows'=>$model->deposits()->latest()->limit(50)->get()->map(fn($d)=>$d->only(['id','amount','status','proof_image','note','submitted_at']))];}
        $documents=[];
        foreach($this->documents($module,$model) as $key=>$document)if($document['path'])$documents[]=['key'=>$key,'label'=>$document['label'],'path'=>'admin/workflows/'.$module.'/'.$id.'/documents/'.$key,'extension'=>strtolower(pathinfo($document['path'],PATHINFO_EXTENSION))];
        return response()->json(['data'=>['values'=>$values,'actions'=>(object)$spec[5],'tables'=>$tables,'documents'=>$documents]]);
    }
    private function documents(string $module,$model):array
    {
        if($module==='applications')return ['identity'=>['label'=>'Identitas pendaftar','path'=>$model->identity_file_path],'legal'=>['label'=>'Legalitas usaha','path'=>$model->legal_document_path]];
        if($module==='partners')return ['identity'=>['label'=>'Identitas partner','path'=>$model->partner_identity_file_path],'legal'=>['label'=>'Legalitas usaha','path'=>$model->partner_legal_document_path]];
        if($module==='savings_deposits')return ['proof'=>['label'=>'Bukti setoran','path'=>$model->proof_image]];
        if($module==='orders')return $model->payments()->whereNotNull('proof_image')->get()->mapWithKeys(fn($p)=>['payment_'.$p->id=>['label'=>'Bukti pembayaran '.$p->id,'path'=>$p->proof_image]])->all();
        return [];
    }
    public function document(Request $request,string $module,int $id,string $document)
    {
        $spec=$this->spec($request,$module);$model=$this->query($module,$spec)->findOrFail($id);$documents=$this->documents($module,$model);
        abort_unless(isset($documents[$document])&&!empty($documents[$document]['path']),404);
        $disk=\Illuminate\Support\Facades\Storage::disk('public');$path=$documents[$document]['path'];
        abort_if(str_contains($path,'..')||str_contains($path,':')||str_starts_with($path,'/'),404);
        abort_unless($disk->exists($path),404,'Dokumen tidak ditemukan.');
        return $disk->response($path,basename($path),['Cache-Control'=>'private, no-store','X-Content-Type-Options'=>'nosniff']);
    }
    private function actionSpec(Request $request,string $module,string $action,int $id):array
    {
        $spec=$this->spec($request,$module);
        abort_unless(($id>0&&isset($spec[5][$action]))||($id===0&&$action==='store'&&in_array($module,['users','reviews'])),404);return $spec;
    }
    public function form(Request $request,string $module,int $id,string $action)
    {
        $spec=$this->actionSpec($request,$module,$action,$id);$model=$id?$this->query($module,$spec)->findOrFail($id):null;$fields=AdminWorkflows::fields($module,$action);$values=[];
        foreach($fields as $f)$values[$f['key']]=$f['type']==='password'?null:($model?->getAttribute($f['key']));
        if($model instanceof \App\Models\User){$values['role']=$model->getRoleNames()->first();$values['is_verified']=$model->hasVerifiedEmail()?'1':'0';$values['permissions']=$model->getDirectPermissions()->pluck('name');$values['tax_percent']=$model->partner_tax_percent;}
        if($module==='savings_accounts'&&$model){foreach(['name','email','phone','address','full_address','sub_district'] as $key)$values['user_'.$key]=$model->user?->$key;foreach(['full_name','whatsapp','saving_type','status'] as $key)$values['account_'.$key]=$model->$key;}
        if($module==='reviews'&&$model)$values['created_at']=$model->created_at->timezone('Asia/Jakarta')->format('Y-m-d\TH:i');
        if($action==='printStatement'){$values['from']=now()->startOfMonth()->toDateString();$values['to']=now()->toDateString();}
        return response()->json(['data'=>['fields'=>$fields,'values'=>(object)$values,'label'=>$id?$spec[5][$action]:'Tambah '.$spec[2]]]);
    }
    public function action(Request $request,string $module,int $id,string $action)
    {
        $spec=$this->actionSpec($request,$module,$action,$id);
        if(in_array($module,['users','partners'])&&$id&&!$request->user()->hasRole('admin'))abort_if($id===$request->user()->id||\App\Models\User::findOrFail($id)->hasRole('site_moderator'),403);
        if($request->has('payload')){$request->validate(['payload'=>'required|json']);$payload=json_decode($request->payload,true);abort_unless(is_array($payload),422);$request->replace($payload);}
        $fields=AdminWorkflows::fields($module,$action);$data=Arr::only($request->input(),array_merge(['request_id'],array_column($fields,'key')));foreach($data as &$v)if($v==='')$v=null;unset($v);$request->replace($data);
        if(in_array($module,['users','partners'])&&in_array($action,['store','update','updatePartnerUser'])) {
            $request->validate(['role'=>'required|in:user,partner,site_moderator']);
            // A moderator cannot grant new privileges or change another moderator.
            if(!$request->user()->hasRole('admin')){
                abort_if($request->role==='site_moderator'||$request->filled('permissions')||$id===$request->user()->id,403);
                if($id)abort_if(\App\Models\User::findOrFail($id)->hasRole('site_moderator'),403);
            }
        }
        if($action==='printStatement') {
            $request->validate(['from'=>'required|date_format:Y-m-d','to'=>'required|date_format:Y-m-d|after_or_equal:from']);$request->query->add($request->only(['from','to']));
            $model=$this->query($module,$spec)->findOrFail($id);$view=app(WebAction::class)->run($request,'App\\Http\\Controllers\\Admin\\'.$spec[1],$action,$model);$data=$view->getData();
            return response()->json(['message'=>'Laporan tabungan','data'=>['rows'=>$data['rows'],'totals'=>$data['totals'],'period'=>$request->only(['from','to']),'opening_balance'=>$data['openingBalance'],'account'=>$data['account']->only(['id','full_name','saving_type']),'statement_number'=>$data['statementNo']]]);
        }
        return app(IdempotentAction::class)->run($request,'admin:'.$module.':'.$id.':'.$action,function()use($request,$module,$id,$action,$spec){
            $model=$id?$this->query($module,$spec)->lockForUpdate()->findOrFail($id):null;
            if($module==='partner_withdrawals'&&$action==='destroy')abort_if($model->status==='pending',409,'Tolak atau selesaikan pencairan sebelum menghapus agar saldo tetap sesuai.');
            app(WebAction::class)->run($request,'App\\Http\\Controllers\\Admin\\'.$spec[1],$action,$model);
            return ['message'=>'Perubahan berhasil disimpan.'];
        });
    }
}
