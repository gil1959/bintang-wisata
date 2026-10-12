<?php
namespace App\Services\Mobile;
class ContentForms
{
    public const TYPES=[
        'tour_categories'=>['TourCategory','Kategori tour','agency_paket_tour','name'],
        'rent_car_categories'=>['RentCarCategory','Kategori mobil','agency_rental_mobil','name'],
        'ship_categories'=>['ShipCategory','Kategori kapal',null,'name'],
        'umrah_categories'=>['UmrahCategory','Kategori Umrah',null,'name'],
        'mice_categories'=>['MiceCategory','Kategori MICE',null,'name'],
        'articles'=>['Article','Artikel',null,'title'],
        'logos'=>['ClientLogo','Logo klien',null,'name'],
        'inspirations'=>['DestinationInspiration','Inspirasi destinasi',null,'title'],
        'documentations'=>['Documentation','Dokumentasi',null,'title'],
        'promos'=>['Promo','Kode promo',null,'code'],
        'footer_logos'=>['FooterLogo','Logo footer',null,'name'],
        'discount_banners'=>['HomePromoBanner','Banner diskon',null,'link_url'],
        'mission_banners'=>['HomePromoBanner','Banner misi',null,'link_url'],
    ];
    public static function fields(string $type,bool $editing):array
    {
        $f=fn($key,$label,$kind='text',$extra=[])=>ProductForms::field($key,$label,$kind,$extra);
        $select=fn($key,$label,$options)=>$f($key,$label,'select',['options'=>collect($options)->map(fn($label,$id)=>compact('id','label'))->values()->all()]);
        if(in_array($type,['discount_banners','mission_banners']))return [$f('thumbnail','Gambar banner','file'),$f('link_url','Tautan tujuan'),$f('sort_order','Urutan','number'),$f('is_active','Aktif','boolean')];
        if($type==='footer_logos')return [$f('name','Nama logo'),$f('image','Gambar logo','file'),$f('url','Tautan'),$f('sort_order','Urutan','number'),$f('is_active','Aktif','boolean')];
        if(str_ends_with($type,'categories')) {
            $fields=[$f('name','Nama kategori'),$f('slug','Slug URL')];
            if($type==='tour_categories') $fields[]=$f('parent_id','Kategori induk','select');
            return $fields;
        }
        if($type==='promos')return [$f('code','Kode promo'),$select('type','Jenis diskon',['percentage'=>'Persentase','nominal'=>'Nominal']),$f('value','Nilai diskon','number')];
        $fields=[$f($type==='logos'?'name':'title','Nama')];
        if($type==='articles') {
            $fields=array_merge($fields,[$f('excerpt','Ringkasan','multiline'),$f('content','Isi artikel','multiline'),$f('tags','Tag, pisahkan dengan koma'),$f('cover_image','Gambar sampul','file'),$f('is_published','Publikasikan','boolean'),$f('ads_code','Kode iklan','multiline')]);
            foreach(['seo_title'=>'Judul SEO','seo_description'=>'Deskripsi SEO','seo_keywords'=>'Kata kunci','social_title'=>'Judul media sosial','social_description'=>'Deskripsi media sosial'] as $key=>$label)$fields[]=$f($key,$label);
            $fields[]=$f('seo_image','Gambar SEO','file');return $fields;
        }
        if($type==='logos')$fields=array_merge($fields,[$f('logo','Logo','file'),$f('url','Tautan')]);
        if($type==='inspirations')$fields=array_merge($fields,[$f('image','Gambar','file'),$f('tour_category_id','Kategori tour','select'),$f('tour_subcategory_id','Subkategori tour','select')]);
        if($type==='documentations') {
            if(!$editing)$fields=array_merge($fields,[$select('category','Kategori',['tour'=>'Tour','ship'=>'Kapal','umrah'=>'Umrah']),$select('type','Jenis',['photo'=>'Foto','video'=>'Video'])]);
            $fields[]=$select('source','Sumber',['upload'=>'Upload','link'=>'Tautan']);
            $fields[]=$f($editing?'replace_file':'files','Upload dokumentasi',$editing?'file':'files',['mime'=>['image/jpeg','image/png','video/mp4']]);
            $fields[]=$f($editing?'embed_link':'embed_links','Tautan dokumentasi','multiline');
        }
        return array_merge($fields,[$f('sort_order','Urutan','number',['default'=>'0']),$f('is_active','Aktif','boolean',['default'=>'1'])]);
    }
}
