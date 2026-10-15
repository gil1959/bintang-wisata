<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Mobile\{ProductForms,WebAction};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB,Storage};
use Illuminate\Validation\ValidationException;

class ProductManagementController extends Controller
{
    private function spec(Request $request, string $type): array
    {
        abort_unless(isset(ProductForms::TYPES[$type]),404);
        $spec=ProductForms::TYPES[$type];$user=$request->user();
        if ($user->hasAnyRole(['admin','site_moderator'])) {
            abort_unless($user->can('admin.dashboard.view'),403);
            $spec['area']='Admin';
        } else {
            abort_unless($user->hasRole('partner') && $spec[3] && $user->partner_type===$spec[3],403);
            $spec['area']='Partner';
        }
        return $spec;
    }
    private function query(Request $request,array $spec)
    {
        $class='App\\Models\\'.$spec[0];$query=$class::query();
        if ($spec['area']==='Partner') $query->where('created_by_partner_id',$request->user()->id);
        return $query;
    }
    public function types(Request $request)
    {
        $result=[];
        foreach(ProductForms::TYPES as $key=>$spec) {
            $user=$request->user();
            if (($user->hasAnyRole(['admin','site_moderator']) && $user->can('admin.dashboard.view')) || ($user->hasRole('partner') && $spec[3] && $user->partner_type===$spec[3])) $result[]=['key'=>$key,'title'=>$spec[2]];
        }
        return response()->json(['data'=>$result]);
    }
    public function index(Request $request,string $type)
    {
        $spec=$this->spec($request,$type);
        $request->validate(['q'=>'nullable|string|max:150','page'=>'nullable|integer|min:1']);
        $query=$this->query($request,$spec);
        if ($request->filled('q')) $query->where($type==='travel_bus'?'armada_name':'title','like','%'.$request->q.'%');
        $rows=$query->latest('id')->paginate(20);
        return response()->json(['data'=>$rows->getCollection()->map(fn($row)=>array_merge($row->only(['id','title','slug','is_active','partner_review_status','partner_review_note','thumbnail_path']),['title'=>$type==='travel_bus'?$row->armada_name:$row->title,'can_review'=>$spec['area']==='Admin'&&(bool)$row->created_by_partner_id])),'meta'=>['page'=>$rows->currentPage(),'last_page'=>$rows->lastPage(),'total'=>$rows->total()]]);
    }
    public function form(Request $request,string $type,?int $id=null)
    {
        $spec=$this->spec($request,$type);$partner=$spec['area']==='Partner';
        $fields=ProductForms::fields($type,$partner);$data=[];
        if ($id) {
            $model=$this->query($request,$spec)->with($spec[4])->findOrFail($id);
            foreach($fields as $field) {
                $key=$field['key'];if(str_contains($key,'.') || in_array($field['type'],['file','files'])) continue;
                $data[$key]=$model->getAttribute($key);
                if($type==='travel_bus'&&$field['type']==='time')$data[$key]=substr((string)$data[$key],0,5);
            }
            if (in_array($type,['tour','mice'])) $data['tiers']=$model->tiers->groupBy('type')->toArray();
            if ($type==='tour') {
                $data['include_text']=implode("\n",$model->includes??[]);
                $data['exclude_text']=implode("\n",$model->excludes??[]);
                $data['itinerary_text']=$model->itineraries->pluck('title')->implode("\n");
            }
            foreach(['rooms','menus'] as $relation) if(in_array($relation,$spec[4])) {
                $data[$relation]=$model->$relation->map(function($row){$values=$row->toArray();$values['existing_photos']=$row->photos?:array_values(array_filter([$row->photo_path??$row->thumbnail_path]));return $values;})->all();
            }
            $data['photos']=in_array('photos',$spec[4])?$model->photos->map(fn($row)=>['id'=>$row->id,'file_path'=>$type==='travel_bus'?$row->photo_path:$row->file_path])->all():[];
        }
        if ($spec[1]) {
            $class='App\\Models\\'.$spec[1];$categories=$class::query();
            if ($partner) $categories->where(fn($q)=>$q->whereNull('created_by_partner_id')->orWhere('created_by_partner_id',$request->user()->id));
            $options=$categories->orderBy('name')->get()->map(fn($row)=>['id'=>(string)$row->id,'label'=>$row->name,'parent_id'=>$row->parent_id])->all();
            foreach($fields as &$field) if(in_array($field['key'],['category_id','subcategory_id'])) $field['options']=$options;
            unset($field);
        }
        if($type==='travel_bus'&&!$partner) {
            $options=\App\Models\User::role('partner')->orderBy('name')->get()->map(fn($user)=>['id'=>(string)$user->id,'label'=>$user->name])->all();
            foreach($fields as &$field)if($field['key']==='created_by_partner_id')$field['options']=$options;
            unset($field);
        }
        return response()->json(['data'=>['title'=>$spec[2],'fields'=>$fields,'values'=>$data]]);
    }
    public function save(Request $request,string $type,?int $id=null)
    {
        $spec=$this->spec($request,$type);$model=$id?$this->query($request,$spec)->with($spec[4])->findOrFail($id):null;
        if ($request->has('payload')) {
            $request->validate(['payload'=>'required|json']);
            $payload=json_decode($request->input('payload'),true);
            abort_unless(is_array($payload),422);$request->replace($payload);
        }
        // No ownership, review, or arbitrary model attributes may enter the web action.
        $allowed=array_unique(array_map(fn($f)=>explode('.',$f['key'])[0],ProductForms::fields($type,$spec['area']==='Partner')));
        $request->replace(\Illuminate\Support\Arr::only($request->input(),$allowed));
        $clean=function($value) use (&$clean) {
            if(!is_array($value)) return $value===''?null:$value;
            foreach($value as $key=>$child) {
                if($key==='available' && !in_array($child,[true,1,'1'],true)) unset($value[$key]);
                else $value[$key]=$clean($child);
            }
            return $value;
        };
        $request->replace($clean($request->input()));
        $request->validate(['tiers'=>'nullable|array','tiers.domestic'=>'nullable|array','tiers.international'=>'nullable|array','tiers.foreign'=>'nullable|array','rooms'=>'nullable|array','rooms.*'=>'array','menus'=>'nullable|array','menus.*'=>'array','features'=>'nullable|array','features.*'=>'array']);
        if (in_array($type,['tour','mice'])) {
            $groups=$type==='tour'?['domestic','international']:['domestic','foreign'];
            $tierRules=['tiers'=>'nullable|array:'.implode(',',$groups)];
            foreach($groups as $group) $tierRules['tiers.'.$group.'.*']='array';
            $request->validate($tierRules);
        } else $request->validate(['tiers.*'=>'array']);
        if ($spec[1] && $request->filled('category_id') && $spec['area']==='Partner') {
            $category='App\\Models\\'.$spec[1];
            abort_unless($category::whereKey($request->category_id)->where(fn($q)=>$q->whereNull('created_by_partner_id')->orWhere('created_by_partner_id',$request->user()->id))->exists(),422,'Kategori tidak tersedia.');
        }
        $request->validate(['seo_image'=>'nullable|image|max:4096','rooms.*.photos.*'=>'nullable|image|max:3072','menus.*.photos.*'=>'nullable|image|max:3072','rooms.*.price'=>'nullable|numeric|min:0','menus.*.price'=>'nullable|numeric|min:0','rooms.*.available_rooms'=>'nullable|integer|min:0','rooms.*.max_guests'=>'nullable|integer|min:1']);
        if($type==='travel_bus') {
            $request->validate(['departure_time'=>'required|date_format:H:i','arrival_time'=>'required|date_format:H:i','route_details'=>'nullable|array','route_details.*'=>'nullable|string|max:255','facilities'=>'nullable|array','facilities.*'=>'in:tv,reclining_seat,wifi,baggage,usb,ac,snack,toilet']);
            if($request->filled('created_by_partner_id'))abort_unless(\App\Models\User::role('partner')->whereKey($request->created_by_partner_id)->exists(),422,'Pemilik harus akun partner.');
            if($spec['area']==='Admin'&&!$request->boolean('is_active'))$request->request->remove('is_active');
        }
        // Child IDs and stored photos must belong to this exact product.
        foreach(['tiers','rooms','menus'] as $relation) {
            if (!in_array($relation,$spec[4])) continue;
            $rows=$request->input($relation,[])??[];
            if ($relation==='tiers' && in_array($type,['tour','mice'])) $rows=array_merge(...array_values($rows?:[[]]));
            foreach($rows as $row) {
                $child=!empty($row['id']) && $model?$model->$relation->firstWhere('id',(int)$row['id']):null;
                if (!empty($row['id']) && !$child) throw ValidationException::withMessages([$relation=>'Data pilihan tidak dimiliki paket ini.']);
                $photos=$row['existing_photos']??[];
                $owned=$child?($child->photos?:array_values(array_filter([$child->photo_path??$child->thumbnail_path]))):[];
                if (!is_array($photos) || array_diff($photos,$owned)) throw ValidationException::withMessages([$relation=>'Foto tersimpan tidak valid.']);
            }
        }
        DB::transaction(fn()=>app(WebAction::class)->run($request,'App\\Http\\Controllers\\'.$spec['area'].'\\'.$spec[0].'Controller',$id?'update':'store',$model));
        return response()->json(['message'=>$spec['area']==='Partner'?'Paket disimpan dan menunggu review admin.':'Paket berhasil disimpan.'], $id?200:201);
    }
    public function destroy(Request $request,string $type,int $id)
    {
        $spec=$this->spec($request,$type);$model=$this->query($request,$spec)->findOrFail($id);
        app(WebAction::class)->run($request,'App\\Http\\Controllers\\'.$spec['area'].'\\'.$spec[0].'Controller','destroy',$model);
        return response()->json(['message'=>'Paket berhasil dihapus.']);
    }
    public function photo(Request $request,string $type,int $id,int $photo)
    {
        $spec=$this->spec($request,$type);abort_unless(in_array('photos',$spec[4]),404);
        $model=$this->query($request,$spec)->findOrFail($id);$row=$model->photos()->findOrFail($photo);
        Storage::disk('public')->delete($type==='travel_bus'?$row->photo_path:$row->file_path);$row->delete();
        return response()->json(['message'=>'Foto berhasil dihapus.']);
    }
    public function review(Request $request,string $type,int $id,string $action)
    {
        $spec=$this->spec($request,$type);abort_unless($spec['area']==='Admin',403);abort_unless(in_array($action,['approve','reject','disable'])&&in_array($type,['tour','rent_car','ship','hotel','restoran','travel_bus']),404);
        $request->validate(['note'=>'nullable|string|max:2000']);
        DB::transaction(function()use($request,$spec,$type,$id,$action){
            $product=$this->query($request,$spec)->whereNotNull('created_by_partner_id')->lockForUpdate()->findOrFail($id);
            if($type==='travel_bus') {
                $product->update(['is_active'=>$action==='approve','partner_review_status'=>match($action){'approve'=>'approved','reject'=>'rejected',default=>'disabled'},'partner_review_note'=>$request->note,'partner_reviewed_by'=>$request->user()->id,'partner_reviewed_at'=>now()]);
                return;
            }
            app(WebAction::class)->run($request,\App\Http\Controllers\Admin\PartnerProductReviewController::class,$action,null,['type'=>$type==='rent_car'?'rentcar':$type,'id'=>$id]);
        });return response()->json(['message'=>'Status review produk diperbarui.']);
    }
}
