<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Models\{TourPackage,RentCarPackage,ShipPackage,HotelPackage,RestoranPackage,UmrahPackage,MicePackage};
use Illuminate\Http\Request;
class CatalogController extends Controller
{
    private ?string $bookingContact=null;
    public const TYPES = [
        'tour'=>[TourPackage::class,'Paket Tour',['tiers','itineraries','photos']],
        'rent_car'=>[RentCarPackage::class,'Rental Mobil',[]],
        'ship'=>[ShipPackage::class,'Sewa Kapal',['tiers']],
        'hotel'=>[HotelPackage::class,'Hotel dan Vila',['rooms','photos']],
        'restoran'=>[RestoranPackage::class,'Restoran',['menus','photos']],
        'umrah'=>[UmrahPackage::class,'Paket Umrah',['tiers','photos']],
        'mice'=>[MicePackage::class,'MICE',['tiers','photos']],
        'travel_bus'=>[\App\Models\TravelBusPackage::class,'Bus dan Travel',['photos']],
    ];
    public function types()
    {
        return response()->json(['data'=>collect(self::TYPES)->map(fn($v,$k)=>['key'=>$k,'title'=>$v[1]])->values()]);
    }
    private function query(string $type)
    {
        abort_unless(isset(self::TYPES[$type]),404);
        [$model,,$relations]=self::TYPES[$type];
        $query=$model::query()->where('is_active',true)->with($relations);
        if($type==='travel_bus')$query->where('partner_review_status','approved')->withAvg(['reviews as approved_review_rating'=>fn($q)=>$q->where('status','approved')],'rating')->withCount(['reviews as approved_review_count'=>fn($q)=>$q->where('status','approved')]);
        // HotelController and HotelOrderController publish hotels using is_active.
        // Keep the API aligned when an admin activates an existing partner hotel.
        if ($type!=='hotel' && in_array('created_by_partner_id',(new $model)->getFillable(),true)) {
            $query->where(function($q){
                $q->whereNull('created_by_partner_id')->orWhere('partner_review_status','approved');
            });
        }
        return $query;
    }
    private function item($model,string $type): array
    {
        $fields=['id','slug','title','label','destination','duration_text','address','maps_url','long_description','thumbnail_path','price_per_night','price_per_pax','price_per_hour','price_per_12_hours','price_per_24_hours','includes','excludes','itinerary','include_text','exclude_text','features','facilities','nearby_places','note','keunggulan','flight_info'];
        $data=$model->only($fields);
        $path=(string)$model->thumbnail_path;
        $data['image_url']=$path===''?null:(preg_match('~^https?://~',$path)?$path:asset(str_starts_with($path,'storage/')?$path:'storage/'.ltrim($path,'/')));
        $data['type']=$type;
        if($type==='travel_bus') {
            $data=array_merge($data,$model->only(['armada_name','armada_type','travel_type','departure_location','arrival_location','route_details','departure_time','arrival_time','arrival_days_offset','duration_minutes','duration_text','price','facilities','description','terms_conditions']));
            $data['title']=$model->armada_name;$data['destination']=$model->departure_location.' → '.$model->arrival_location;
            $data['long_description']=$model->description;$data['duration_text']=$model->formatted_duration;
            $data['price_unit']='penumpang';$data['label']=$model->travel_type;
            $labels=['tv'=>'TV / Audio','reclining_seat'=>'Reclining Seat','wifi'=>'Free WiFi','baggage'=>'Bagasi Luas','usb'=>'USB Charger','ac'=>'AC Penuh','snack'=>'Snack & Minuman','toilet'=>'Toilet'];
            $data['facilities']=array_map(fn($key)=>$labels[$key]??$key,$model->facilities??[]);
        }
        $data['rating_value']=is_numeric($model->rating_value)?(float)$model->rating_value:null;
        $data['rating_count']=is_numeric($model->rating_count)?(int)$model->rating_count:null;
        if($type==='travel_bus'){$data['rating_value']=$model->approved_review_rating===null?null:round((float)$model->approved_review_rating,1);$data['rating_count']=(int)$model->approved_review_count;}
        $data['contact_whatsapp']=$this->bookingContact??=preg_replace('/\D+/','',(string)\App\Models\Setting::getValue('footer_whatsapp',''));
        foreach(self::TYPES[$type][2] as $relation) $data[$relation]=$model->$relation->map(function($item){
            return collect($item->toArray())->except(['created_at','updated_at'])->all();
        });
        return $data;
    }
    public function index(Request $request,string $type)
    {
        $data=$request->validate(['q'=>'nullable|string|max:150','page'=>'nullable|integer|min:1','category'=>'nullable|integer|min:1','subcategory'=>'nullable|integer|min:1','sort'=>'nullable|in:latest,oldest,title_asc,price_asc,price_desc']);
        $query=$this->query($type);
        if (!empty($data['q'])) $query->where(function($q)use($data,$type){$term='%'.$data['q'].'%';if($type==='travel_bus'){$q->where('armada_name','like',$term)->orWhere('armada_type','like',$term)->orWhere('departure_location','like',$term)->orWhere('arrival_location','like',$term);}else $q->where('title','like',$term)->orWhere(in_array($type,['tour','mice'])?'destination':($type==='ship'?'label':'slug'),'like',$term);});
        if(!empty($data['category'])) {
            abort_unless(isset(self::CATEGORIES[$type]),422,'Jenis produk tidak memiliki kategori.');
            $category=self::CATEGORIES[$type]::findOrFail($data['category']);
            if($type==='tour')abort_unless($category->parent_id===null,422,'Pilih kategori utama.');
            $query->where('category_id',$category->id);
        }
        if(!empty($data['subcategory'])) {
            abort_unless($type==='tour'&&!empty($data['category']),422,'Pilih kategori utama terlebih dahulu.');
            $child=\App\Models\TourCategory::where('parent_id',$data['category'])->findOrFail($data['subcategory']);
            $query->where('subcategory_id',$child->id);
        }
        $sort=$data['sort']??($type==='tour'?'title_asc':'latest');
        if(in_array($sort,['price_asc','price_desc'])) {
            $columns=['rent_car'=>'price_per_12_hours','hotel'=>'price_per_night','restoran'=>'price_per_pax','travel_bus'=>'price'];
            if($type==='mice'){$query->withMin(['tiers as sort_price'=>fn($q)=>$q->where('type','domestic')],'price');$column='sort_price';}
            else {$column=$columns[$type]??null;abort_unless($column,422,'Urutan harga tidak tersedia untuk jenis produk ini.');}
            $query->orderBy($column,$sort==='price_asc'?'asc':'desc');
        }elseif($sort==='title_asc')$query->orderBy($type==='travel_bus'?'armada_name':'title');
        else $query->orderBy('created_at',$sort==='oldest'?'asc':'desc');
        $items=$query->orderByDesc('id')->paginate(20);
        return response()->json(['data'=>$items->getCollection()->map(fn($item)=>$this->item($item,$type)), 'meta'=>['page'=>$items->currentPage(),'last_page'=>$items->lastPage(),'total'=>$items->total()]]);
    }
    private const CATEGORIES=['tour'=>\App\Models\TourCategory::class,'rent_car'=>\App\Models\RentCarCategory::class,'ship'=>\App\Models\ShipCategory::class,'umrah'=>\App\Models\UmrahCategory::class,'mice'=>\App\Models\MiceCategory::class];
    public function filters(string $type)
    {
        abort_unless(isset(self::TYPES[$type]),404);
        $categories=isset(self::CATEGORIES[$type])?self::CATEGORIES[$type]::orderBy('name')->get()->map(fn($row)=>['id'=>$row->id,'name'=>$row->name,'parent_id'=>$type==='tour'?$row->parent_id:null]):[];
        $sorts=['latest'=>'Terbaru','title_asc'=>'Nama'];
        if($type==='ship')$sorts['oldest']='Terlama';
        if(in_array($type,['rent_car','hotel','restoran','mice','travel_bus']))$sorts+=['price_asc'=>'Harga terendah','price_desc'=>'Harga tertinggi'];
        return response()->json(['data'=>['categories'=>$categories,'sorts'=>collect($sorts)->map(fn($label,$key)=>compact('key','label'))->values(),'default_sort'=>$type==='tour'?'title_asc':'latest']]);
    }
    public function show(string $type,string $slug)
    {
        return response()->json(['data'=>$this->item($this->query($type)->where('slug',$slug)->firstOrFail(),$type)]);
    }
    public function reviews(Request $request,string $type,string $slug)
    {
        $request->validate(['page'=>'nullable|integer|min:1']);
        $model=$this->query($type)->where('slug',$slug)->firstOrFail();
        $query=$model->reviews()->approved();
        $count=(clone $query)->count();$average=(float)(clone $query)->avg('rating');
        $ratings=(clone $query)->selectRaw('rating, COUNT(*) as total')->groupBy('rating')->pluck('total','rating');
        $rows=$query->latest()->paginate(10);
        return response()->json(['data'=>$rows->getCollection()->map(fn($review)=>$review->only(['id','name','rating','comment','created_at'])),'meta'=>['page'=>$rows->currentPage(),'last_page'=>$rows->lastPage(),'count'=>$count,'average'=>$average,'ratings'=>(object)$ratings->all()]]);
    }
    public function review(Request $request,string $type,string $slug)
    {
        $model=$this->query($type)->where('slug',$slug)->firstOrFail();
        $request->replace($request->only(['name','email','rating','comment','website','form_started_at']));
        $request->merge(['reviewable_type'=>$type==='rent_car'?'rent':$type,'reviewable_id'=>$model->id]);
        app(\App\Services\Mobile\WebAction::class)->run($request,\App\Http\Controllers\Front\ReviewController::class,'store');
        return response()->json(['message'=>'Ulasan berhasil dikirim dan menunggu persetujuan admin.'],201);
    }
}
