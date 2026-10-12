<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\{Setting,HomePromoBanner,DestinationInspiration,ClientLogo,FooterLogo,Article,PopupWidget};
use Illuminate\Http\Request;

class HomeBlocksController extends Controller
{
    private function image($path) {return !$path?null:(preg_match('~^https?://~i',$path)?$path:asset('storage/'.ltrim($path,'/')));}
    private function link($url) {if(!$url)return null;if(str_starts_with($url,'/')&&!str_starts_with($url,'//'))return url($url);return preg_match('~^https?://~i',$url)?$url:null;}
    public function index(Request $request)
    {
        $blocks=[];
        $highlights=[];
        $defaults=['left1'=>['Harga Transparan','Tanpa biaya tersembunyi'],'left2'=>['Itinerary Jelas','Rute dan waktu terstruktur'],'left3'=>['Booking Cepat','Form ringkas dan jelas'],'left4'=>['Support Aktif','Bisa konsultasi trip'],'right1'=>['Destinasi Favorit','Pilihan destinasi domestik dan internasional sesuai paket'],'right2'=>['Cocok untuk Grup','Perjalanan keluarga, kantor, dan komunitas'],'right3'=>['Budget Friendly','Informasi harga jelas sejak awal'],'right4'=>['Spot Wisata Terbaik','Tempat ikonik dan alur perjalanan nyaman']];
        foreach($defaults as $key=>[$title,$description])$highlights[]=['title'=>Setting::getValue('home_highlight_'.$key.'_title',$title),'description'=>Setting::getValue('home_highlight_'.$key.'_desc',$description)];
        $blocks[]=['kind'=>'info','title'=>Setting::getValue('home_highlight_title','Detail, rapi, dan fokus ke pengalaman perjalanan.'),'description'=>Setting::getValue('home_highlight_desc','Informasi jelas, itinerary enak diikuti, dan tim responsif.'),'items'=>$highlights];
        $tabs=json_decode(Setting::getValue('home_tabs','[]'),true);
        $blocks[]=['kind'=>'links','title'=>'Layanan perjalanan','items'=>collect(is_array($tabs)?$tabs:[])->map(fn($tab)=>['title'=>$tab['label']??'','url'=>$this->link($tab['url']??null),'image_url'=>$this->image($tab['icon_image']??null)])->values()];
        foreach(['discount'=>'Diskon dan penawaran','missions'=>'Pilihan perjalanan'] as $section=>$title) {
            $blocks[]=['kind'=>'banners','title'=>Setting::getValue('home_'.$section.'_banner_title',$title),'items'=>HomePromoBanner::where('section',$section)->where('is_active',true)->orderBy('sort_order')->orderBy('id')->get()->map(fn($row)=>['title'=>$title,'image_url'=>$this->image($row->thumbnail_path),'url'=>$this->link($row->link_url)])];
        }
        $blocks[]=['kind'=>'inspirations','title'=>Setting::getValue('home_inspiration_title','Inspirasi destinasi'),'items'=>DestinationInspiration::where('is_active',true)->orderBy('sort_order')->orderBy('title')->get()->map(fn($row)=>['title'=>$row->title,'image_url'=>$this->image($row->image_path),'category'=>$row->tour_category_id,'subcategory'=>$row->tour_subcategory_id])];
        if(Setting::getValue('home_articles_enabled','0')==='1') {
            $query=Article::published();$ids=json_decode(Setting::getValue('home_articles_custom_ids','[]'),true);$ids=is_array($ids)?array_map('intval',$ids):[];
            $rows=Setting::getValue('home_articles_mode','custom')==='custom'?$query->whereIn('id',$ids)->get()->sortBy(fn($row)=>array_search($row->id,$ids)):$query->orderByDesc('published_at')->limit(8)->get();
            $blocks[]=['kind'=>'articles','title'=>Setting::getValue('home_articles_title','Inspirasi perjalanan'),'items'=>$rows->map(fn($row)=>['title'=>$row->title,'slug'=>$row->slug,'image_url'=>$this->image($row->cover_image)])->values()];
        }
        foreach(['clients'=>ClientLogo::class,'footer'=>FooterLogo::class] as $key=>$class)$blocks[]=['kind'=>'logos','title'=>$key==='clients'?'Dipercaya pelanggan':'Mitra kami','items'=>$class::where('is_active',true)->orderBy('sort_order')->get()->map(fn($row)=>['title'=>$row->name,'image_url'=>$this->image($row->image_path),'url'=>$this->link($row->url)])];
        $partner=[];
        foreach([1=>['Dashboard Partner','Akses halaman khusus partner untuk mengelola layanan.'],2=>['Pengaturan Fleksibel','Kelola akun dan konfigurasi layanan.'],3=>['Ringkas dan Terukur','Pantau aktivitas dan kebutuhan harian.'],4=>['Dukungan Tim','Bantuan untuk kelancaran kerja sama operasional.']] as $key=>[$title,$description])$partner[]=['title'=>Setting::getValue('home_partner_card'.$key.'_title',$title),'description'=>Setting::getValue('home_partner_card'.$key.'_desc',$description)];
        $blocks[]=['kind'=>'info','title'=>Setting::getValue('home_partner_title','Mau jadi Partner Bintang Wisata?'),'description'=>Setting::getValue('home_partner_desc','Kembangkan jangkauan layanan bersama Bintang Wisata.'),'button_text'=>Setting::getValue('home_partner_button_text','Daftar Partner'),'button_url'=>$this->link(Setting::getValue('home_partner_button_url','/partner')),'items'=>$partner];
        $popup=PopupWidget::enabled()->orderBy('id')->first();
        if($popup&&(!$popup->show_on_mobile||($popup->start_at&&now()->lt($popup->start_at))||($popup->end_at&&now()->gt($popup->end_at))))$popup=null;
        if($popup){
            $matches=fn($pattern)=>in_array(trim($pattern),['/','*','/*',''],true);
            if((count($popup->include_paths??[])&&!collect($popup->include_paths)->contains($matches))||collect($popup->exclude_paths??[])->contains($matches))$popup=null;
        }
        $popupData=$popup?$popup->only(['id','title','body_format','body_html','body_text','delay_seconds','frequency','primary_button_text','secondary_button_text']):null;
        if($popup){$popupData['image_url']=$this->image($popup->image_path);foreach(['primary','secondary'] as $button)$popupData[$button.'_button_link']=$this->link($popup->{$button.'_button_link'});}
        return response()->json(['data'=>['blocks'=>$blocks,'popup'=>$popupData]]);
    }
}
