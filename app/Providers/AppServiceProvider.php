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
            ]);
        });
    }
}
