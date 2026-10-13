<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\{LegalController,TravelDocumentController};
use App\Models\{Article,Documentation,Setting};
use Illuminate\Http\Request;

class PublicContentController extends Controller
{
    private function locale(Request $request): void
    {
        $data=$request->validate(['locale'=>'nullable|in:id,en']);
        app()->setLocale($data['locale']??'id');
    }
    private function image(?string $path): ?string
    {
        if (!$path) return null;
        return preg_match('~^https?://~i',$path)?$path:asset(str_starts_with(ltrim($path,'/'),'storage/')?ltrim($path,'/'):'storage/'.ltrim($path,'/'));
    }
    private function articleData(Article $article,bool $detail=false): array
    {
        $data=$article->only(['id','slug','title','excerpt','published_at','tags']);
        if ($detail) $data['content']=$article->content;
        if (app()->getLocale()==='en') foreach(['title','excerpt',...($detail?['content']:[])] as $key) $data[$key]=$article->{$key.'_en'}?:$data[$key];
        $data['image_url']=$this->image($article->cover_image);
        return $data;
    }
    public function articles(Request $request)
    {
        $this->locale($request);
        $data=$request->validate(['q'=>'nullable|string|max:150','page'=>'nullable|integer|min:1']);
        $query=Article::published();
        if (!empty($data['q'])) $query->where(app()->getLocale()==='en'?'title_en':'title','like','%'.$data['q'].'%');
        $rows=$query->orderByDesc('published_at')->paginate(12);
        return response()->json(['data'=>$rows->getCollection()->map(fn($row)=>$this->articleData($row)),'meta'=>['page'=>$rows->currentPage(),'last_page'=>$rows->lastPage()]]);
    }
    public function article(Request $request,string $slug)
    {
        $this->locale($request);
        return response()->json(['data'=>$this->articleData(Article::published()->where('slug',$slug)->firstOrFail(),true)]);
    }
    public function page(Request $request,string $page)
    {
        $this->locale($request);
        abort_unless(in_array($page,['privacy','terms','contact','travel','about'],true),404);
        if($page==='about')return $this->about();
        if ($page!=='travel') {
            $data=app(LegalController::class)->$page()->getData();
            return response()->json(['data'=>['title'=>$data['title'],'sections'=>[['title'=>'','html'=>$data['html']]],'contact'=>$this->contactDetails(),'links'=>[]]]);
        }
        $data=app(TravelDocumentController::class)->index()->getData();
        $sections=[['key'=>'hero','title'=>$data['heroTitle'],'html'=>$data['heroDesc']]];
        foreach(['tabPassportTitle'=>'passportHtml','tabVisaTitle'=>'visaHtml','passportPriceTitle'=>'passportPriceHtml','visaPriceTitle'=>'visaPriceHtml','immigrationTitle'=>'immigrationHtml','orderTitle'=>'orderHtml'] as $title=>$html) {
            if (!empty($data[$html])) $sections[]=['key'=>$html,'title'=>$data[$title],'html'=>$data[$html]];
        }
        $links=collect($data['downloads'])->filter(fn($link)=>is_array($link)&&preg_match('~^https?://~i',$link['url']??''))->map(fn($link)=>['label'=>$link['label']??'Unduh dokumen','url'=>$link['url']])->values()->all();
        $phone=preg_replace('/\D/','',$data['orderWa']);
        if ($phone) $links[]=['label'=>'Hubungi layanan dokumen','url'=>'https://wa.me/'.(str_starts_with($phone,'0')?'62'.substr($phone,1):$phone)];
        return response()->json(['data'=>['title'=>$data['pageTitle'],'sections'=>$sections,'links'=>$links]]);
    }
    public function documentations(Request $request)
    {
        $this->locale($request);
        $data=$request->validate(['category'=>'nullable|string|max:80','page'=>'nullable|integer|min:1']);
        $query=Documentation::where('is_active',true);
        if (!empty($data['category'])) $query->where('category',$data['category']);
        $rows=$query->orderBy('sort_order')->orderByDesc('id')->paginate(12);
        return response()->json(['data'=>$rows->getCollection()->map(fn($row)=>['id'=>$row->id,'title'=>app()->getLocale()==='en'?($row->title_en?:$row->title):$row->title,'category'=>$row->category,'type'=>$row->type,'url'=>$row->url]),'meta'=>['page'=>$rows->currentPage(),'last_page'=>$rows->lastPage()]]);
    }
    private function about()
    {
        $sections=[];
        $defaults=[
            'about_hero'=>['Mitra perjalanan yang rapi, transparan, dan berorientasi pada kenyamanan Anda','Bintang Wisata menyediakan layanan perjalanan dan transportasi yang dirancang untuk memudahkan Anda: mulai dari pemilihan paket, penjadwalan, hingga dukungan selama perjalanan. Kami menempatkan transparansi dan ketepatan layanan sebagai standar utama.'],
            'about_values'=>['Prinsip kerja yang kami pegang','Kami membangun layanan yang rapi dan konsisten. Tujuannya sederhana: pengalaman perjalanan yang nyaman dan dapat diandalkan.'],
            'about_value1'=>['Transparansi','Harga, fasilitas, dan ketentuan disampaikan dengan jelas sejak awal.'],
            'about_value2'=>['Ketepatan','Jadwal dan rencana perjalanan disusun realistis sesuai kebutuhan Anda.'],
            'about_value3'=>['Kenyamanan','Kami menjaga detail layanan agar perjalanan terasa lebih ringan.'],
            'about_value4'=>['Responsif','Tim kami memberikan bantuan cepat untuk pertanyaan dan penyesuaian.'],
            'about_flow'=>['Langkah sederhana, hasil yang jelas','Kami menyusun alur layanan agar Anda dapat melakukan pemesanan tanpa kebingungan. Setiap tahap terstruktur dan mudah diikuti.'],
            'about_step1'=>['Pilih layanan','Tentukan paket tour atau rental sesuai kebutuhan.'],
            'about_step2'=>['Konsultasi singkat','Konfirmasi detail itinerary, durasi, dan ketentuan.'],
            'about_step3'=>['Pemesanan','Lengkapi data dan lakukan proses sesuai instruksi.'],
            'about_step4'=>['Perjalanan dimulai','Nikmati perjalanan, tim kami siap membantu bila diperlukan.'],
        ];
        foreach($defaults as $key=>[$title,$description])$sections[]=['key'=>$key,'title'=>Setting::getValue($key.'_title',$title),'html'=>nl2br(e(Setting::getValue($key.'_desc',$description)))];
        return response()->json(['data'=>['title'=>Setting::getValue('about_hero_badge','Tentang Bintang Wisata'),'sections'=>$sections,'contact'=>$this->contactDetails(),'links'=>[]]]);
    }
    private function contactDetails():array
    {
        $phone=preg_replace('/\D+/','',(string)Setting::getValue('footer_whatsapp',''));
        return ['address'=>Setting::getValue('footer_address',''),'phone'=>Setting::getValue('footer_phone',''),'email'=>Setting::getValue('footer_email',''),'whatsapp_url'=>$phone?'https://wa.me/'.(str_starts_with($phone,'0')?'62'.substr($phone,1):$phone):null];
    }
    public function home(Request $request)
    {
        $this->locale($request);
        $hero=['title'=>Setting::getValue('hero_title','Perjalanan Nyaman & Terpercaya'),'description'=>Setting::getValue('hero_subtitle','Kami membantu Anda merencanakan perjalanan dengan layanan profesional dan harga transparan.'),'image_url'=>$this->image(Setting::getValue('hero_image'))];
        $sections=[];
        foreach(['tour'=>'home_promo','ship'=>'home_ship_promo','umrah'=>'home_umrah_promo','mice'=>'home_mice_promo'] as $type=>$prefix){
            if(!filter_var(Setting::getValue($prefix.'_enabled','1'),FILTER_VALIDATE_BOOLEAN))continue;
            $model=CatalogController::TYPES[$type][0];$query=$model::where('is_active',true);
            if(in_array('created_by_partner_id',(new $model)->getFillable(),true))$query->where(fn($q)=>$q->whereNull('created_by_partner_id')->orWhere('partner_review_status','approved'));
            $ids=json_decode(Setting::getValue($prefix.'_custom_ids','[]'),true);$ids=is_array($ids)?array_values(array_filter(array_map('intval',$ids))):[];
            $custom=Setting::getValue($prefix.'_mode','auto')==='custom'&&count($ids)>0;
            $rows=$custom?$query->whereIn('id',$ids)->get()->sortBy(fn($row)=>array_search($row->id,$ids))->take(20):$query->where('label','PROMO')->latest()->limit(20)->get();
            if($rows->count())$sections[]=['title'=>Setting::getValue($prefix.'_title',CatalogController::TYPES[$type][1]),'products'=>$rows->map(fn($row)=>['type'=>$type,'slug'=>$row->slug,'title'=>$row->title,'image_url'=>$this->image($row->thumbnail_path)])->values()];
        }
        return response()->json(['data'=>['hero'=>$hero,'sections'=>$sections]]);
    }
}
