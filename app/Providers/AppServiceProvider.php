<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        View::composer('*', function ($view) {

            $keys = [
                'hero_title',
                'hero_subtitle',
                'hero_image',
                'seo_site_title',
                'seo_meta_description',
                'seo_keywords',

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

            ];

            $settings = Setting::whereIn('key', $keys)->pluck('value', 'key');

            $view->with('siteSettings', [
                // HERO
                'hero_title' => $settings['hero_title'] ?? 'Paket Tour Spesial untuk Liburan Tak Terlupakan!',
                'hero_subtitle' => $settings['hero_subtitle'] ?? 'Liburan Tanpa Batas! Jelajahi Destinasi Impian dengan Paket Tour Kami',
                'hero_image' => $settings['hero_image'] ?? '/images/hero-default.jpg',
                'seo_site_title' => $settings['seo_site_title'] ?? 'Bintang Wisata - Tour & Travel Terpercaya',
                'seo_meta_description' => $settings['seo_meta_description'] ?? '',
                'seo_keywords' => $settings['seo_keywords'] ?? '',
                'tour_cta_secondary_button' => $settings['tour_cta_secondary_button'] ?? 'Lihat Rental',
                'tour_cta_secondary_link'   => $settings['tour_cta_secondary_link'] ?? route('rentcar.index'),

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
        });
    }
}
