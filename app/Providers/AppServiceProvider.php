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
                'hero_title_en',
                'hero_subtitle',
                'hero_subtitle_en',
                'hero_image',
                'seo_site_title',
                'seo_meta_description',
                'seo_keywords',

                'home_tabs',
                'home_tabs_en',
                // SHIP PACKAGES PAGE
                'ship_hero_badge',
                'ship_hero_badge_en',
                'ship_hero_title',
                'ship_hero_title_en',
                'ship_hero_desc',
                'ship_hero_desc_en',

                'ship_tips_title',
                'ship_tips_title_en',
                'ship_tips_desc',
                'ship_tips_desc_en',

                'ship_tip1_title',
                'ship_tip1_title_en',
                'ship_tip1_desc',
                'ship_tip1_desc_en',

                'ship_tip2_title',
                'ship_tip2_title_en',
                'ship_tip2_desc',
                'ship_tip2_desc_en',

                'ship_tip3_title',
                'ship_tip3_title_en',
                'ship_tip3_desc',
                'ship_tip3_desc_en',

                'ship_tip4_title',
                'ship_tip4_title_en',
                'ship_tip4_desc',
                'ship_tip4_desc_en',


                // umrah packages page
                'umrah_hero_badge',
                'umrah_hero_badge_en',
                'umrah_hero_title',
                'umrah_hero_title_en',
                'umrah_hero_desc',
                'umrah_hero_desc_en',

                'umrah_filter_dest_label',
                'umrah_filter_dest_label_en',
                'umrah_filter_cat_label',
                'umrah_filter_cat_label_en',
                'umrah_filter_dur_label',
                'umrah_filter_dur_label_en',
                'umrah_filter_trans_label',
                'umrah_filter_trans_label_en',

                'umrah_tips_title',
                'umrah_tips_title_en',
                'umrah_tips_desc',
                'umrah_tips_desc_en',

                'umrah_tip1_title',
                'umrah_tip1_title_en',
                'umrah_tip1_desc',
                'umrah_tip1_desc_en',
                'umrah_tip2_title',
                'umrah_tip2_title_en',
                'umrah_tip2_desc',
                'umrah_tip2_desc_en',
                'umrah_tip3_title',
                'umrah_tip3_title_en',
                'umrah_tip3_desc',
                'umrah_tip3_desc_en',
                'umrah_tip4_title',
                'umrah_tip4_title_en',
                'umrah_tip4_desc',
                'umrah_tip4_desc_en',

                'docs_ship_hero_badge',
                'docs_ship_hero_badge_en',
                'docs_ship_hero_title',
                'docs_ship_hero_title_en',
                'docs_ship_hero_desc',
                'docs_ship_hero_desc_en',

                'docs_umrah_hero_badge',
                'docs_umrah_hero_badge_en',
                'docs_umrah_hero_title',
                'docs_umrah_hero_title_en',
                'docs_umrah_hero_desc',
                'docs_umrah_hero_desc_en',


                // footer
                'footer_address',
                'footer_phone',
                'footer_email',
                'footer_whatsapp',

                // about meta + hero
                'about_meta_title',
                'about_meta_title_en',
                'about_hero_badge',
                'about_hero_badge_en',
                'about_hero_title',
                'about_hero_title_en',
                'about_hero_desc',
                'about_hero_desc_en',

                // about values
                'about_values_label',
                'about_values_label_en',
                'about_values_title',
                'about_values_title_en',
                'about_values_desc',
                'about_values_desc_en',

                'about_value1_title',
                'about_value1_title_en',
                'about_value1_desc',
                'about_value1_desc_en',

                'about_value2_title',
                'about_value2_title_en',
                'about_value2_desc',
                'about_value2_desc_en',

                'about_value3_title',
                'about_value3_title_en',
                'about_value3_desc',
                'about_value3_desc_en',

                'about_value4_title',
                'about_value4_title_en',
                'about_value4_desc',
                'about_value4_desc_en',

                // about flow/steps
                'about_flow_label',
                'about_flow_label_en',
                'about_flow_title',
                'about_flow_title_en',
                'about_flow_desc',
                'about_flow_desc_en',

                'about_step1_title',
                'about_step1_title_en',
                'about_step1_desc',
                'about_step1_desc_en',

                'about_step2_title',
                'about_step2_title_en',
                'about_step2_desc',
                'about_step2_desc_en',

                'about_step3_title',
                'about_step3_title_en',
                'about_step3_desc',
                'about_step3_desc_en',

                'about_step4_title',
                'about_step4_title_en',
                'about_step4_desc',
                'about_step4_desc_en',


                'home_highlight_label',
                'home_highlight_label_en',
                'home_highlight_title',
                'home_highlight_title_en',
                'home_highlight_desc',
                'home_highlight_desc_en',

                'home_highlight_left1_title',
                'home_highlight_left1_title_en',
                'home_highlight_left1_desc',
                'home_highlight_left1_desc_en',

                'home_highlight_left2_title',
                'home_highlight_left2_title_en',
                'home_highlight_left2_desc',
                'home_highlight_left2_desc_en',

                'home_highlight_left3_title',
                'home_highlight_left3_title_en',
                'home_highlight_left3_desc',
                'home_highlight_left3_desc_en',

                'home_highlight_left4_title',
                'home_highlight_left4_title_en',
                'home_highlight_left4_desc',
                'home_highlight_left4_desc_en',

                'home_highlight_right1_title',
                'home_highlight_right1_title_en',
                'home_highlight_right1_desc',
                'home_highlight_right1_desc_en',

                'home_highlight_right2_title',
                'home_highlight_right2_title_en',
                'home_highlight_right2_desc',
                'home_highlight_right2_desc_en',

                'home_highlight_right3_title',
                'home_highlight_right3_title_en',
                'home_highlight_right3_desc',
                'home_highlight_right3_desc_en',

                'home_highlight_right4_title',
                'home_highlight_right4_title_en',
                'home_highlight_right4_desc',
                'home_highlight_right4_desc_en',

                'home_highlight_cta_primary_text',
                'home_highlight_cta_primary_text_en',
                'home_highlight_cta_secondary_text',
                'home_highlight_cta_secondary_text_en',


                // HOME: why choose (Mengapa Memilih ...)
                'home_why_label',
                'home_why_label_en',
                'home_why_title',
                'home_why_title_en',
                'home_why_desc',
                'home_why_desc_en',

                'home_why1_title',
                'home_why1_title_en',
                'home_why1_desc',
                'home_why1_desc_en',

                'home_why2_title',
                'home_why2_title_en',
                'home_why2_desc',
                'home_why2_desc_en',

                'home_why3_title',
                'home_why3_title_en',
                'home_why3_desc',
                'home_why3_desc_en',

                'home_why4_title',
                'home_why4_title_en',
                'home_why4_desc',
                'home_why4_desc_en',


                // HOME: booking flow (Cara Booking ...)
                'home_flow_label',
                'home_flow_label_en',
                'home_flow_title',
                'home_flow_title_en',
                'home_flow_desc',
                'home_flow_desc_en',

                'home_flow1_title',
                'home_flow1_title_en',
                'home_flow1_desc',
                'home_flow1_desc_en',

                'home_flow2_title',
                'home_flow2_title_en',
                'home_flow2_desc',
                'home_flow2_desc_en',

                'home_flow3_title',
                'home_flow3_title_en',
                'home_flow3_desc',
                'home_flow3_desc_en',

                'home_flow4_title',
                'home_flow4_title_en',
                'home_flow4_desc',
                'home_flow4_desc_en',

                // footer - konten
                'footer_tagline',
                'footer_tagline_en',
                'footer_quick_links_title',
                'footer_quick_links_title_en',

                'footer_link1_label',
                'footer_link1_label_en',
                'footer_link1_url',

                'footer_link2_label',
                'footer_link2_label_en',
                'footer_link2_url',

                'footer_link3_label',
                'footer_link3_label_en',
                'footer_link3_url',

                'footer_link4_label',
                'footer_link4_label_en',
                'footer_link4_url',

                'footer_copyright',
                'footer_copyright_en',


                // tour packages page
                'tour_hero_badge',
                'tour_hero_badge_en',
                'tour_hero_title',
                'tour_hero_title_en',
                'tour_hero_desc',
                'tour_hero_desc_en',

                'tour_filter_dest_label',
                'tour_filter_dest_label_en',
                'tour_filter_cat_label',
                'tour_filter_cat_label_en',
                'tour_filter_dur_label',
                'tour_filter_dur_label_en',
                'tour_filter_trans_label',
                'tour_filter_trans_label_en',

                'tour_tips_title',
                'tour_tips_title_en',
                'tour_tips_desc',
                'tour_tips_desc_en',

                'tour_tip1_title',
                'tour_tip1_title_en',
                'tour_tip1_desc',
                'tour_tip1_desc_en',
                'tour_tip2_title',
                'tour_tip2_title_en',
                'tour_tip2_desc',
                'tour_tip2_desc_en',
                'tour_tip3_title',
                'tour_tip3_title_en',
                'tour_tip3_desc',
                'tour_tip3_desc_en',
                'tour_tip4_title',
                'tour_tip4_title_en',
                'tour_tip4_desc',
                'tour_tip4_desc_en',

                'tour_cta_title',
                'tour_cta_title_en',
                'tour_cta_desc',
                'tour_cta_desc_en',
                'tour_cta_button',
                'tour_cta_button_en',

                'tour_cta_secondary_button',
                'tour_cta_secondary_button_en',

                // HOME: promo sections (static text)
                'home_promo_badge',
                'home_promo_badge_en',
                'home_promo_title',
                'home_promo_title_en',
                'home_promo_desc',
                'home_promo_desc_en',

                'home_ship_promo_badge',
                'home_ship_promo_badge_en',
                'home_ship_promo_title',
                'home_ship_promo_title_en',
                'home_ship_promo_desc',
                'home_ship_promo_desc_en',

                'home_umrah_promo_badge',
                'home_umrah_promo_badge_en',
                'home_umrah_promo_title',
                'home_umrah_promo_title_en',
                'home_umrah_promo_desc',
                'home_umrah_promo_desc_en',

                'home_mice_promo_badge',
                'home_mice_promo_badge_en',
                'home_mice_promo_title',
                'home_mice_promo_title_en',
                'home_mice_promo_desc',
                'home_mice_promo_desc_en',

                // HOME: articles/inspiration section (static text)
                'home_articles_title',
                'home_articles_title_en',
                'home_articles_desc',
                'home_articles_desc_en',
                'home_articles_button_text',
                'home_articles_button_text_en',

                // rentcar page
                'rentcar_hero_badge',
                'rentcar_hero_badge_en',
                'rentcar_hero_title',
                'rentcar_hero_title_en',
                'rentcar_hero_desc',
                'rentcar_hero_desc_en',

                'rentcar_chip1',
                'rentcar_chip1_en',
                'rentcar_chip2',
                'rentcar_chip2_en',
                'rentcar_chip3',
                'rentcar_chip3_en',
                'rentcar_chip4',
                'rentcar_chip4_en',

                'rentcar_note_title',
                'rentcar_note_title_en',
                'rentcar_note_desc',
                'rentcar_note_desc_en',

                'rentcar_note1_title',
                'rentcar_note1_title_en',
                'rentcar_note1_desc',
                'rentcar_note1_desc_en',

                'rentcar_note2_title',
                'rentcar_note2_title_en',
                'rentcar_note2_desc',
                'rentcar_note2_desc_en',

                'rentcar_note3_title',
                'rentcar_note3_title_en',
                'rentcar_note3_desc',
                'rentcar_note3_desc_en',

                'rentcar_note4_title',
                'rentcar_note4_title_en',
                'rentcar_note4_desc',
                'rentcar_note4_desc_en',



                'home_discount_banner_title',
                'home_mission_banner_title',
                'home_discount_banner_title_en',
                'home_mission_banner_title_en',


                'home_logos_badge',
                'home_logos_badge_en',
                'home_logos_title',
                'home_logos_title_en',
                'home_logos_desc',
                'home_logos_desc_en',


                'home_search_title',
                'home_search_desc',
                'home_search_hint',
                'home_search_title_en',
                'home_search_desc_en',
                'home_search_hint_en',

                'home_final_cta_title',
                'home_final_cta_title_en',
                'home_final_cta_desc',
                'home_final_cta_desc_en',
                'home_final_cta_primary_text',
                'home_final_cta_primary_text_en',
                'home_final_cta_secondary_text',
                'home_final_cta_secondary_text_en',

                'home_final_cta_secondary_url',

                // HOME: partner CTA
                'home_partner_badge',
                'home_partner_badge_en',
                'home_partner_title',
                'home_partner_title_en',
                'home_partner_desc',
                'home_partner_desc_en',
                'home_partner_button_text',
                'home_partner_button_text_en',

                'home_partner_button_url',
                'home_partner_card1_title',
                'home_partner_card1_title_en',
                'home_partner_card1_desc',
                'home_partner_card1_desc_en',

                'home_partner_card2_title',
                'home_partner_card2_title_en',
                'home_partner_card2_desc',
                'home_partner_card2_desc_en',

                'home_partner_card3_title',
                'home_partner_card3_title_en',
                'home_partner_card3_desc',
                'home_partner_card3_desc_en',

                'home_partner_card4_title',
                'home_partner_card4_title_en',
                'home_partner_card4_desc',
                'home_partner_card4_desc_en',

                // MICE hero + tips
                'mice_hero_badge',
                'mice_hero_badge_en',
                'mice_hero_title',
                'mice_hero_title_en',
                'mice_hero_desc',
                'mice_hero_desc_en',
                'mice_cta_button',
                'mice_cta_button_en',

                'mice_tip1_title',
                'mice_tip1_title_en',
                'mice_tip1_desc',
                'mice_tip1_desc_en',
                'mice_tip2_title',
                'mice_tip2_title_en',
                'mice_tip2_desc',
                'mice_tip2_desc_en',
                'mice_tip3_title',
                'mice_tip3_title_en',
                'mice_tip3_desc',
                'mice_tip3_desc_en',
                'mice_tip4_title',
                'mice_tip4_title_en',
                'mice_tip4_desc',
                'mice_tip4_desc_en',


                // docs page
                'docs_hero_badge',
                'docs_hero_badge_en',
                'docs_hero_title',
                'docs_hero_title_en',
                'docs_hero_desc',
                'docs_hero_desc_en',

                'docs_tab_photos',
                'docs_tab_photos_en',
                'docs_tab_videos',
                'docs_tab_videos_en',

                'docs_stat_photos',
                'docs_stat_photos_en',
                'docs_stat_videos',
                'docs_stat_videos_en',

                'docs_hint',
                'docs_hint_en',

                // HOME: promo (keys only)
                'home_promo_enabled',
                'home_promo_badge',
                'home_promo_badge_en',
                'home_promo_title',
                'home_promo_title_en',
                'home_promo_desc',
                'home_promo_desc_en',
                'home_promo_mode',
                'home_promo_custom_ids',

                'home_ship_promo_enabled',
                'home_ship_promo_badge',
                'home_ship_promo_badge_en',
                'home_ship_promo_title',
                'home_ship_promo_title_en',
                'home_ship_promo_desc',
                'home_ship_promo_desc_en',
                'home_ship_promo_mode',
                'home_ship_promo_custom_ids',

                'home_umrah_promo_enabled',
                'home_umrah_promo_badge',
                'home_umrah_promo_badge_en',
                'home_umrah_promo_title',
                'home_umrah_promo_title_en',
                'home_umrah_promo_desc',
                'home_umrah_promo_desc_en',
                'home_umrah_promo_mode',
                'home_umrah_promo_custom_ids',

                'home_mice_promo_enabled',
                'home_mice_promo_badge',
                'home_mice_promo_badge_en',
                'home_mice_promo_title',
                'home_mice_promo_title_en',
                'home_mice_promo_desc',
                'home_mice_promo_desc_en',
                'home_mice_promo_mode',
                'home_mice_promo_custom_ids',

                // HOME: articles section copy
                'home_articles_enabled',
                'home_articles_title',
                'home_articles_title_en',
                'home_articles_desc',
                'home_articles_desc_en',
                'home_articles_button_text',
                'home_articles_button_text_en',
                'home_articles_button_url',
                'home_articles_mode',
                'home_articles_custom_ids',



            ];

            $settings = Setting::whereIn('key', $keys)->pluck('value', 'key');
            $isEn = app()->getLocale() === 'en';

            $getSetting = function (string $key, string $default = '') use ($settings, $isEn) {
                if ($isEn) {
                    $enKey = $key . '_en';
                    if (isset($settings[$enKey]) && trim((string)$settings[$enKey]) !== '') {
                        return $settings[$enKey];
                    }
                }
                return $settings[$key] ?? $default;
            };

            $rawHomeTabs = null;

            if ($isEn) {
                $rawHomeTabsEn = $settings['home_tabs_en'] ?? null;
                $decodedEn = is_string($rawHomeTabsEn) ? json_decode($rawHomeTabsEn, true) : null;

                // pakai EN kalau valid array
                if (is_array($decodedEn)) {
                    $rawHomeTabs = $rawHomeTabsEn;
                }
            }

            // fallback ke ID
            if ($rawHomeTabs === null) {
                $rawHomeTabs = $settings['home_tabs'] ?? null;
            }

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
                    ['label' => $isEn ? 'To Do' : 'To Do',                 'icon' => 'clipboard-check', 'icon_image' => '', 'url' => route('tours.index')],
                    ['label' => $isEn ? 'Airport Transfer' : 'Jemputan Bandara', 'icon' => 'plane',     'icon_image' => '', 'url' => '#'],
                    ['label' => $isEn ? 'Ferry' : 'Ferry',                 'icon' => 'ship',            'icon_image' => '', 'url' => '#'],
                    ['label' => $isEn ? 'Travel' : 'Travel',               'icon' => 'bus',             'icon_image' => '', 'url' => '#'],
                    ['label' => $isEn ? 'Car Rental' : 'Sewa Mobil',       'icon' => 'car',             'icon_image' => '', 'url' => route('rentcar.index')],
                ];
            }


            $view->with('siteSettings', [
                'home_tabs' => $homeTabs,
                // HERO
                // HERO (locale-aware via *_en + fallback)
                'hero_title' => $getSetting('hero_title', 'Paket Tour Spesial untuk Liburan Tak Terlupakan!'),
                'hero_subtitle' => $getSetting('hero_subtitle', 'Liburan Tanpa Batas! Jelajahi Destinasi Impian dengan Paket Tour Kami'),

                'hero_image' => $settings['hero_image'] ?? '/images/hero-default.jpg',
                'seo_site_title' => $settings['seo_site_title'] ?? 'Bintang Wisata - Tour & Travel Terpercaya',
                'seo_meta_description' => $settings['seo_meta_description'] ?? '',
                'seo_keywords' => $settings['seo_keywords'] ?? '',
                'tour_cta_secondary_button' => $settings['tour_cta_secondary_button'] ?? 'Lihat Rental',
                'tour_cta_secondary_link'   => $settings['tour_cta_secondary_link'] ?? route('rentcar.index'),
                // SHIP PACKAGES PAGE (locale-aware via *_en + fallback)
                'ship_hero_badge' => $getSetting('ship_hero_badge', $isEn ? 'Ship Rental' : 'Sewa Kapal'),
                'ship_hero_title' => $getSetting('ship_hero_title', $isEn ? 'Choose Private Charter According to Your Trip Needs' : 'Temukan Paket Sewa Kapal yang Sesuai Kebutuhan Anda'),
                'ship_hero_desc'  => $getSetting('ship_hero_desc',  $isEn ? 'Use the search and category filters to sift through the available packages.' : 'Gunakan pencarian dan filter kategori untuk menyaring paket yang tersedia.'),

                'ship_tips_title' => $getSetting('ship_tips_title', $isEn ? 'Quick Tips' : 'Tips Cepat'),
                'ship_tips_desc'  => $getSetting('ship_tips_desc',  $isEn ? 'Check package details for weekday/weekend prices and available features.' : 'Cek detail paket untuk harga weekday/weekend & fitur yang tersedia.'),

                'ship_tip1_title' => $getSetting('ship_tip1_title', $isEn ? 'Weekday/Weekend' : 'Weekday/Weekend'),
                'ship_tip1_desc'  => $getSetting('ship_tip1_desc',  $isEn ? 'Prices vary by day' : 'Harga berbeda sesuai hari'),

                'ship_tip2_title' => $getSetting('ship_tip2_title', $isEn ? 'For Groups' : 'Untuk Grup'),
                'ship_tip2_desc'  => $getSetting('ship_tip2_desc',  $isEn ? 'Suitable for families/groups' : 'Cocok keluarga/rombongan'),

                'ship_tip3_title' => $getSetting('ship_tip3_title', $isEn ? 'Recommended' : 'Rekomendasi'),
                'ship_tip3_desc'  => $getSetting('ship_tip3_desc',  $isEn ? 'Customer favorite packages' : 'Paket favorit pelanggan'),

                'ship_tip4_title' => $getSetting('ship_tip4_title', $isEn ? 'Support' : 'Support'),
                'ship_tip4_desc'  => $getSetting('ship_tip4_desc',  $isEn ? 'Consultation before booking' : 'Bisa konsultasi sebelum booking'),


                // HOME: banner titles
                'home_discount_banner_title' => $getSetting('home_discount_banner_title', 'Discount up to 50% + instant cashback'),
                'home_mission_banner_title'  => $getSetting('home_mission_banner_title', 'Earn up to IDR 850K from missions'),
                'home_promo_enabled' => $settings['home_promo_enabled'] ?? '1',
                'home_promo_badge'   => $getSetting('home_promo_badge', 'PROMO'),
                'home_promo_title'   => $getSetting('home_promo_title', 'Paket Tour Promo'),
                'home_promo_desc'    => $getSetting('home_promo_desc', ''),
                'home_promo_mode'    => $settings['home_promo_mode'] ?? 'auto',
                'home_promo_custom_ids' => $settings['home_promo_custom_ids'] ?? '[]',
                // HOME: promo ship
                'home_ship_promo_enabled' => $settings['home_ship_promo_enabled'] ?? '1',
                'home_ship_promo_badge'   => $getSetting('home_ship_promo_badge', 'PROMO KAPAL'),
                'home_ship_promo_title'   => $getSetting('home_ship_promo_title', 'Paket Sewa Kapal Promo'),
                'home_ship_promo_desc'    => $getSetting('home_ship_promo_desc', ''),
                'home_ship_promo_mode'    => $settings['home_ship_promo_mode'] ?? 'auto',
                'home_ship_promo_custom_ids' => $settings['home_ship_promo_custom_ids'] ?? '[]',

                // HOME: promo umrah
                'home_umrah_promo_enabled' => $settings['home_umrah_promo_enabled'] ?? '1',
                'home_umrah_promo_badge'   => $getSetting('home_umrah_promo_badge', 'PROMO UMRAH'),
                'home_umrah_promo_title'   => $getSetting('home_umrah_promo_title', 'Paket Umrah Promo'),
                'home_umrah_promo_desc'    => $getSetting('home_umrah_promo_desc', ''),
                'home_umrah_promo_mode'    => $settings['home_umrah_promo_mode'] ?? 'auto',
                'home_umrah_promo_custom_ids' => $settings['home_umrah_promo_custom_ids'] ?? '[]',

                // HOME: promo mice
                'home_mice_promo_enabled' => $settings['home_mice_promo_enabled'] ?? '1',
                'home_mice_promo_badge'   => $getSetting('home_mice_promo_badge', 'PROMO MICE'),
                'home_mice_promo_title'   => $getSetting('home_mice_promo_title', 'Paket MICE Promo'),
                'home_mice_promo_desc'    => $getSetting('home_mice_promo_desc', ''),
                'home_mice_promo_mode'    => $settings['home_mice_promo_mode'] ?? 'auto',
                'home_mice_promo_custom_ids' => $settings['home_mice_promo_custom_ids'] ?? '[]',

                // HOME: articles section copy
                'home_articles_title'       => $getSetting('home_articles_title', 'Baca dan bangkitkan semangat liburanmu'),
                'home_articles_desc'        => $getSetting('home_articles_desc', ''),
                'home_articles_button_text' => $getSetting('home_articles_button_text', 'Baca Artikel Inspirasi'),

                // HOME: logos header (locale-aware via *_en + fallback)
                'home_logos_badge' => $getSetting('home_logos_badge', 'Kepercayaan pelanggan'),
                'home_logos_title' => $getSetting('home_logos_title', 'Kepercayaan Pelanggan Bintang Wisata'),
                'home_logos_desc'  => $getSetting('home_logos_desc', 'Brand dan institusi yang telah mempercayakan perjalanan bersama kami'),

                // HOME: hero search box (locale-aware via *_en + fallback)
                'home_search_title' => $getSetting('home_search_title', 'Cari Paket Wisata'),
                'home_search_desc'  => $getSetting('home_search_desc', 'Temukan paket sesuai destinasi, kategori, dan tanggal keberangkatan.'),
                'home_search_hint'  => $getSetting('home_search_hint', 'Pakai kata kunci yang spesifik agar hasil lebih relevan.'),


                // HOME: final CTA
                'home_final_cta_title'          => $getSetting('home_final_cta_title', 'Rencanakan Perjalanan Anda Sekarang'),
                'home_final_cta_desc'           => $getSetting('home_final_cta_desc', 'Hubungi tim kami untuk mendapatkan rekomendasi perjalanan terbaik sesuai kebutuhan Anda.'),
                'home_final_cta_primary_text'   => $getSetting('home_final_cta_primary_text', 'Lihat Paket Tour'),
                'home_final_cta_secondary_text' => $getSetting('home_final_cta_secondary_text', 'Konsultasi Perjalanan'),

                'home_final_cta_secondary_url'  => $settings['home_final_cta_secondary_url'] ?? '#',

                // HOME: partner CTA
                'home_partner_badge'       => $getSetting('home_partner_badge', 'Program Partner'),
                'home_partner_title'       => $getSetting('home_partner_title', 'Mau jadi Partner Bintang Wisata?'),
                'home_partner_desc'        => $getSetting('home_partner_desc', 'Kembangkan jangkauan layanan kamu bersama Bintang Wisata. Dapatkan akses dashboard khusus partner untuk kebutuhan operasional.'),
                'home_partner_button_text' => $getSetting('home_partner_button_text', 'Daftar Partner'),

                'home_partner_button_url'  => $settings['home_partner_button_url'] ?? '',

                'home_partner_card1_title' => $getSetting('home_partner_card1_title', 'Dashboard Partner'),
                'home_partner_card1_desc'  => $getSetting('home_partner_card1_desc', 'Akses halaman khusus partner untuk mengelola kebutuhan operasional.'),

                'home_partner_card2_title' => $getSetting('home_partner_card2_title', 'Pengaturan Fleksibel'),
                'home_partner_card2_desc'  => $getSetting('home_partner_card2_desc', 'Data akun partner dan konfigurasi layanan dapat dikelola dengan rapi.'),

                'home_partner_card3_title' => $getSetting('home_partner_card3_title', 'Ringkas & Terukur'),
                'home_partner_card3_desc'  => $getSetting('home_partner_card3_desc', 'Memudahkan pemantauan aktivitas dan pengelolaan kebutuhan harian.'),

                'home_partner_card4_title' => $getSetting('home_partner_card4_title', 'Dukungan Tim'),
                'home_partner_card4_desc'  => $getSetting('home_partner_card4_desc', 'Tim kami siap membantu untuk kelancaran kerja sama operasional.'),
                // MICE: hero + tips (locale-aware via *_en + fallback)
                'mice_hero_badge' => $getSetting('mice_hero_badge', 'Paket MICE'),
                'mice_hero_title' => $getSetting('mice_hero_title', 'Solusi Paket MICE untuk Event Perusahaan Anda'),
                'mice_hero_desc'  => $getSetting('mice_hero_desc', 'Meetings, Incentives, Conferences, and Exhibitions. Pilih paket, lihat detail, dan lanjut checkout dengan mudah.'),
                'mice_cta_button' => $getSetting('mice_cta_button', 'Lihat Paket'),

                'mice_tip1_title' => $getSetting('mice_tip1_title', 'Event Ready'),
                'mice_tip1_desc'  => $getSetting('mice_tip1_desc', 'Paket siap untuk meeting, conference, dan exhibition.'),
                'mice_tip2_title' => $getSetting('mice_tip2_title', 'Terpercaya'),
                'mice_tip2_desc'  => $getSetting('mice_tip2_desc', 'Pilihan paket jelas, detail lengkap, mudah dipilih.'),
                'mice_tip3_title' => $getSetting('mice_tip3_title', 'Harga Fleksibel'),
                'mice_tip3_desc'  => $getSetting('mice_tip3_desc', 'Tier harga Domestik & WNA bisa multi baris sesuai kebutuhan.'),
                'mice_tip4_title' => $getSetting('mice_tip4_title', 'Support'),
                'mice_tip4_desc'  => $getSetting('mice_tip4_desc', 'Bisa konsultasi kebutuhan event dan itinerary.'),


                // UMRAH PACKAGES PAGE
                'umrah_hero_badge' => $getSetting('umrah_hero_badge', 'Paket Umrah'),
                'umrah_hero_title' => $getSetting('umrah_hero_title', 'Temukan Paket Umrah yang Sesuai Kebutuhan Anda'),
                'umrah_hero_desc'  => $getSetting('umrah_hero_desc', 'Gunakan pencarian dan filter untuk menyaring paket berdasarkan destinasi maupun kategori.'),

                'umrah_filter_dest_label'  => $getSetting('umrah_filter_dest_label', 'Destinasi'),
                'umrah_filter_cat_label'   => $getSetting('umrah_filter_cat_label', 'Kategori'),
                'umrah_filter_dur_label'   => $getSetting('umrah_filter_dur_label', 'Durasi'),
                'umrah_filter_trans_label' => $getSetting('umrah_filter_trans_label', 'Transparan'),

                'umrah_tips_title' => $getSetting('umrah_tips_title', 'Tips Cepat'),
                'umrah_tips_desc'  => $getSetting('umrah_tips_desc', 'Gunakan kata kunci destinasi untuk hasil lebih akurat.'),
                'umrah_tip1_title' => $getSetting('umrah_tip1_title', 'Rekomendasi'),
                'umrah_tip1_desc'  => $getSetting('umrah_tip1_desc', 'Paket favorit pelanggan'),
                'umrah_tip2_title' => $getSetting('umrah_tip2_title', 'Itinerary'),
                'umrah_tip2_desc'  => $getSetting('umrah_tip2_desc', 'Alur perjalanan jelas'),
                'umrah_tip3_title' => $getSetting('umrah_tip3_title', 'Grup'),
                'umrah_tip3_desc'  => $getSetting('umrah_tip3_desc', 'Cocok untuk rombongan'),
                'umrah_tip4_title' => $getSetting('umrah_tip4_title', 'Support'),
                'umrah_tip4_desc'  => $getSetting('umrah_tip4_desc', 'Bisa konsultasi trip'),


                // FOOTER (Kontak)
                'footer_address' => $settings['footer_address'] ?? 'Jl. Raya Kuta No. 88, Bali',
                'footer_phone' => $settings['footer_phone'] ?? '+62 811-1111-1752',
                'footer_email' => $settings['footer_email'] ?? 'info@bintangwisata.id',
                'footer_whatsapp' => $settings['footer_whatsapp'] ?? '6281111111752',
                // ABOUT META + HERO
                // ABOUT PAGE (locale-aware via *_en + fallback)
                'about_meta_title' => $getSetting('about_meta_title', $isEn ? 'About - Bintang Wisata' : 'About - Bintang Wisata'),

                'about_hero_badge' => $getSetting('about_hero_badge', $isEn ? 'About Bintang Wisata' : 'Tentang Bintang Wisata'),
                'about_hero_title' => $getSetting(
                    'about_hero_title',
                    $isEn ? 'A travel partner that is neat, transparent, and focused on your comfort'
                        : 'Mitra perjalanan yang rapi, transparan, dan berorientasi pada kenyamanan Anda'
                ),
                'about_hero_desc'  => $getSetting(
                    'about_hero_desc',
                    $isEn ? 'Bintang Wisata provides travel and transportation services designed to make everything easier—from choosing packages and scheduling, to on-trip support. Transparency and service accuracy are our baseline standards.'
                        : 'Bintang Wisata menyediakan layanan perjalanan dan transportasi yang dirancang untuk memudahkan Anda: mulai dari pemilihan paket, penjadwalan, hingga dukungan selama perjalanan. Kami menempatkan transparansi dan ketepatan layanan sebagai standar utama.'
                ),

                'about_values_label' => $getSetting('about_values_label', $isEn ? 'OUR VALUES' : 'NILAI KAMI'),
                'about_values_title' => $getSetting(
                    'about_values_title',
                    $isEn ? 'Principles we stand by' : 'Prinsip kerja yang kami pegang'
                ),
                'about_values_desc'  => $getSetting(
                    'about_values_desc',
                    $isEn ? 'We build a neat and consistent service. The goal is simple: a comfortable and reliable travel experience.'
                        : 'Kami membangun layanan yang rapi dan konsisten. Tujuannya sederhana: pengalaman perjalanan yang nyaman dan dapat diandalkan.'
                ),

                'about_value1_title' => $getSetting('about_value1_title', $isEn ? 'Transparency' : 'Transparansi'),
                'about_value1_desc'  => $getSetting(
                    'about_value1_desc',
                    $isEn ? 'Pricing, inclusions, and terms are communicated clearly from the start.'
                        : 'Harga, fasilitas, dan ketentuan disampaikan dengan jelas sejak awal.'
                ),

                'about_value2_title' => $getSetting('about_value2_title', $isEn ? 'Accuracy' : 'Ketepatan'),
                'about_value2_desc'  => $getSetting(
                    'about_value2_desc',
                    $isEn ? 'Schedules and itineraries are planned realistically based on your needs.'
                        : 'Jadwal dan rencana perjalanan disusun realistis sesuai kebutuhan Anda.'
                ),

                'about_value3_title' => $getSetting('about_value3_title', $isEn ? 'Comfort' : 'Kenyamanan'),
                'about_value3_desc'  => $getSetting(
                    'about_value3_desc',
                    $isEn ? 'We take care of the details so your trip feels lighter.'
                        : 'Kami menjaga detail layanan agar perjalanan terasa lebih ringan.'
                ),

                'about_value4_title' => $getSetting('about_value4_title', $isEn ? 'Responsive' : 'Responsif'),
                'about_value4_desc'  => $getSetting(
                    'about_value4_desc',
                    $isEn ? 'Our team responds quickly to questions and adjustments.'
                        : 'Tim kami memberikan bantuan cepat untuk pertanyaan dan penyesuaian.'
                ),

                'about_flow_label' => $getSetting('about_flow_label', $isEn ? 'SERVICE FLOW' : 'ALUR LAYANAN'),
                'about_flow_title' => $getSetting(
                    'about_flow_title',
                    $isEn ? 'Simple steps, clear outcomes' : 'Langkah sederhana, hasil yang jelas'
                ),
                'about_flow_desc'  => $getSetting(
                    'about_flow_desc',
                    $isEn ? 'We structure the service flow so you can book without confusion. Each step is organized and easy to follow.'
                        : 'Kami menyusun alur layanan agar Anda dapat melakukan pemesanan tanpa kebingungan. Setiap tahap terstruktur dan mudah diikuti.'
                ),

                'about_step1_title' => $getSetting('about_step1_title', $isEn ? 'Choose a service' : 'Pilih layanan'),
                'about_step1_desc'  => $getSetting(
                    'about_step1_desc',
                    $isEn ? 'Select a tour package or rental based on your needs.'
                        : 'Tentukan paket tour atau rental sesuai kebutuhan.'
                ),

                'about_step2_title' => $getSetting('about_step2_title', $isEn ? 'Quick consultation' : 'Konsultasi singkat'),
                'about_step2_desc'  => $getSetting(
                    'about_step2_desc',
                    $isEn ? 'Confirm itinerary details, duration, and terms.'
                        : 'Konfirmasi detail itinerary, durasi, dan ketentuan.'
                ),

                'about_step3_title' => $getSetting('about_step3_title', $isEn ? 'Booking' : 'Pemesanan'),
                'about_step3_desc'  => $getSetting(
                    'about_step3_desc',
                    $isEn ? 'Complete your details and follow the booking instructions.'
                        : 'Lengkapi data dan lakukan proses sesuai instruksi.'
                ),

                'about_step4_title' => $getSetting('about_step4_title', $isEn ? 'Trip starts' : 'Perjalanan dimulai'),
                'about_step4_desc'  => $getSetting(
                    'about_step4_desc',
                    $isEn ? 'Enjoy the trip—our team is ready to help if needed.'
                        : 'Nikmati perjalanan, tim kami siap membantu bila diperlukan.'
                ),


                // HOME: highlights (Kenapa layanan kami beda)
                'home_highlight_label' => $getSetting('home_highlight_label', 'Kenapa layanan kami beda'),
                'home_highlight_title' => $getSetting('home_highlight_title', 'Detail, rapi, dan fokus ke pengalaman perjalanan.'),
                'home_highlight_desc'  => $getSetting('home_highlight_desc', 'Kami bikin trip terasa “beres” dari awal: informasi jelas, itinerary enak diikuti, dan tim responsif.'),

                'home_highlight_left1_title' => $getSetting('home_highlight_left1_title', 'Harga Transparan'),
                'home_highlight_left1_desc'  => $getSetting('home_highlight_left1_desc', 'Tanpa biaya tersembunyi'),
                'home_highlight_left2_title' => $getSetting('home_highlight_left2_title', 'Itinerary Jelas'),
                'home_highlight_left2_desc'  => $getSetting('home_highlight_left2_desc', 'Rute & waktu terstruktur'),
                'home_highlight_left3_title' => $getSetting('home_highlight_left3_title', 'Booking Cepat'),
                'home_highlight_left3_desc'  => $getSetting('home_highlight_left3_desc', 'Form ringkas & jelas'),
                'home_highlight_left4_title' => $getSetting('home_highlight_left4_title', 'Support Aktif'),
                'home_highlight_left4_desc'  => $getSetting('home_highlight_left4_desc', 'Bisa konsultasi trip'),

                'home_highlight_right1_title' => $getSetting('home_highlight_right1_title', 'Destinasi Favorit'),
                'home_highlight_right1_desc'  => $getSetting('home_highlight_right1_desc', 'Bali, Lombok, Jogja, Bandung, sampai destinasi luar negeri (tergantung paket).'),
                'home_highlight_right2_title' => $getSetting('home_highlight_right2_title', 'Cocok untuk Grup'),
                'home_highlight_right2_desc'  => $getSetting('home_highlight_right2_desc', 'Trip keluarga, kantor, komunitas — tinggal sesuaikan kebutuhan.'),
                'home_highlight_right3_title' => $getSetting('home_highlight_right3_title', 'Budget Friendly'),
                'home_highlight_right3_desc'  => $getSetting('home_highlight_right3_desc', 'Paket fleksibel dengan informasi harga jelas sejak awal.'),
                'home_highlight_right4_title' => $getSetting('home_highlight_right4_title', 'Spot Wisata Terbaik'),
                'home_highlight_right4_desc'  => $getSetting('home_highlight_right4_desc', 'Fokus pengalaman: view bagus, tempat ikonik, dan alur perjalanan nyaman.'),

                'home_highlight_cta_primary_text'   => $getSetting('home_highlight_cta_primary_text', 'Mulai Jelajah Paket'),
                'home_highlight_cta_secondary_text' => $getSetting('home_highlight_cta_secondary_text', 'Cek Armada Rental'),


                // HOME: why choose (locale-aware via *_en + fallback)
                'home_why_label' => $getSetting('home_why_label', 'Layanan unggulan'),
                'home_why_title' => $getSetting('home_why_title', 'Mengapa Memilih Bintang Wisata'),
                'home_why_desc'  => $getSetting('home_why_desc', 'Kami berkomitmen memberikan layanan perjalanan yang profesional, transparan, dan berorientasi pada kenyamanan pelanggan.'),

                'home_why1_title' => $getSetting('home_why1_title', 'Harga Transparan'),
                'home_why1_desc'  => $getSetting('home_why1_desc', 'Tanpa biaya tersembunyi, semua detail jelas dari awal.'),
                'home_why2_title' => $getSetting('home_why2_title', 'Itinerary Terarah'),
                'home_why2_desc'  => $getSetting('home_why2_desc', 'Rute & jadwal disusun rapi agar perjalanan efisien.'),
                'home_why3_title' => $getSetting('home_why3_title', 'Pilihan Fleksibel'),
                'home_why3_desc'  => $getSetting('home_why3_desc', 'Bisa custom sesuai kebutuhan rombongan atau keluarga.'),
                'home_why4_title' => $getSetting('home_why4_title', 'Dukungan Pelanggan'),
                'home_why4_desc'  => $getSetting('home_why4_desc', 'Tim siap membantu sebelum dan selama perjalanan.'),


                // HOME: booking flow
                // HOME: booking flow (locale-aware via *_en + fallback)
                'home_flow_label' => $getSetting('home_flow_label', 'Alur mudah'),
                'home_flow_title' => $getSetting('home_flow_title', 'Cara Booking yang Rapi & Cepat'),
                'home_flow_desc'  => $getSetting('home_flow_desc', 'Biar gak buang waktu, alurnya dibuat simple tapi tetap jelas.'),

                'home_flow1_title' => $getSetting('home_flow1_title', 'Pilih Paket'),
                'home_flow1_desc'  => $getSetting('home_flow1_desc', 'Cari destinasi, cek detail itinerary, dan sesuaikan kebutuhan.'),
                'home_flow2_title' => $getSetting('home_flow2_title', 'Konsultasi'),
                'home_flow2_desc'  => $getSetting('home_flow2_desc', 'Tanya jadwal, meeting point, atau request khusus untuk grup.'),
                'home_flow3_title' => $getSetting('home_flow3_title', 'Konfirmasi'),
                'home_flow3_desc'  => $getSetting('home_flow3_desc', 'Finalisasi tanggal & data peserta, lalu booking dikunci.'),
                'home_flow4_title' => $getSetting('home_flow4_title', 'Berangkat'),
                'home_flow4_desc'  => $getSetting('home_flow4_desc', 'Nikmati perjalanan. Tim support siap bantu selama trip.'),

                // FOOTER (Konten) - locale-aware
                'footer_tagline' => $getSetting(
                    'footer_tagline',
                    $isEn
                        ? 'Your trusted travel partner to explore the beauty of Indonesia. Premium tour packages at friendly prices.'
                        : 'Partner perjalanan terpercaya untuk menjelajahi keindahan Indonesia. Paket wisata premium dengan harga bersahabat.'
                ),

                'footer_quick_links_title' => $getSetting(
                    'footer_quick_links_title',
                    $isEn ? 'Quick Links' : 'Tautan Cepat'
                ),

                'footer_link1_label' => $getSetting('footer_link1_label', $isEn ? 'Home' : 'Beranda'),
                'footer_link1_url'   => $settings['footer_link1_url'] ?? route('home'),

                'footer_link2_label' => $getSetting('footer_link2_label', $isEn ? 'Tour Packages' : 'Paket Tour'),
                'footer_link2_url'   => $settings['footer_link2_url'] ?? route('tours.index'),

                'footer_link3_label' => $getSetting('footer_link3_label', $isEn ? 'Articles' : 'Artikel'),
                'footer_link3_url'   => $settings['footer_link3_url'] ?? route('articles'),

                'footer_link4_label' => $getSetting('footer_link4_label', $isEn ? 'About' : 'Tentang'),
                'footer_link4_url'   => $settings['footer_link4_url'] ?? route('about'),

                'footer_copyright' => $getSetting(
                    'footer_copyright',
                    $isEn
                        ? ('© ' . date('Y') . ' Bintang Wisata Indonesia. All rights reserved.')
                        : ('© ' . date('Y') . ' Bintang Wisata Indonesia. Hak cipta dilindungi.')
                ),

                // TOUR PACKAGES PAGE (locale-aware via *_en + fallback)
                'tour_hero_badge' => $getSetting('tour_hero_badge', 'Paket Tour'),
                'tour_hero_title' => $getSetting('tour_hero_title', 'Temukan Paket Tour yang Sesuai Kebutuhan Anda'),
                'tour_hero_desc'  => $getSetting('tour_hero_desc', 'Gunakan pencarian dan filter untuk menyaring paket berdasarkan destinasi maupun kategori.'),

                'tour_filter_dest_label'  => $getSetting('tour_filter_dest_label', 'Destinasi'),
                'tour_filter_cat_label'   => $getSetting('tour_filter_cat_label', 'Kategori'),
                'tour_filter_dur_label'   => $getSetting('tour_filter_dur_label', 'Durasi'),
                'tour_filter_trans_label' => $getSetting('tour_filter_trans_label', 'Transparan'),

                'tour_tips_title' => $getSetting('tour_tips_title', 'Tips Cepat'),
                'tour_tips_desc'  => $getSetting('tour_tips_desc', 'Gunakan kata kunci destinasi untuk hasil lebih akurat.'),

                'tour_tip1_title' => $getSetting('tour_tip1_title', 'Rekomendasi'),
                'tour_tip1_desc'  => $getSetting('tour_tip1_desc', 'Paket favorit pelanggan'),
                'tour_tip2_title' => $getSetting('tour_tip2_title', 'Itinerary'),
                'tour_tip2_desc'  => $getSetting('tour_tip2_desc', 'Alur perjalanan jelas'),
                'tour_tip3_title' => $getSetting('tour_tip3_title', 'Grup'),
                'tour_tip3_desc'  => $getSetting('tour_tip3_desc', 'Cocok untuk rombongan'),
                'tour_tip4_title' => $getSetting('tour_tip4_title', 'Support'),
                'tour_tip4_desc'  => $getSetting('tour_tip4_desc', 'Bisa konsultasi trip'),

                'tour_cta_title'  => $getSetting('tour_cta_title', 'Membutuhkan Rekomendasi Paket yang Tepat?'),
                'tour_cta_desc'   => $getSetting('tour_cta_desc', 'Hubungi tim kami untuk konsultasi gratis dan dapatkan rekomendasi paket sesuai kebutuhan Anda.'),
                'tour_cta_button' => $getSetting('tour_cta_button', 'Konsultasi via WhatsApp'),

                'tour_cta_secondary_button' => $getSetting('tour_cta_secondary_button', 'Lihat Rental'),

                // RENT CAR PAGE (locale-aware via $getSetting -> *_en fallback)
                'rentcar_hero_badge' => $getSetting('rentcar_hero_badge', 'Rental Mobil'),
                'rentcar_hero_title' => $getSetting('rentcar_hero_title', 'Pilihan Mobil Terbaik untuk Perjalanan Anda'),
                'rentcar_hero_desc'  => $getSetting('rentcar_hero_desc',  'Armada terawat, harga transparan, dan proses booking cepat tanpa ribet.'),

                'rentcar_chip1' => $getSetting('rentcar_chip1', 'Terawat'),
                'rentcar_chip2' => $getSetting('rentcar_chip2', 'Transparan'),
                'rentcar_chip3' => $getSetting('rentcar_chip3', 'Cepat'),
                'rentcar_chip4' => $getSetting('rentcar_chip4', 'Travel Ready'),

                'rentcar_note_title' => $getSetting('rentcar_note_title', 'Catatan'),
                'rentcar_note_desc'  => $getSetting('rentcar_note_desc',  'Klik “Booking Sekarang” untuk lihat detail unit.'),

                'rentcar_note1_title' => $getSetting('rentcar_note1_title', 'Hemat'),
                'rentcar_note1_desc'  => $getSetting('rentcar_note1_desc',  'Nyaman untuk perjalanan'),
                'rentcar_note2_title' => $getSetting('rentcar_note2_title', 'Bersih'),
                'rentcar_note2_desc'  => $getSetting('rentcar_note2_desc',  'Unit terawat'),
                'rentcar_note3_title' => $getSetting('rentcar_note3_title', 'Kapasitas'),
                'rentcar_note3_desc'  => $getSetting('rentcar_note3_desc',  'Cocok keluarga/grup'),
                'rentcar_note4_title' => $getSetting('rentcar_note4_title', 'Fleksibel'),
                'rentcar_note4_desc'  => $getSetting('rentcar_note4_desc',  'Untuk wisata & kerja'),


                'tracking_head' => $settings['tracking_head'] ?? '',
                'tracking_body' => $settings['tracking_body'] ?? '',
                // DOCUMENTATION PAGE (locale-aware via *_en + fallback)
                'docs_hero_badge' => $getSetting('docs_hero_badge', 'Dokumentasi Perjalanan'),
                'docs_hero_title' => $getSetting('docs_hero_title', 'Dokumentasi'),
                'docs_hero_desc'  => $getSetting('docs_hero_desc', 'Galeri dokumentasi perjalanan dan aktivitas layanan kami, terdiri dari foto dan video.'),

                'docs_tab_photos' => $getSetting('docs_tab_photos', 'Foto'),
                'docs_tab_videos' => $getSetting('docs_tab_videos', 'Video'),

                'docs_stat_photos' => $getSetting('docs_stat_photos', 'Total Foto'),
                'docs_stat_videos' => $getSetting('docs_stat_videos', 'Total Video'),

                'docs_hint' => $getSetting('docs_hint', 'Gunakan tab untuk menavigasi dokumentasi. Konten tetap dimuat lengkap.'),

                // optional: category hero (kalau setting nya ada)
                'docs_ship_hero_badge' => $getSetting('docs_ship_hero_badge', ''),
                'docs_ship_hero_title' => $getSetting('docs_ship_hero_title', ''),
                'docs_ship_hero_desc'  => $getSetting('docs_ship_hero_desc', ''),

                'docs_umrah_hero_badge' => $getSetting('docs_umrah_hero_badge', ''),
                'docs_umrah_hero_title' => $getSetting('docs_umrah_hero_title', ''),
                'docs_umrah_hero_desc'  => $getSetting('docs_umrah_hero_desc', ''),


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
