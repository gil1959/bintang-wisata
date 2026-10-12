<?php
namespace App\Services\Mobile;
class AdminWorkflows
{
    // Model, web controller, heading, search field, public response attributes, actions.
    public const MODULES=[
        'applications'=>['PartnerApplication','PartnerApplicationController','Pendaftaran partner','name',['name','email','phone','address','reason','identity_type','partner_type','bank_name','bank_account_number','bank_account_holder','status','review_note','submitted_at'],['approve'=>'Setujui','reject'=>'Tolak']],
        'partners'=>['User','PartnerApplicationController','Akun partner','name',['name','email','phone','address','full_address','sub_district','partner_type','partner_tax_percent','partner_bank_name','partner_bank_account_number','partner_bank_account_holder','is_suspended'],['updatePartnerUser'=>'Edit profil','setTax'=>'Ubah pajak','suspend'=>'Suspend','unsuspend'=>'Aktifkan kembali','destroyPartner'=>'Hapus']],
        'affiliate_requests'=>['User','AffiliateApprovalController','Pengajuan afiliasi','name',['name','email','phone','affiliate_status','affiliate_review_note','affiliate_requested_at'],['approve'=>'Setujui','decline'=>'Tolak']],
        'affiliate_users'=>['User','AffiliateUserController','Pengaturan afiliasi user','name',['name','email','is_affiliate','affiliate_status','affiliate_commission_type','affiliate_commission_value'],['update'=>'Simpan pengaturan afiliasi']],
        'affiliate_orders'=>['Order','AffiliateOrderController','Komisi pesanan','invoice_number',['invoice_number','product_name','customer_name','final_price','order_status','payment_status','affiliate_user_id','affiliate_commission_type','affiliate_commission_value','affiliate_commission_amount','affiliate_commission_status'],['setCommission'=>'Ubah komisi']],
        'affiliate_withdrawals'=>['AffiliateWithdrawalRequest','AffiliateWithdrawalController','Pencairan afiliasi','account_name',['user_id','amount','payout_method','payout_provider','account_name','account_number','status','admin_note','paid_at'],['updateStatus'=>'Ubah status']],
        'partner_withdrawals'=>['PartnerWithdrawalRequest','PartnerWithdrawalController','Pencairan partner','email',['partner_id','amount','email','bank_name','account_holder','account_number','status','admin_note','reviewed_at'],['update'=>'Proses pencairan','destroy'=>'Hapus']],
        'savings_accounts'=>['TabunganUmrahAccount','TabunganUmrahAdminController','Akun tabungan Umrah','full_name',['user_id','full_name','whatsapp','saving_type','status','target_amount','target_departure_date','rejected_reason'],['verifyAccount'=>'Verifikasi','rejectAccount'=>'Tolak','suspendAccount'=>'Suspend','unsuspendAccount'=>'Aktifkan kembali','updateAccount'=>'Edit akun','printStatement'=>'Laporan']],
        'savings_deposits'=>['TabunganUmrahDeposit','TabunganUmrahAdminController','Setoran tabungan','status',['user_id','account_id','amount','status','proof_image','note','submitted_at','verified_at'],['approveDeposit'=>'Setujui','rejectDeposit'=>'Tolak']],
        'orders'=>['Order','OrderController','Pengelolaan pesanan','invoice_number',['invoice_number','type','product_name','customer_name','customer_email','customer_phone','booking_date','participants','subtotal','discount','final_price','payable_amount','order_status','payment_status','created_at'],['update'=>'Proses pesanan','destroy'=>'Hapus']],
        'users'=>['User','UserController','Pengguna dan moderator','name',['name','email','phone','address','full_address','sub_district','email_verified_at','is_suspended','partner_type','partner_bank_name','partner_bank_account_number','partner_bank_account_holder'],['update'=>'Edit pengguna','destroy'=>'Hapus']],
        'reviews'=>['Review','ReviewController','Ulasan pelanggan','name',['name','email','rating','comment','status','created_at','reviewable_id','reviewable_type'],['approve'=>'Setujui','reject'=>'Tolak','update'=>'Edit ulasan','destroy'=>'Hapus']],
    ];
    public static function fields(string $module,string $action):array
    {
        $f=fn($key,$label,$type='text',$extra=[])=>ProductForms::field($key,$label,$type,$extra);
        $s=fn($key,$label,$options)=>$f($key,$label,'select',['options'=>collect($options)->map(fn($label,$id)=>['id'=>$id,'label'=>$label])->values()->all()]);
        $note=$f('note','Catatan','multiline');$adminNote=$f('admin_note','Catatan admin','multiline');
        if(in_array($action,['destroy','destroyPartner','suspend','unsuspend','suspendAccount','unsuspendAccount','approveDeposit']))return [];
        if($module==='applications')return $action==='approve'?[$note,$f('tax_percent','Persentase pajak','number')]:[$note];
        if($module==='affiliate_requests')return [$note];
        if($module==='affiliate_users')return [$f('is_affiliate','Afiliasi aktif','boolean'),$s('affiliate_commission_type','Jenis komisi',['percent'=>'Persentase','fixed'=>'Nominal']),$f('affiliate_commission_value','Nilai komisi','number')];
        if($module==='affiliate_orders')return [$s('affiliate_commission_type','Jenis komisi',['percent'=>'Persentase','fixed'=>'Nominal']),$f('affiliate_commission_value','Nilai komisi','number'),$s('affiliate_commission_status','Status komisi',['pending'=>'Menunggu','approved'=>'Disetujui','paid'=>'Dibayar','cancelled'=>'Dibatalkan'])];
        if($module==='affiliate_withdrawals')return [$s('status','Status',['approved'=>'Disetujui','declined'=>'Ditolak','paid'=>'Dibayar']),$adminNote];
        if($module==='partner_withdrawals')return [$s('action','Keputusan',['approve'=>'Setujui','reject'=>'Tolak']),$adminNote];
        if($module==='orders')return [$s('action','Keputusan',['approve'=>'Setujui','reject'=>'Tolak'])];
        if($module==='savings_deposits')return [$note];
        if($module==='savings_accounts') {
            if($action==='printStatement')return [$f('from','Dari tanggal','date'),$f('to','Sampai tanggal','date')];
            if($action==='rejectAccount')return [$f('rejected_reason','Alasan penolakan','multiline')];
            $target=[$f('target_amount','Target tabungan','number'),$f('target_departure_date','Rencana keberangkatan','date')];
            if($action==='verifyAccount')return $target;
            $fields=[];foreach(['user_name'=>'Nama user','user_email'=>'Email user','user_phone'=>'Telepon','user_address'=>'Alamat','user_full_address'=>'Alamat lengkap','user_sub_district'=>'Kecamatan','account_full_name'=>'Nama penabung','account_whatsapp'=>'WhatsApp'] as $key=>$label)$fields[]=$f($key,$label);
            return array_merge($fields,[$f('user_password','Sandi baru','password'),$f('user_password_confirmation','Konfirmasi sandi','password'),$s('account_saving_type','Jenis',['umroh_reguler'=>'Umrah reguler','haji_furoda'=>'Haji Furoda']),$s('account_status','Status',['pending'=>'Menunggu','verified'=>'Terverifikasi','rejected'=>'Ditolak','suspended'=>'Ditangguhkan']),$f('rejected_reason','Alasan penolakan','multiline')],$target);
        }
        if($module==='reviews') {
            if(in_array($action,['approve','reject']))return [];
            if($action==='update')return [$f('comment','Ulasan','multiline'),$f('created_at','Waktu ulasan WIB (YYYY-MM-DDTHH:mm)')];
            return [$s('package_type','Jenis paket',collect(ProductForms::TYPES)->map(fn($v)=>$v[2])->all()),$f('package_id','Nomor paket','number'),$f('name','Nama'),$f('email','Email'),$f('rating','Nilai ulasan','number'),$f('comment','Ulasan','multiline')];
        }
        if($module==='partners'&&$action==='setTax')return [$f('tax_percent','Persentase pajak','number')];
        if(in_array($module,['partners','users'])) {
            $fields=[];foreach(['name'=>'Nama','email'=>'Email','phone'=>'Telepon','address'=>'Alamat','full_address'=>'Alamat lengkap','sub_district'=>'Kecamatan'] as $key=>$label)$fields[]=$f($key,$label);
            $fields[]=$f('password','Sandi','password');$fields[]=$s('role','Role',$module==='partners'?['user'=>'User','partner'=>'Partner']:['user'=>'User','partner'=>'Partner','site_moderator'=>'Moderator']);$fields[]=$f('is_verified','Email terverifikasi','boolean');
            $fields[]=$s('partner_type','Jenis usaha',['agency_paket_tour'=>'Tour','agency_rental_mobil'=>'Rental mobil','agency_restoran'=>'Restoran','agency_hotel_vila'=>'Hotel dan vila']);
            foreach(['partner_bank_name'=>'Nama bank','partner_bank_account_number'=>'Nomor rekening','partner_bank_account_holder'=>'Nama pemilik rekening'] as $key=>$label)$fields[]=$f($key,$label);
            if($module==='partners')$fields[]=$f('partner_tax_percent','Persentase pajak','number');
            else $fields[]=$f('permissions','Izin moderator','multiselect',['options'=>collect(config('admin_permissions',[]))->map(fn($v,$id)=>['id'=>$id,'label'=>$v['label']??$id])->values()->all()]);
            return $fields;
        }
        return [];
    }
}
