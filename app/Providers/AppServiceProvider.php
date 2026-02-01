<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\PopupWidget;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::before(function ($user, $ability) {
    if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
        return true;
    }
    return null;
});

        View::composer('*', function ($view) {

            $keys = [
                'hero_title',
                'hero_subtitle',
                'hero_image',
                'seo_site_title',
                'seo_meta_description',
                'seo_keywords',
                 'home_tabs',
// ship packages page
'ship_hero_badge',
'ship_hero_title',
'ship_hero_desc',
'ship_tips_title',
'ship_tips_desc',
'ship_tip1_title',
'ship_tip1_desc',
'ship_tip2_title',
'ship_tip2_desc',
'ship_tip3_title',
'ship_tip3_desc',
'ship_tip4_title',
'ship_tip4_desc',

// umrah packages page
'umrah_hero_badge',
'umrah_hero_title',
'umrah_hero_desc',
'umrah_filter_dest_label',
'umrah_filter_cat_label',
'umrah_filter_dur_label',
'umrah_filter_trans_label',
'umrah_tips_title',
'umrah_tips_desc',
'umrah_tip1_title',
'umrah_tip1_desc',
'umrah_tip2_title',
'umrah_tip2_desc',
'umrah_tip3_title',
'umrah_tip3_desc',
'umrah_tip4_title',
'umrah_tip4_desc',
'docs_ship_hero_badge','docs_ship_hero_title','docs_ship_hero_desc',
'docs_umrah_hero_badge','docs_umrah_hero_title','docs_umrah_hero_desc',

                // footer
                'footer_address',
                'footer_phone',
                'footer_email',
                'footer_whatsapp',
                // about meta + hero
                'about_meta_title',
                'about_hero_badge',
                'about_hero_title',
                'about_hero_desc',

                // about values
                'about_values_label',
                'about_values_title',
                'about_values_desc',
                'about_value1_title',
                'about_value1_desc',
                'about_value2_title',
                'about_value2_desc',
                'about_value3_title',
                'about_value3_desc',
                'about_value4_title',
                'about_value4_desc',

                // about flow/steps
                'about_flow_label',
                'about_flow_title',
                'about_flow_desc',
                'about_step1_title',
                'about_step1_desc',
                'about_step2_title',
                'about_step2_desc',
                'about_step3_title',
                'about_step3_desc',
                'about_step4_title',
                'about_step4_desc',

                'home_highlight_label',
                'home_highlight_title',
                'home_highlight_desc',
                'home_highlight_left1_title',
                'home_highlight_left1_desc',
                'home_highlight_left2_title',
                'home_highlight_left2_desc',
                'home_highlight_left3_title',
                'home_highlight_left3_desc',
                'home_highlight_left4_title',
                'home_highlight_left4_desc',
                'home_highlight_right1_title',
                'home_highlight_right1_desc',
                'home_highlight_right2_title',
                'home_highlight_right2_desc',
                'home_highlight_right3_title',
                'home_highlight_right3_desc',
                'home_highlight_right4_title',
                'home_highlight_right4_desc',
                'home_highlight_cta_primary_text',
                'home_highlight_cta_secondary_text',

                // HOME: why choose (Mengapa Memilih ...)
                'home_why_label',
                'home_why_title',
                'home_why_desc',
                'home_why1_title',
                'home_why1_desc',
                'home_why2_title',
                'home_why2_desc',
                'home_why3_title',
                'home_why3_desc',
                'home_why4_title',
                'home_why4_desc',

                // HOME: booking flow (Cara Booking ...)
                'home_flow_label',
                'home_flow_title',
                'home_flow_desc',
                'home_flow1_title',
                'home_flow1_desc',
                'home_flow2_title',
                'home_flow2_desc',
                'home_flow3_title',
                'home_flow3_desc',
                'home_flow4_title',
                'home_flow4_desc',
                // footer - konten
                'footer_tagline',
                'footer_quick_links_title',
                'footer_link1_label',
                'footer_link1_url',
                'footer_link2_label',
                'footer_link2_url',
                'footer_link3_label',
                'footer_link3_url',
                'footer_link4_label',
                'footer_link4_url',
                'footer_copyright',

                // tour packages page
                'tour_hero_badge',
                'tour_hero_title',
                'tour_hero_desc',
                'tour_filter_dest_label',
                'tour_filter_cat_label',
                'tour_filter_dur_label',
                'tour_filter_trans_label',
                'tour_tips_title',
                'tour_tips_desc',
                'tour_tip1_title',
                'tour_tip1_desc',
                'tour_tip2_title',
                'tour_tip2_desc',
                'tour_tip3_title',
                'tour_tip3_desc',
                'tour_tip4_title',
                'tour_tip4_desc',
                'tour_cta_title',
                'tour_cta_desc',
                'tour_cta_button',

                // rentcar page
                'rentcar_hero_badge',
                'rentcar_hero_title',
                'rentcar_hero_desc',
                'rentcar_chip1',
                'rentcar_chip2',
                'rentcar_chip3',
                'rentcar_chip4',
                'rentcar_note_title',
                'rentcar_note_desc',
                'rentcar_note1_title',
                'rentcar_note1_desc',
                'rentcar_note2_title',
                'rentcar_note2_desc',
                'rentcar_note3_title',
                'rentcar_note3_desc',
                'rentcar_note4_title',
                'rentcar_note4_desc',
                'home_discount_banner_title',
'home_mission_banner_title',

// HOME: logos header
'home_logos_badge',
'home_logos_title',
'home_logos_desc',
'home_search_title',
'home_search_desc',
'home_search_hint',
// HOME: final CTA
'home_final_cta_title',
'home_final_cta_desc',
'home_final_cta_primary_text',
'home_final_cta_primary_url',
'home_final_cta_secondary_text',
'home_final_cta_secondary_url',

// HOME: partner CTA
'home_partner_badge',
'home_partner_title',
'home_partner_desc',
'home_partner_button_text',
'home_partner_button_url',
'home_partner_card1_title','home_partner_card1_desc',
'home_partner_card2_title','home_partner_card2_desc',
'home_partner_card3_title','home_partner_card3_desc',
'home_partner_card4_title','home_partner_card4_desc',

// MICE hero + tips
'mice_hero_badge',
'mice_hero_title',
'mice_hero_desc',
'mice_cta_button',
'mice_tip1_title','mice_tip1_desc',
'mice_tip2_title','mice_tip2_desc',
'mice_tip3_title','mice_tip3_desc',
'mice_tip4_title','mice_tip4_desc',

                // docs page
                'docs_hero_badge',
                'docs_hero_title',
                'docs_hero_desc',
                'docs_tab_photos',
                'docs_tab_videos',
                'docs_stat_photos',
                'docs_stat_videos',
                'docs_hint',
                'site_logo',
                'tour_cta_secondary_button',
                'tour_cta_secondary_link',
                'tracking_head',
'tracking_body',
                // HOME: promo tours
                'home_promo_enabled' => $settings['home_promo_enabled'] ?? '1',
                'home_promo_badge'   => $settings['home_promo_badge'] ?? 'PROMO',
                'home_promo_title'   => $settings['home_promo_title'] ?? 'Paket Tour Promo',
                'home_promo_desc'    => $settings['home_promo_desc'] ?? '',
                'home_promo_mode'    => $settings['home_promo_mode'] ?? 'auto',
                'home_promo_custom_ids' => $settings['home_promo_custom_ids'] ?? '[]',


            ];

            $settings = Setting::whereIn('key', $keys)->pluck('value', 'key');
$rawHomeTabs = $settings['home_tabs'] ?? null;
$decodedHomeTabs = is_string($rawHomeTabs) ? json_decode($rawHomeTabs, true) : null;

$homeTabs = [];
if (is_array($decodedHomeTabs)) {
    foreach ($decodedHomeTabs as $t) {
        if (!is_array($t)) continue;

        $label = trim((string)($t['label'] ?? ''));
        $url   = trim((string)($t['url'] ?? ''));
        $icon  = trim((string)($t['icon'] ?? ''));

        // minimal requirement
        if ($label === '' || $url === '') continue;

        $iconImage = trim((string)($t['icon_image'] ?? ''));

$homeTabs[] = [
    'label' => $label,
    'url'   => $url,
    'icon'  => $icon !== '' ? $icon : 'sparkles',
    'icon_image' => $iconImage, // path relatif storage/public
];
    }
}

// fallback default (kalau DB kosong/invalid)
if (count($homeTabs) === 0) {
   $homeTabs = [
    ['label' => 'To Do',            'icon' => 'clipboard-check', 'icon_image' => '', 'url' => route('tours.index')],
    ['label' => 'Jemputan Bandara', 'icon' => 'plane',           'icon_image' => '', 'url' => '#'],
    ['label' => 'Ferry',            'icon' => 'ship',            'icon_image' => '', 'url' => '#'],
    ['label' => 'Travel',           'icon' => 'bus',             'icon_image' => '', 'url' => '#'],
    ['label' => 'Sewa Mobil',       'icon' => 'car',             'icon_image' => '', 'url' => route('rentcar.index')],
];
}

            $view->with('siteSettings', [
                'home_tabs' => $homeTabs,
                // HERO
                'hero_title' => $settings['hero_title'] ?? 'Paket Tour Spesial untuk Liburan Tak Terlupakan!',
                'hero_subtitle' => $settings['hero_subtitle'] ?? 'Liburan Tanpa Batas! Jelajahi Destinasi Impian dengan Paket Tour Kami',
                'hero_image' => $settings['hero_image'] ?? '/images/hero-default.jpg',
                'seo_site_title' => $settings['seo_site_title'] ?? 'Bintang Wisata - Tour & Travel Terpercaya',
                'seo_meta_description' => $settings['seo_meta_description'] ?? '',
                'seo_keywords' => $settings['seo_keywords'] ?? '',
                'tour_cta_secondary_button' => $settings['tour_cta_secondary_button'] ?? 'Lihat Rental',
                'tour_cta_secondary_link'   => $settings['tour_cta_secondary_link'] ?? route('rentcar.index'),
// SHIP PACKAGES PAGE
'ship_hero_badge' => $settings['ship_hero_badge'] ?? 'Sewa Kapal',
'ship_hero_title' => $settings['ship_hero_title'] ?? 'Temukan Paket Sewa Kapal yang Sesuai Kebutuhan Anda',
'ship_hero_desc'  => $settings['ship_hero_desc'] ?? 'Gunakan pencarian dan filter kategori untuk menyaring paket yang tersedia.',
// HOME: banner titles
'home_discount_banner_title' => $settings['home_discount_banner_title'] ?? 'Discount up to 50% + instant cashback',
'home_mission_banner_title'  => $settings['home_mission_banner_title'] ?? 'Earn up to IDR 850K from missions',

'ship_tips_title' => $settings['ship_tips_title'] ?? 'Tips Cepat',
'ship_tips_desc'  => $settings['ship_tips_desc'] ?? 'Cek detail paket untuk harga weekday/weekend & fitur yang tersedia.',
'ship_tip1_title' => $settings['ship_tip1_title'] ?? 'Weekday/Weekend',
'ship_tip1_desc'  => $settings['ship_tip1_desc'] ?? 'Harga berbeda sesuai hari',
'ship_tip2_title' => $settings['ship_tip2_title'] ?? 'Untuk Grup',
'ship_tip2_desc'  => $settings['ship_tip2_desc'] ?? 'Cocok keluarga/rombongan',
'ship_tip3_title' => $settings['ship_tip3_title'] ?? 'Rekomendasi',
'ship_tip3_desc'  => $settings['ship_tip3_desc'] ?? 'Paket favorit pelanggan',
'ship_tip4_title' => $settings['ship_tip4_title'] ?? 'Support',
'ship_tip4_desc'  => $settings['ship_tip4_desc'] ?? 'Bisa konsultasi sebelum booking',

// HOME: logos header
'home_logos_badge' => $settings['home_logos_badge'] ?? 'Kepercayaan pelanggan',
'home_logos_title' => $settings['home_logos_title'] ?? 'Kepercayaan Pelanggan Bintang Wisata',
'home_logos_desc'  => $settings['home_logos_desc']  ?? 'Brand dan institusi yang telah mempercayakan perjalanan bersama kami',
// HOME: hero search box (Cari Paket Wisata)
'home_search_title' => $settings['home_search_title'] ?? 'Cari Paket Wisata',
'home_search_desc'  => $settings['home_search_desc'] ?? 'Temukan paket sesuai destinasi, kategori, dan tanggal keberangkatan.',
'home_search_hint'  => $settings['home_search_hint'] ?? 'Pakai kata kunci yang spesifik agar hasil lebih relevan.',

// HOME: final CTA
'home_final_cta_title'          => $settings['home_final_cta_title'] ?? 'Rencanakan Perjalanan Anda Sekarang',
'home_final_cta_desc'           => $settings['home_final_cta_desc'] ?? 'Hubungi tim kami untuk mendapatkan rekomendasi perjalanan terbaik sesuai kebutuhan Anda.',
'home_final_cta_primary_text'   => $settings['home_final_cta_primary_text'] ?? 'Lihat Paket Tour',
'home_final_cta_primary_url'    => $settings['home_final_cta_primary_url'] ?? '',
'home_final_cta_secondary_text' => $settings['home_final_cta_secondary_text'] ?? 'Konsultasi Perjalanan',
'home_final_cta_secondary_url'  => $settings['home_final_cta_secondary_url'] ?? '#',

// HOME: partner CTA
'home_partner_badge'       => $settings['home_partner_badge'] ?? 'Program Partner',
'home_partner_title'       => $settings['home_partner_title'] ?? 'Mau jadi Partner Bintang Wisata?',
'home_partner_desc'        => $settings['home_partner_desc'] ?? 'Kembangkan jangkauan layanan kamu bersama Bintang Wisata. Dapatkan akses dashboard khusus partner untuk kebutuhan operasional.',
'home_partner_button_text' => $settings['home_partner_button_text'] ?? 'Daftar Partner',
'home_partner_button_url'  => $settings['home_partner_button_url'] ?? '',

'home_partner_card1_title' => $settings['home_partner_card1_title'] ?? 'Dashboard Partner',
'home_partner_card1_desc'  => $settings['home_partner_card1_desc']  ?? 'Akses halaman khusus partner untuk mengelola kebutuhan operasional.',
'home_partner_card2_title' => $settings['home_partner_card2_title'] ?? 'Pengaturan Fleksibel',
'home_partner_card2_desc'  => $settings['home_partner_card2_desc']  ?? 'Data akun partner dan konfigurasi layanan dapat dikelola dengan rapi.',
'home_partner_card3_title' => $settings['home_partner_card3_title'] ?? 'Ringkas & Terukur',
'home_partner_card3_desc'  => $settings['home_partner_card3_desc']  ?? 'Memudahkan pemantauan aktivitas dan pengelolaan kebutuhan harian.',
'home_partner_card4_title' => $settings['home_partner_card4_title'] ?? 'Dukungan Tim',
'home_partner_card4_desc'  => $settings['home_partner_card4_desc']  ?? 'Tim kami siap membantu untuk kelancaran kerja sama operasional.',

// MICE: hero + tips
'mice_hero_badge' => $settings['mice_hero_badge'] ?? 'Paket MICE',
'mice_hero_title' => $settings['mice_hero_title'] ?? 'Solusi Paket MICE untuk Event Perusahaan Anda',
'mice_hero_desc'  => $settings['mice_hero_desc']  ?? 'Meetings, Incentives, Conferences, and Exhibitions. Pilih paket, lihat detail, dan lanjut checkout dengan mudah.',
'mice_cta_button' => $settings['mice_cta_button'] ?? 'Lihat Paket',

'mice_tip1_title' => $settings['mice_tip1_title'] ?? 'Event Ready',
'mice_tip1_desc'  => $settings['mice_tip1_desc']  ?? 'Paket siap untuk meeting, conference, dan exhibition.',
'mice_tip2_title' => $settings['mice_tip2_title'] ?? 'Terpercaya',
'mice_tip2_desc'  => $settings['mice_tip2_desc']  ?? 'Pilihan paket jelas, detail lengkap, mudah dipilih.',
'mice_tip3_title' => $settings['mice_tip3_title'] ?? 'Harga Fleksibel',
'mice_tip3_desc'  => $settings['mice_tip3_desc']  ?? 'Tier harga Domestik & WNA bisa multi baris sesuai kebutuhan.',
'mice_tip4_title' => $settings['mice_tip4_title'] ?? 'Support',
'mice_tip4_desc'  => $settings['mice_tip4_desc']  ?? 'Bisa konsultasi kebutuhan event dan itinerary.',

// UMRAH PACKAGES PAGE
'umrah_hero_badge' => $settings['umrah_hero_badge'] ?? 'Paket Umrah',
'umrah_hero_title' => $settings['umrah_hero_title'] ?? 'Temukan Paket Umrah yang Sesuai Kebutuhan Anda',
'umrah_hero_desc'  => $settings['umrah_hero_desc'] ?? 'Gunakan pencarian dan filter untuk menyaring paket berdasarkan destinasi maupun kategori.',

'umrah_filter_dest_label'  => $settings['umrah_filter_dest_label'] ?? 'Destinasi',
'umrah_filter_cat_label'   => $settings['umrah_filter_cat_label'] ?? 'Kategori',
'umrah_filter_dur_label'   => $settings['umrah_filter_dur_label'] ?? 'Durasi',
'umrah_filter_trans_label' => $settings['umrah_filter_trans_label'] ?? 'Transparan',

'umrah_tips_title' => $settings['umrah_tips_title'] ?? 'Tips Cepat',
'umrah_tips_desc'  => $settings['umrah_tips_desc'] ?? 'Gunakan kata kunci destinasi untuk hasil lebih akurat.',
'umrah_tip1_title' => $settings['umrah_tip1_title'] ?? 'Rekomendasi',
'umrah_tip1_desc'  => $settings['umrah_tip1_desc'] ?? 'Paket favorit pelanggan',
'umrah_tip2_title' => $settings['umrah_tip2_title'] ?? 'Itinerary',
'umrah_tip2_desc'  => $settings['umrah_tip2_desc'] ?? 'Alur perjalanan jelas',
'umrah_tip3_title' => $settings['umrah_tip3_title'] ?? 'Grup',
'umrah_tip3_desc'  => $settings['umrah_tip3_desc'] ?? 'Cocok untuk rombongan',
'umrah_tip4_title' => $settings['umrah_tip4_title'] ?? 'Support',
'umrah_tip4_desc'  => $settings['umrah_tip4_desc'] ?? 'Bisa konsultasi trip',

                // FOOTER (Kontak)
                'footer_address' => $settings['footer_address'] ?? 'Jl. Raya Kuta No. 88, Bali',
                'footer_phone' => $settings['footer_phone'] ?? '+62 811-1111-1752',
                'footer_email' => $settings['footer_email'] ?? 'info@bintangwisata.id',
                'footer_whatsapp' => $settings['footer_whatsapp'] ?? '6281111111752',
                // ABOUT META + HERO
                'about_meta_title' => $settings['about_meta_title'] ?? 'About - Bintang Wisata',
                'about_hero_badge' => $settings['about_hero_badge'] ?? 'Tentang Bintang Wisata',
                'about_hero_title' => $settings['about_hero_title'] ?? 'Mitra perjalanan yang rapi, transparan, dan berorientasi pada kenyamanan Anda',
                'about_hero_desc' => $settings['about_hero_desc'] ?? 'Bintang Wisata menyediakan layanan perjalanan dan transportasi yang dirancang untuk memudahkan Anda: mulai dari pemilihan paket, penjadwalan, hingga dukungan selama perjalanan. Kami menempatkan transparansi dan ketepatan layanan sebagai standar utama.',
                'site_logo' => $settings['site_logo'] ?? '/images/logo.png',
                // ABOUT VALUES
                'about_values_label' => $settings['about_values_label'] ?? 'NILAI KAMI',
                'about_values_title' => $settings['about_values_title'] ?? 'Prinsip kerja yang kami pegang',
                'about_values_desc' => $settings['about_values_desc'] ?? 'Kami membangun layanan yang rapi dan konsisten. Tujuannya sederhana: pengalaman perjalanan yang nyaman dan dapat diandalkan.',

                'about_value1_title' => $settings['about_value1_title'] ?? 'Transparansi',
                'about_value1_desc' => $settings['about_value1_desc'] ?? 'Harga, fasilitas, dan ketentuan disampaikan dengan jelas sejak awal.',
                'about_value2_title' => $settings['about_value2_title'] ?? 'Ketepatan',
                'about_value2_desc' => $settings['about_value2_desc'] ?? 'Jadwal dan rencana perjalanan disusun realistis sesuai kebutuhan Anda.',
                'about_value3_title' => $settings['about_value3_title'] ?? 'Kenyamanan',
                'about_value3_desc' => $settings['about_value3_desc'] ?? 'Kami menjaga detail layanan agar perjalanan terasa lebih ringan.',
                'about_value4_title' => $settings['about_value4_title'] ?? 'Responsif',
                'about_value4_desc' => $settings['about_value4_desc'] ?? 'Tim kami memberikan bantuan cepat untuk pertanyaan dan penyesuaian.',

                // ABOUT FLOW/STEPS
                'about_flow_label' => $settings['about_flow_label'] ?? 'ALUR LAYANAN',
                'about_flow_title' => $settings['about_flow_title'] ?? 'Langkah sederhana, hasil yang jelas',
                'about_flow_desc' => $settings['about_flow_desc'] ?? 'Kami menyusun alur layanan agar Anda dapat melakukan pemesanan tanpa kebingungan. Setiap tahap terstruktur dan mudah diikuti.',

                'about_step1_title' => $settings['about_step1_title'] ?? 'Pilih layanan',
                'about_step1_desc' => $settings['about_step1_desc'] ?? 'Tentukan paket tour atau rental sesuai kebutuhan.',
                'about_step2_title' => $settings['about_step2_title'] ?? 'Konsultasi singkat',
                'about_step2_desc' => $settings['about_step2_desc'] ?? 'Konfirmasi detail itinerary, durasi, dan ketentuan.',
                'about_step3_title' => $settings['about_step3_title'] ?? 'Pemesanan',
                'about_step3_desc' => $settings['about_step3_desc'] ?? 'Lengkapi data dan lakukan proses sesuai instruksi.',
                'about_step4_title' => $settings['about_step4_title'] ?? 'Perjalanan dimulai',
                'about_step4_desc' => $settings['about_step4_desc'] ?? 'Nikmati perjalanan, tim kami siap membantu bila diperlukan.',

                // HOME: highlights (Kenapa layanan kami beda)
                'home_highlight_label' => $settings['home_highlight_label'] ?? 'Kenapa layanan kami beda',
                'home_highlight_title' => $settings['home_highlight_title'] ?? 'Detail, rapi, dan fokus ke pengalaman perjalanan.',
                'home_highlight_desc' => $settings['home_highlight_desc'] ?? 'Kami bikin trip terasa “beres” dari awal: informasi jelas, itinerary enak diikuti, dan tim responsif.',

                'home_highlight_left1_title' => $settings['home_highlight_left1_title'] ?? 'Harga Transparan',
                'home_highlight_left1_desc' => $settings['home_highlight_left1_desc'] ?? 'Tanpa biaya tersembunyi',
                'home_highlight_left2_title' => $settings['home_highlight_left2_title'] ?? 'Itinerary Jelas',
                'home_highlight_left2_desc' => $settings['home_highlight_left2_desc'] ?? 'Rute & waktu terstruktur',
                'home_highlight_left3_title' => $settings['home_highlight_left3_title'] ?? 'Booking Cepat',
                'home_highlight_left3_desc' => $settings['home_highlight_left3_desc'] ?? 'Form ringkas & jelas',
                'home_highlight_left4_title' => $settings['home_highlight_left4_title'] ?? 'Support Aktif',
                'home_highlight_left4_desc' => $settings['home_highlight_left4_desc'] ?? 'Bisa konsultasi trip',

                'home_highlight_right1_title' => $settings['home_highlight_right1_title'] ?? 'Destinasi Favorit',
                'home_highlight_right1_desc' => $settings['home_highlight_right1_desc'] ?? 'Bali, Lombok, Jogja, Bandung, sampai destinasi luar negeri (tergantung paket).',
                'home_highlight_right2_title' => $settings['home_highlight_right2_title'] ?? 'Cocok untuk Grup',
                'home_highlight_right2_desc' => $settings['home_highlight_right2_desc'] ?? 'Trip keluarga, kantor, komunitas — tinggal sesuaikan kebutuhan.',
                'home_highlight_right3_title' => $settings['home_highlight_right3_title'] ?? 'Budget Friendly',
                'home_highlight_right3_desc' => $settings['home_highlight_right3_desc'] ?? 'Paket fleksibel dengan informasi harga jelas sejak awal.',
                'home_highlight_right4_title' => $settings['home_highlight_right4_title'] ?? 'Spot Wisata Terbaik',
                'home_highlight_right4_desc' => $settings['home_highlight_right4_desc'] ?? 'Fokus pengalaman: view bagus, tempat ikonik, dan alur perjalanan nyaman.',

                'home_highlight_cta_primary_text' => $settings['home_highlight_cta_primary_text'] ?? 'Mulai Jelajah Paket',
                'home_highlight_cta_secondary_text' => $settings['home_highlight_cta_secondary_text'] ?? 'Cek Armada Rental',

                // HOME: why choose
                'home_why_label' => $settings['home_why_label'] ?? 'Layanan unggulan',
                'home_why_title' => $settings['home_why_title'] ?? 'Mengapa Memilih Bintang Wisata',
                'home_why_desc' => $settings['home_why_desc'] ?? 'Kami berkomitmen memberikan layanan perjalanan yang profesional, transparan, dan berorientasi pada kenyamanan pelanggan.',
                'home_why1_title' => $settings['home_why1_title'] ?? 'Harga Transparan',
                'home_why1_desc' => $settings['home_why1_desc'] ?? 'Tanpa biaya tersembunyi, semua informasi jelas sejak awal.',
                'home_why2_title' => $settings['home_why2_title'] ?? 'Legal & Terpercaya',
                'home_why2_desc' => $settings['home_why2_desc'] ?? 'Dikelola secara profesional dan berpengalaman.',
                'home_why3_title' => $settings['home_why3_title'] ?? 'Proses Booking Cepat',
                'home_why3_desc' => $settings['home_why3_desc'] ?? 'Sistem pemesanan ringkas dan mudah digunakan.',
                'home_why4_title' => $settings['home_why4_title'] ?? 'Dukungan Pelanggan',
                'home_why4_desc' => $settings['home_why4_desc'] ?? 'Tim siap membantu sebelum dan selama perjalanan.',

                // HOME: booking flow
                'home_flow_label' => $settings['home_flow_label'] ?? 'Alur mudah',
                'home_flow_title' => $settings['home_flow_title'] ?? 'Cara Booking yang Rapi & Cepat',
                'home_flow_desc' => $settings['home_flow_desc'] ?? 'Biar gak buang waktu, alurnya dibuat simple tapi tetap jelas.',
                'home_flow1_title' => $settings['home_flow1_title'] ?? 'Pilih Paket',
                'home_flow1_desc' => $settings['home_flow1_desc'] ?? 'Cari destinasi, cek detail itinerary, dan sesuaikan kebutuhan.',
                'home_flow2_title' => $settings['home_flow2_title'] ?? 'Konsultasi',
                'home_flow2_desc' => $settings['home_flow2_desc'] ?? 'Tanya jadwal, meeting point, atau request khusus untuk grup.',
                'home_flow3_title' => $settings['home_flow3_title'] ?? 'Konfirmasi',
                'home_flow3_desc' => $settings['home_flow3_desc'] ?? 'Finalisasi tanggal & data peserta, lalu booking dikunci.',
                'home_flow4_title' => $settings['home_flow4_title'] ?? 'Berangkat',
                'home_flow4_desc' => $settings['home_flow4_desc'] ?? 'Nikmati perjalanan. Tim support siap bantu selama trip.',
                // FOOTER (Konten)
                'footer_tagline' => $settings['footer_tagline'] ?? 'Partner perjalanan terpercaya untuk menjelajahi keindahan Indonesia. Paket wisata premium dengan harga bersahabat.',
                'footer_quick_links_title' => $settings['footer_quick_links_title'] ?? 'Tautan Cepat',

                'footer_link1_label' => $settings['footer_link1_label'] ?? 'Beranda',
                'footer_link1_url' => $settings['footer_link1_url'] ?? route('home'),
                'footer_link2_label' => $settings['footer_link2_label'] ?? 'Paket Tour',
                'footer_link2_url' => $settings['footer_link2_url'] ?? route('tours.index'),
                'footer_link3_label' => $settings['footer_link3_label'] ?? 'Artikel',
                'footer_link3_url' => $settings['footer_link3_url'] ?? route('articles'),
                'footer_link4_label' => $settings['footer_link4_label'] ?? 'Tentang',
                'footer_link4_url' => $settings['footer_link4_url'] ?? route('about'),

                'footer_copyright' => $settings['footer_copyright'] ?? ('© ' . date('Y') . ' Bintang Wisata Indonesia. All rights reserved.'),

                // TOUR PACKAGES PAGE
                'tour_hero_badge' => $settings['tour_hero_badge'] ?? 'Paket Tour',
                'tour_hero_title' => $settings['tour_hero_title'] ?? 'Temukan Paket Tour yang Sesuai Kebutuhan Anda',
                'tour_hero_desc'  => $settings['tour_hero_desc']  ?? 'Gunakan pencarian dan filter untuk menyaring paket berdasarkan destinasi maupun kategori.',

                'tour_filter_dest_label'  => $settings['tour_filter_dest_label'] ?? 'Destinasi',
                'tour_filter_cat_label'   => $settings['tour_filter_cat_label'] ?? 'Kategori',
                'tour_filter_dur_label'   => $settings['tour_filter_dur_label'] ?? 'Durasi',
                'tour_filter_trans_label' => $settings['tour_filter_trans_label'] ?? 'Transparan',

                'tour_tips_title' => $settings['tour_tips_title'] ?? 'Tips Cepat',
                'tour_tips_desc'  => $settings['tour_tips_desc']  ?? 'Gunakan kata kunci destinasi untuk hasil lebih akurat.',

                'tour_tip1_title' => $settings['tour_tip1_title'] ?? 'Rekomendasi',
                'tour_tip1_desc'  => $settings['tour_tip1_desc']  ?? 'Paket favorit pelanggan',
                'tour_tip2_title' => $settings['tour_tip2_title'] ?? 'Itinerary',
                'tour_tip2_desc'  => $settings['tour_tip2_desc']  ?? 'Alur perjalanan jelas',
                'tour_tip3_title' => $settings['tour_tip3_title'] ?? 'Grup',
                'tour_tip3_desc'  => $settings['tour_tip3_desc']  ?? 'Cocok untuk rombongan',
                'tour_tip4_title' => $settings['tour_tip4_title'] ?? 'Support',
                'tour_tip4_desc'  => $settings['tour_tip4_desc']  ?? 'Bisa konsultasi trip',

                'tour_cta_title'  => $settings['tour_cta_title'] ?? 'Membutuhkan Rekomendasi Paket yang Tepat?',
                'tour_cta_desc'   => $settings['tour_cta_desc'] ?? 'Hubungi tim kami untuk konsultasi gratis dan dapatkan rekomendasi paket sesuai kebutuhan Anda.',
                'tour_cta_button' => $settings['tour_cta_button'] ?? 'Konsultasi via WhatsApp',

                // RENT CAR PAGE
                'rentcar_hero_badge' => $settings['rentcar_hero_badge'] ?? 'Rental Mobil',
                'rentcar_hero_title' => $settings['rentcar_hero_title'] ?? 'Pilihan Mobil Terbaik untuk Perjalanan Anda',
                'rentcar_hero_desc'  => $settings['rentcar_hero_desc']  ?? 'Armada terawat, harga transparan, dan proses booking cepat tanpa ribet.',

                'rentcar_chip1' => $settings['rentcar_chip1'] ?? 'Terawat',
                'rentcar_chip2' => $settings['rentcar_chip2'] ?? 'Transparan',
                'rentcar_chip3' => $settings['rentcar_chip3'] ?? 'Cepat',
                'rentcar_chip4' => $settings['rentcar_chip4'] ?? 'Travel Ready',

                'rentcar_note_title' => $settings['rentcar_note_title'] ?? 'Catatan',
                'rentcar_note_desc'  => $settings['rentcar_note_desc']  ?? 'Klik “Booking Sekarang” untuk lihat detail unit.',

                'rentcar_note1_title' => $settings['rentcar_note1_title'] ?? 'Hemat',
                'rentcar_note1_desc'  => $settings['rentcar_note1_desc']  ?? 'Nyaman untuk perjalanan',
                'rentcar_note2_title' => $settings['rentcar_note2_title'] ?? 'Bersih',
                'rentcar_note2_desc'  => $settings['rentcar_note2_desc']  ?? 'Unit terawat',
                'rentcar_note3_title' => $settings['rentcar_note3_title'] ?? 'Kapasitas',
                'rentcar_note3_desc'  => $settings['rentcar_note3_desc']  ?? 'Cocok keluarga/grup',
                'rentcar_note4_title' => $settings['rentcar_note4_title'] ?? 'Fleksibel',
                'rentcar_note4_desc'  => $settings['rentcar_note4_desc']  ?? 'Untuk wisata & kerja',

                'tracking_head' => $settings['tracking_head'] ?? '',
'tracking_body' => $settings['tracking_body'] ?? '',
                // DOCUMENTATION PAGE
                'docs_hero_badge' => $settings['docs_hero_badge'] ?? 'Dokumentasi Perjalanan',
                'docs_hero_title' => $settings['docs_hero_title'] ?? 'Dokumentasi',
                'docs_hero_desc'  => $settings['docs_hero_desc']  ?? 'Galeri dokumentasi perjalanan dan aktivitas layanan kami, terdiri dari foto dan video.',

                'docs_tab_photos' => $settings['docs_tab_photos'] ?? 'Foto',
                'docs_tab_videos' => $settings['docs_tab_videos'] ?? 'Video',

                'docs_stat_photos' => $settings['docs_stat_photos'] ?? 'Total Foto',
                'docs_stat_videos' => $settings['docs_stat_videos'] ?? 'Total Video',

                'docs_hint' => $settings['docs_hint'] ?? 'Gunakan tab untuk menavigasi dokumentasi. Konten tetap dimuat lengkap.',

            ]);

                  $popupWidget = null;
            try {
                if (Schema::hasTable('popup_widgets')) {
                    $popupWidget = PopupWidget::enabled()->first();
                }
            } catch (\Throwable $e) {
                $popupWidget = null;
            }

            $view->with('popupWidget', $popupWidget);
        });
    }
}
