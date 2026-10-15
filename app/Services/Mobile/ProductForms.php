<?php
namespace App\Services\Mobile;

class ProductForms
{
    public const TYPES = [
        'tour'=>['TourPackage','TourCategory','Paket tour','agency_paket_tour',['tiers','itineraries','photos']],
        'rent_car'=>['RentCarPackage','RentCarCategory','Rental mobil','agency_rental_mobil',[]],
        'hotel'=>['HotelPackage',null,'Hotel dan vila','agency_hotel_vila',['rooms','photos']],
        'restoran'=>['RestoranPackage',null,'Restoran','agency_restoran',['menus','photos']],
        'ship'=>['ShipPackage','ShipCategory','Kapal',null,['tiers']],
        'umrah'=>['UmrahPackage','UmrahCategory','Umrah',null,['tiers','photos']],
        'mice'=>['MicePackage','MiceCategory','MICE',null,['tiers','photos']],
        'travel_bus'=>['TravelBusPackage',null,'Bus dan Travel','agency_travel_bus',['photos']],
    ];
    public static function field($key,$label,$type='text',$extra=[]): array
    { return array_merge(compact('key','label','type'),$extra); }
    public static function fields(string $type, bool $partner): array
    {
        $f = fn($key,$label,$kind='text',$extra=[])=>self::field($key,$label,$kind,$extra);
        if($type==='travel_bus') {
            $required=['required'=>true];
            $fields=[$f('armada_name','Nama armada','text',$required),$f('armada_type','Jenis armada','text',$required),$f('travel_type','Kelas layanan','select',['required'=>true,'default'=>'BUSINESS','options'=>array_map(fn($value)=>['id'=>$value,'label'=>$value],['BUSINESS','EXECUTIVE','VIP','SLEEPER','ECONOMY'])])];
            foreach(['departure_location'=>'Lokasi keberangkatan','arrival_location'=>'Lokasi tujuan'] as $key=>$label)$fields[]=$f($key,$label,'text',$required);
            $fields[]=$f('route_details','Titik rute, satu per baris','lines');
            foreach(['departure_time'=>'Jam keberangkatan','arrival_time'=>'Jam tiba'] as $key=>$label)$fields[]=$f($key,$label,'time',$required);
            $fields[]=$f('arrival_days_offset','Selisih hari kedatangan','select',['default'=>'0','options'=>array_map(fn($day)=>['id'=>(string)$day,'label'=>$day===0?'Hari yang sama':$day.' hari setelah keberangkatan'],range(0,14))]);
            $fields[]=$f('duration_text','Teks durasi opsional');
            $fields[]=$f('price','Harga per penumpang','number',$required);
            $facilities=['tv'=>'TV / Audio','reclining_seat'=>'Reclining Seat','wifi'=>'Free WiFi','baggage'=>'Bagasi','usb'=>'USB Charger','ac'=>'AC Penuh','snack'=>'Snack & Minuman','toilet'=>'Toilet'];
            $fields[]=$f('facilities','Fasilitas','multiselect',['options'=>collect($facilities)->map(fn($label,$id)=>compact('id','label'))->values()->all()]);
            $fields[]=$f('description','Deskripsi','multiline');$fields[]=$f('terms_conditions','Syarat dan ketentuan','multiline');
            if(!$partner){$fields[]=$f('created_by_partner_id','Pemilik partner','select');$fields[]=$f('is_active','Aktif','boolean',['default'=>'1']);}
            $fields[]=$f('thumbnail','Foto utama','file');$fields[]=$f('gallery','Tambahkan galeri','files');
            return $fields;
        }
        $repeat = fn($key,$label,$fields)=>$f($key,$label,'repeat',['fields'=>$fields]);
        $fields = [$f('title','Nama paket','text',['required'=>true]),$f('label','Label')];
        if (!in_array($type,['rent_car','hotel','restoran'])) $fields[]=$f('slug','Slug URL','text',['required'=>true]);
        if (self::TYPES[$type][1]) $fields[]=$f('category_id','Kategori','select');
        if ($type==='tour') $fields[]=$f('subcategory_id','Subkategori','select');
        if (!$partner) $fields[]=$f('is_active','Aktif','boolean',['default'=>'1']);
        if (in_array($type,['tour','umrah','mice'])) {
            $fields[]=$f('duration_text','Durasi');$fields[]=$f('destination','Destinasi');
            $fields[]=$f($type==='tour'?'itinerary_text':'itinerary','Itinerary','multiline');
            $fields[]=$f('include_text','Termasuk dalam paket','multiline');
            $fields[]=$f('exclude_text','Tidak termasuk paket','multiline');
        }
        if ($type==='tour') $fields[]=$f('flight_info','Penerbangan','select',['options'=>[['id'=>'included','label'=>'Termasuk'],['id'=>'not_included','label'=>'Tidak termasuk']]]);
        if (in_array($type,['tour','ship','umrah','mice'])) {
            $fields[]=$f('rating_value','Nilai ulasan','number',['default'=>'5']);
            $fields[]=$f('rating_count','Jumlah ulasan','number',['default'=>'0']);
            $tier=[$f('id','','hidden'),$f('label_text','Nama pilihan'),$f('price','Harga','number',['required'=>true])];
            if ($type==='tour') {
                $tier=array_merge($tier,[$f('min_people','Minimal peserta','number',['default'=>'1']),$f('max_people','Maksimal peserta','number'),$f('is_custom','Harga khusus','boolean',['default'=>'0'])]);
            }
            if (in_array($type,['tour','mice'])) {
                foreach (['domestic',$type==='tour'?'international':'foreign'] as $group) {
                    $fields[]=$repeat('tiers.'.$group,$group==='domestic'?'Harga domestik':'Harga internasional',array_merge($tier,[$f('type','','hidden',['default'=>$group])]));
                }
            } else {
                if ($type==='ship') $tier[]=$f('type','Jenis hari','select',['options'=>[['id'=>'weekday','label'=>'Hari biasa'],['id'=>'weekend','label'=>'Akhir pekan']]]);
                $fields[]=$repeat('tiers','Pilihan harga',$tier);
            }
        }
        if ($type==='rent_car') foreach (['price_per_hour'=>'Harga per jam','price_per_12_hours'=>'Harga 12 jam','price_per_24_hours'=>'Harga 24 jam'] as $key=>$label) $fields[]=$f($key,$label,'number');
        if (in_array($type,['hotel','restoran'])) {
            $fields[]=$f($type==='hotel'?'price_per_night':'price_per_pax',$type==='hotel'?'Harga per malam':'Harga per orang','number');
            if ($type==='hotel') $fields[]=$f('property_type','Jenis properti','text',['default'=>'Hotel']);
            foreach (['address'=>'Alamat','maps_url'=>'Tautan Google Maps','cs_contact'=>'Kontak layanan','note'=>'Catatan'] as $key=>$label) $fields[]=$f($key,$label);
            $fields[]=$repeat('nearby_places','Tempat sekitar',[$f('name','Nama tempat'),$f('distance','Jarak')]);
            $fields[]=$f('facilities','Fasilitas, satu per baris','lines');
            $fields[]=$f('keunggulan','Keunggulan, satu per baris','lines');
            $child=[$f('id','','hidden'),$f('name','Nama','text',['required'=>true]),$f('price','Harga','number'),$f('description','Deskripsi','multiline'),$f('is_ready','Tersedia','boolean',['default'=>'1']),$f('existing_photos','Foto tersimpan','photos'),$f('photos','Tambahkan foto','files')];
            if ($type==='hotel') {
                foreach (['room_size'=>'Luas kamar','bed_type'=>'Jenis tempat tidur','max_guests'=>'Maksimal tamu','price_with_breakfast'=>'Harga dengan sarapan','original_price'=>'Harga awal','available_rooms'=>'Jumlah kamar'] as $key=>$label) $child[]=$f($key,$label,in_array($key,['room_size','bed_type'])?'text':'number');
                foreach (['has_shower'=>'Shower','has_wifi'=>'WiFi','has_breakfast'=>'Sarapan'] as $key=>$label) $child[]=$f($key,$label,'boolean');
                $child[]=$f('facilities','Fasilitas kamar, satu per baris','lines');
            } else $child[]=$f('category','Kategori menu');
            $fields[]=$repeat($type==='hotel'?'rooms':'menus',$type==='hotel'?'Kamar':'Menu',$child);
        }
        if (in_array($type,['rent_car','ship','hotel','restoran'])) $fields[]=$repeat('features','Fitur',[$f('name','Nama fitur'),$f('available','Tersedia','checkbox')]);
        $fields[]=$f('long_description','Deskripsi lengkap','multiline');
        $fields[]=$f('thumbnail','Foto utama','file');
        if (in_array('photos',self::TYPES[$type][4])) $fields[]=$f('gallery','Tambahkan galeri','files');
        foreach (['seo_title'=>'Judul SEO','seo_description'=>'Deskripsi SEO','seo_keywords'=>'Kata kunci','social_title'=>'Judul media sosial','social_description'=>'Deskripsi media sosial'] as $key=>$label) $fields[]=$f($key,$label);
        $fields[]=$f('seo_image','Gambar SEO','file');
        return $fields;
    }
}
