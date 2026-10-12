<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Services\Mobile\{ContentForms,WebAction};
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ContentManagementController extends Controller
{
    private function allowed($user,$spec):bool
    {
        return ($user->hasAnyRole(['admin','site_moderator'])&&$user->can('admin.dashboard.view'))||($user->hasRole('partner')&&$spec[2]&&$spec[2]===$user->partner_type);
    }
    private function spec(Request $request,string $type):array
    {
        abort_unless(isset(ContentForms::TYPES[$type]),404);$spec=ContentForms::TYPES[$type];abort_unless($this->allowed($request->user(),$spec),403);
        $spec['area']=$request->user()->hasAnyRole(['admin','site_moderator'])?'Admin':'Partner';return $spec;
    }
    private function query(Request $request,array $spec)
    {
        $class='App\\Models\\'.$spec[0];$q=$class::query();if($spec['area']==='Partner')$q->where('created_by_partner_id',$request->user()->id);
        if($spec[0]==='HomePromoBanner')$q->where('section',$spec[1]==='Banner diskon'?'discount':'missions');return $q;
    }
    private function invoke(Request $request,string $type,array $spec,string $method,$model=null)
    {
        $controller=$spec[0].'Controller';$extra=[];
        if($type==='footer_logos'){$controller='HomeSettingController';$method=['store'=>'storeFooterLogo','update'=>'updateFooterLogo','destroy'=>'destroyFooterLogo'][$method];}
        if($spec[0]==='HomePromoBanner')$extra=['section'=>$type==='discount_banners'?'discount':'missions'];
        return app(WebAction::class)->run($request,'App\\Http\\Controllers\\'.$spec['area'].'\\'.$controller,$method,$model,$extra);
    }
    public function types(Request $request)
    {
        return response()->json(['data'=>collect(ContentForms::TYPES)->filter(fn($spec)=>$this->allowed($request->user(),$spec))->map(fn($spec,$key)=>['key'=>$key,'title'=>$spec[1]])->values()]);
    }
    public function index(Request $request,string $type)
    {
        $spec=$this->spec($request,$type);$request->validate(['q'=>'nullable|string|max:150','page'=>'nullable|integer|min:1']);$q=$this->query($request,$spec);
        if($request->filled('q'))$q->where($spec[3],'like','%'.$request->q.'%');$rows=$q->latest('id')->paginate(20);
        return response()->json(['data'=>$rows->getCollection()->map(fn($row)=>['id'=>$row->id,'title'=>$row->{$spec[3]}?:$spec[1].' '.$row->id,'is_active'=>$row->is_active??$row->is_published??true,'thumbnail_path'=>$row->thumbnail_path??$row->image_path??$row->cover_image,'can_edit'=>$type!=='promos']),'meta'=>['page'=>$rows->currentPage(),'last_page'=>$rows->lastPage(),'total'=>$rows->total()]]);
    }
    public function form(Request $request,string $type,?int $id=null)
    {
        $spec=$this->spec($request,$type);abort_if($type==='promos'&&$id,404);$fields=ContentForms::fields($type,(bool)$id);$data=[];
        if($id){$model=$this->query($request,$spec)->findOrFail($id);$data=$model->only(array_column($fields,'key'));if($type==='articles')$data['tags']=implode(', ',$model->tags??[]);if($type==='documentations'&&preg_match('~^https?://~',$model->file_path)){$data['source']='link';$data['embed_link']=$model->file_path;}}
        foreach($fields as &$field)if(in_array($field['key'],['parent_id','tour_category_id','tour_subcategory_id'])) {
            $q=\App\Models\TourCategory::query();if($spec['area']==='Partner')$q->where('created_by_partner_id',$request->user()->id);
            if($field['key']==='parent_id')$q->whereNull('parent_id')->when($id,fn($query)=>$query->where('id','!=',$id));
            $field['options']=$q->orderBy('name')->get()->map(fn($row)=>['id'=>(string)$row->id,'label'=>$row->name])->all();
        }
        return response()->json(['data'=>['title'=>$spec[1],'fields'=>$fields,'values'=>(object)$data]]);
    }
    public function save(Request $request,string $type,?int $id=null)
    {
        $spec=$this->spec($request,$type);abort_if($type==='promos'&&$id,404);$model=$id?$this->query($request,$spec)->findOrFail($id):null;
        if($request->has('payload')){$request->validate(['payload'=>'required|json']);$payload=json_decode($request->payload,true);abort_unless(is_array($payload),422);$request->replace($payload);}
        $fields=ContentForms::fields($type,(bool)$id);$data=Arr::only($request->input(),array_column($fields,'key'));foreach($data as &$value)if($value==='')$value=null;unset($value);$request->replace($data);
        $this->invoke($request,$type,$spec,$id?'update':'store',$model);
        return response()->json(['message'=>'Data berhasil disimpan.'],$id?200:201);
    }
    public function destroy(Request $request,string $type,int $id)
    {
        $spec=$this->spec($request,$type);$model=$this->query($request,$spec)->findOrFail($id);
        $this->invoke($request,$type,$spec,'destroy',$model);
        return response()->json(['message'=>'Data berhasil dihapus.']);
    }
}
