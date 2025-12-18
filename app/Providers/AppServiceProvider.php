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

            ]);
        });
    }
}
