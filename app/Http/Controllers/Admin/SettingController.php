<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function general()
    {
        $settings = Setting::pluck('value', 'key');
        return view('admin.settings.general', compact('settings'));
    }

    public function saveGeneral(Request $request)
    {
        $data = $request->validate([
            'hero_title'       => ['required', 'string', 'max:120'],
            'hero_subtitle'    => ['required', 'string', 'max:180'],
            'hero_image'       => ['nullable', 'image', 'max:2048'],
// Home: teks di box "Cari Paket Wisata"
'home_search_title' => ['nullable', 'string', 'max:255'],
'home_search_desc'  => ['nullable', 'string', 'max:255'],
'home_search_hint'  => ['nullable', 'string', 'max:255'],

            // Footer
            'footer_address'   => ['nullable', 'string', 'max:1000'],
            'footer_phone'     => ['nullable', 'string', 'max:50'],
            'footer_email'     => ['nullable', 'email', 'max:255'],
            'footer_whatsapp'  => ['nullable', 'string', 'max:30'],
            'tour_cta_secondary_button' => ['nullable', 'string', 'max:60'],
            'tour_cta_secondary_link'   => ['nullable', 'string', 'max:255'],
'tracking_head' => ['nullable', 'string'],
'tracking_body' => ['nullable', 'string'],
            // Email notif
            'invoice_admin_email' => ['nullable', 'email', 'max:255'],

            // About meta + hero
            'about_meta_title' => ['nullable', 'string', 'max:120'],
            'about_hero_badge' => ['nullable', 'string', 'max:60'],
            'about_hero_title' => ['nullable', 'string', 'max:200'],
            'about_hero_desc'  => ['nullable', 'string', 'max:1000'],

            // Values section
            'about_values_label' => ['nullable', 'string', 'max:60'],
            'about_values_title' => ['nullable', 'string', 'max:120'],
            'about_values_desc'  => ['nullable', 'string', 'max:500'],

            'about_value1_title' => ['nullable', 'string', 'max:80'],
            'about_value1_desc'  => ['nullable', 'string', 'max:200'],
            'about_value2_title' => ['nullable', 'string', 'max:80'],
            'about_value2_desc'  => ['nullable', 'string', 'max:200'],
            'about_value3_title' => ['nullable', 'string', 'max:80'],
            'about_value3_desc'  => ['nullable', 'string', 'max:200'],
            'about_value4_title' => ['nullable', 'string', 'max:80'],
            'about_value4_desc'  => ['nullable', 'string', 'max:200'],

            // Flow/steps section
            'about_flow_label' => ['nullable', 'string', 'max:60'],
            'about_flow_title' => ['nullable', 'string', 'max:120'],
            'about_flow_desc'  => ['nullable', 'string', 'max:500'],

            'about_step1_title' => ['nullable', 'string', 'max:80'],
            'about_step1_desc'  => ['nullable', 'string', 'max:200'],
            'about_step2_title' => ['nullable', 'string', 'max:80'],
            'about_step2_desc'  => ['nullable', 'string', 'max:200'],
            'about_step3_title' => ['nullable', 'string', 'max:80'],
            'about_step3_desc'  => ['nullable', 'string', 'max:200'],
            'about_step4_title' => ['nullable', 'string', 'max:80'],
            'about_step4_desc'  => ['nullable', 'string', 'max:200'],

            'home_highlight_label' => ['nullable', 'string', 'max:60'],
            'home_highlight_title' => ['nullable', 'string', 'max:140'],
            'home_highlight_desc'  => ['nullable', 'string', 'max:300'],

            'home_highlight_left1_title' => ['nullable', 'string', 'max:80'],
            'home_highlight_left1_desc'  => ['nullable', 'string', 'max:120'],
            'home_highlight_left2_title' => ['nullable', 'string', 'max:80'],
            'home_highlight_left2_desc'  => ['nullable', 'string', 'max:120'],
            'home_highlight_left3_title' => ['nullable', 'string', 'max:80'],
            'home_highlight_left3_desc'  => ['nullable', 'string', 'max:120'],
            'home_highlight_left4_title' => ['nullable', 'string', 'max:80'],
            'home_highlight_left4_desc'  => ['nullable', 'string', 'max:120'],

            'home_highlight_right1_title' => ['nullable', 'string', 'max:80'],
            'home_highlight_right1_desc'  => ['nullable', 'string', 'max:180'],
            'home_highlight_right2_title' => ['nullable', 'string', 'max:80'],
            'home_highlight_right2_desc'  => ['nullable', 'string', 'max:180'],
            'home_highlight_right3_title' => ['nullable', 'string', 'max:80'],
            'home_highlight_right3_desc'  => ['nullable', 'string', 'max:180'],
            'home_highlight_right4_title' => ['nullable', 'string', 'max:80'],
            'home_highlight_right4_desc'  => ['nullable', 'string', 'max:180'],

            'home_highlight_cta_primary_text' => ['nullable', 'string', 'max:40'],
            'home_highlight_cta_secondary_text' => ['nullable', 'string', 'max:40'],

            // Section: Mengapa Memilih (why)
            'home_why_label' => ['nullable', 'string', 'max:60'],
            'home_why_title' => ['nullable', 'string', 'max:140'],
            'home_why_desc'  => ['nullable', 'string', 'max:240'],

            'home_why1_title' => ['nullable', 'string', 'max:80'],
            'home_why1_desc'  => ['nullable', 'string', 'max:160'],
            'home_why2_title' => ['nullable', 'string', 'max:80'],
            'home_why2_desc'  => ['nullable', 'string', 'max:160'],
            'home_why3_title' => ['nullable', 'string', 'max:80'],
            'home_why3_desc'  => ['nullable', 'string', 'max:160'],
            'home_why4_title' => ['nullable', 'string', 'max:80'],
            'home_why4_desc'  => ['nullable', 'string', 'max:160'],

            // Section: Cara Booking (flow)
            'home_flow_label' => ['nullable', 'string', 'max:60'],
            'home_flow_title' => ['nullable', 'string', 'max:140'],
            'home_flow_desc'  => ['nullable', 'string', 'max:240'],

            'home_flow1_title' => ['nullable', 'string', 'max:80'],
            'home_flow1_desc'  => ['nullable', 'string', 'max:180'],
            'home_flow2_title' => ['nullable', 'string', 'max:80'],
            'home_flow2_desc'  => ['nullable', 'string', 'max:180'],
            'home_flow3_title' => ['nullable', 'string', 'max:80'],
            'home_flow3_desc'  => ['nullable', 'string', 'max:180'],
            'home_flow4_title' => ['nullable', 'string', 'max:80'],
            'home_flow4_desc'  => ['nullable', 'string', 'max:180'],
            // Footer - Konten
            'footer_tagline' => ['nullable', 'string', 'max:400'],
            'footer_quick_links_title' => ['nullable', 'string', 'max:60'],
            'footer_link1_label' => ['nullable', 'string', 'max:40'],
            'footer_link1_url' => ['nullable', 'string', 'max:255'],
            'footer_link2_label' => ['nullable', 'string', 'max:40'],
            'footer_link2_url' => ['nullable', 'string', 'max:255'],
            'footer_link3_label' => ['nullable', 'string', 'max:40'],
            'footer_link3_url' => ['nullable', 'string', 'max:255'],
            'footer_link4_label' => ['nullable', 'string', 'max:40'],
            'footer_link4_url' => ['nullable', 'string', 'max:255'],
            'footer_copyright' => ['nullable', 'string', 'max:200'],

            // Halaman Paket Tour
            'tour_hero_badge' => ['nullable', 'string', 'max:60'],
            'tour_hero_title' => ['nullable', 'string', 'max:200'],
            'tour_hero_desc'  => ['nullable', 'string', 'max:500'],
            'tour_filter_dest_label'  => ['nullable', 'string', 'max:40'],
            'tour_filter_cat_label'   => ['nullable', 'string', 'max:40'],
            'tour_filter_dur_label'   => ['nullable', 'string', 'max:40'],
            'tour_filter_trans_label' => ['nullable', 'string', 'max:40'],
            'tour_tips_title' => ['nullable', 'string', 'max:60'],
            'tour_tips_desc'  => ['nullable', 'string', 'max:200'],
            'tour_tip1_title' => ['nullable', 'string', 'max:40'],
            'tour_tip1_desc'  => ['nullable', 'string', 'max:80'],
            'tour_tip2_title' => ['nullable', 'string', 'max:40'],
            'tour_tip2_desc'  => ['nullable', 'string', 'max:80'],
            'tour_tip3_title' => ['nullable', 'string', 'max:40'],
            'tour_tip3_desc'  => ['nullable', 'string', 'max:80'],
            'tour_tip4_title' => ['nullable', 'string', 'max:40'],
            'tour_tip4_desc'  => ['nullable', 'string', 'max:80'],
            'tour_cta_title'  => ['nullable', 'string', 'max:200'],
            'tour_cta_desc'   => ['nullable', 'string', 'max:500'],
            'tour_cta_button' => ['nullable', 'string', 'max:60'],

            // Halaman Rent Car
            'rentcar_hero_badge' => ['nullable', 'string', 'max:60'],
            'rentcar_hero_title' => ['nullable', 'string', 'max:200'],
            'rentcar_hero_desc'  => ['nullable', 'string', 'max:500'],
            'rentcar_chip1' => ['nullable', 'string', 'max:30'],
            'rentcar_chip2' => ['nullable', 'string', 'max:30'],
            'rentcar_chip3' => ['nullable', 'string', 'max:30'],
            'rentcar_chip4' => ['nullable', 'string', 'max:30'],
            'rentcar_note_title' => ['nullable', 'string', 'max:60'],
            'rentcar_note_desc'  => ['nullable', 'string', 'max:200'],
            'rentcar_note1_title' => ['nullable', 'string', 'max:40'],
            'rentcar_note1_desc'  => ['nullable', 'string', 'max:80'],
            'rentcar_note2_title' => ['nullable', 'string', 'max:40'],
            'rentcar_note2_desc'  => ['nullable', 'string', 'max:80'],
            'rentcar_note3_title' => ['nullable', 'string', 'max:40'],
            'rentcar_note3_desc'  => ['nullable', 'string', 'max:80'],
            'rentcar_note4_title' => ['nullable', 'string', 'max:40'],
            'rentcar_note4_desc'  => ['nullable', 'string', 'max:80'],
// Halaman Sewa Kapal (Hero)
'ship_hero_badge' => ['nullable', 'string', 'max:60'],
'ship_hero_title' => ['nullable', 'string', 'max:200'],
'ship_hero_desc'  => ['nullable', 'string', 'max:500'],

// Sewa Kapal - Tips Box
'ship_tips_title' => ['nullable', 'string', 'max:60'],
'ship_tips_desc'  => ['nullable', 'string', 'max:180'],
'ship_tip1_title' => ['nullable', 'string', 'max:60'],
'ship_tip1_desc'  => ['nullable', 'string', 'max:80'],
'ship_tip2_title' => ['nullable', 'string', 'max:60'],
'ship_tip2_desc'  => ['nullable', 'string', 'max:80'],
'ship_tip3_title' => ['nullable', 'string', 'max:60'],
'ship_tip3_desc'  => ['nullable', 'string', 'max:80'],
'ship_tip4_title' => ['nullable', 'string', 'max:60'],
'ship_tip4_desc'  => ['nullable', 'string', 'max:80'],

// Halaman Umrah (Hero + filter + tips) - ini penting karena view udah pakai keys ini
'umrah_hero_badge' => ['nullable', 'string', 'max:60'],
'umrah_hero_title' => ['nullable', 'string', 'max:200'],
'umrah_hero_desc'  => ['nullable', 'string', 'max:500'],

'umrah_filter_dest_label'  => ['nullable', 'string', 'max:30'],
'umrah_filter_cat_label'   => ['nullable', 'string', 'max:30'],
'umrah_filter_dur_label'   => ['nullable', 'string', 'max:30'],
'umrah_filter_trans_label' => ['nullable', 'string', 'max:30'],

'umrah_tips_title' => ['nullable', 'string', 'max:60'],
'umrah_tips_desc'  => ['nullable', 'string', 'max:180'],
'umrah_tip1_title' => ['nullable', 'string', 'max:60'],
'umrah_tip1_desc'  => ['nullable', 'string', 'max:80'],
'umrah_tip2_title' => ['nullable', 'string', 'max:60'],
'umrah_tip2_desc'  => ['nullable', 'string', 'max:80'],
'umrah_tip3_title' => ['nullable', 'string', 'max:60'],
'umrah_tip3_desc'  => ['nullable', 'string', 'max:80'],
'umrah_tip4_title' => ['nullable', 'string', 'max:60'],
'umrah_tip4_desc'  => ['nullable', 'string', 'max:80'],

// HOME: Logo wall header
'home_logos_badge' => ['nullable', 'string', 'max:60'],
'home_logos_title' => ['nullable', 'string', 'max:140'],
'home_logos_desc'  => ['nullable', 'string', 'max:240'],

// HOME: Final CTA (Rencanakan Perjalanan...)
'home_final_cta_title'          => ['nullable', 'string', 'max:140'],
'home_final_cta_desc'           => ['nullable', 'string', 'max:240'],
'home_final_cta_primary_text'   => ['nullable', 'string', 'max:60'],
'home_final_cta_primary_url'    => ['nullable', 'string', 'max:255'],
'home_final_cta_secondary_text' => ['nullable', 'string', 'max:60'],
'home_final_cta_secondary_url'  => ['nullable', 'string', 'max:255'],

// HOME: Partner CTA
'home_partner_badge'       => ['nullable', 'string', 'max:60'],
'home_partner_title'       => ['nullable', 'string', 'max:140'],
'home_partner_desc'        => ['nullable', 'string', 'max:300'],
'home_partner_button_text' => ['nullable', 'string', 'max:60'],
'home_partner_button_url'  => ['nullable', 'string', 'max:255'],

'home_partner_card1_title' => ['nullable', 'string', 'max:80'],
'home_partner_card1_desc'  => ['nullable', 'string', 'max:200'],
'home_partner_card2_title' => ['nullable', 'string', 'max:80'],
'home_partner_card2_desc'  => ['nullable', 'string', 'max:200'],
'home_partner_card3_title' => ['nullable', 'string', 'max:80'],
'home_partner_card3_desc'  => ['nullable', 'string', 'max:200'],
'home_partner_card4_title' => ['nullable', 'string', 'max:80'],
'home_partner_card4_desc'  => ['nullable', 'string', 'max:200'],

// MICE: Hero + tips (section yang lu tandain)
'mice_hero_badge' => ['nullable', 'string', 'max:60'],
'mice_hero_title' => ['nullable', 'string', 'max:140'],
'mice_hero_desc'  => ['nullable', 'string', 'max:500'],
'mice_cta_button' => ['nullable', 'string', 'max:60'],

'mice_tip1_title' => ['nullable', 'string', 'max:60'],
'mice_tip1_desc'  => ['nullable', 'string', 'max:120'],
'mice_tip2_title' => ['nullable', 'string', 'max:60'],
'mice_tip2_desc'  => ['nullable', 'string', 'max:120'],
'mice_tip3_title' => ['nullable', 'string', 'max:60'],
'mice_tip3_desc'  => ['nullable', 'string', 'max:120'],
'mice_tip4_title' => ['nullable', 'string', 'max:60'],
'mice_tip4_desc'  => ['nullable', 'string', 'max:120'],

// Dokumentasi per kategori (Ship & Umrah)
'docs_ship_hero_badge' => ['nullable', 'string', 'max:60'],
'docs_ship_hero_title' => ['nullable', 'string', 'max:120'],
'docs_ship_hero_desc'  => ['nullable', 'string', 'max:500'],

'docs_umrah_hero_badge' => ['nullable', 'string', 'max:60'],
'docs_umrah_hero_title' => ['nullable', 'string', 'max:120'],
'docs_umrah_hero_desc'  => ['nullable', 'string', 'max:500'],

            // Halaman Dokumentasi
            'docs_hero_badge' => ['nullable', 'string', 'max:60'],
            'docs_hero_title' => ['nullable', 'string', 'max:120'],
            'docs_hero_desc'  => ['nullable', 'string', 'max:500'],
            'docs_tab_photos' => ['nullable', 'string', 'max:30'],
            'docs_tab_videos' => ['nullable', 'string', 'max:30'],
            'docs_stat_photos' => ['nullable', 'string', 'max:40'],
            'docs_stat_videos' => ['nullable', 'string', 'max:40'],
            'docs_hint' => ['nullable', 'string', 'max:200'],
            'site_logo'        => ['nullable', 'image', 'max:2048'],
        ]);

        // HERO
        // TRACKING
Setting::updateOrCreate(
    ['key' => 'tracking_head'],
    ['value' => $data['tracking_head'] ?? '']
);

Setting::updateOrCreate(
    ['key' => 'tracking_body'],
    ['value' => $data['tracking_body'] ?? '']
);


        Setting::updateOrCreate(['key' => 'hero_title'], ['value' => $data['hero_title']]);
        Setting::updateOrCreate(['key' => 'hero_subtitle'], ['value' => $data['hero_subtitle']]);
Setting::updateOrCreate(['key' => 'home_search_title'], ['value' => $data['home_search_title'] ?? '']);
Setting::updateOrCreate(['key' => 'home_search_desc'],  ['value' => $data['home_search_desc'] ?? '']);
Setting::updateOrCreate(['key' => 'home_search_hint'],  ['value' => $data['home_search_hint'] ?? '']);
        if ($request->hasFile('hero_image')) {
            $old = Setting::where('key', 'hero_image')->value('value');
            if ($old && str_starts_with($old, '/storage/')) {
                $oldPath = str_replace('/storage/', 'public/', $old);
                Storage::delete($oldPath);
            }

            $path = $request->file('hero_image')->store('public/settings');
            $url = Storage::url($path);
            Setting::updateOrCreate(['key' => 'hero_image'], ['value' => $url]);
        }
        // BRAND LOGO (Navbar & Footer)
        if ($request->hasFile('site_logo')) {
            $old = Setting::where('key', 'site_logo')->value('value');
            if ($old && str_starts_with($old, '/storage/')) {
                $oldPath = str_replace('/storage/', 'public/', $old);
                Storage::delete($oldPath);
            }

            $path = $request->file('site_logo')->store('public/settings');
            $url = Storage::url($path);
            Setting::updateOrCreate(['key' => 'site_logo'], ['value' => $url]);
        }
        // FOOTER
        Setting::updateOrCreate(['key' => 'footer_address'],  ['value' => $data['footer_address'] ?? '']);
        Setting::updateOrCreate(['key' => 'footer_phone'],    ['value' => $data['footer_phone'] ?? '']);
        Setting::updateOrCreate(['key' => 'footer_email'],    ['value' => $data['footer_email'] ?? '']);
        Setting::updateOrCreate(['key' => 'footer_whatsapp'], ['value' => $data['footer_whatsapp'] ?? '']);
        Setting::updateOrCreate(
            ['key' => 'tour_cta_secondary_button'],
            ['value' => $data['tour_cta_secondary_button'] ?? '']
        );

        Setting::updateOrCreate(
            ['key' => 'tour_cta_secondary_link'],
            ['value' => $data['tour_cta_secondary_link'] ?? '']
        );

        // Email notif
        Setting::updateOrCreate(['key' => 'invoice_admin_email'], ['value' => $data['invoice_admin_email'] ?? '']);

        // ABOUT META + HERO
        Setting::updateOrCreate(['key' => 'about_meta_title'], ['value' => $data['about_meta_title'] ?? '']);
        Setting::updateOrCreate(['key' => 'about_hero_badge'], ['value' => $data['about_hero_badge'] ?? '']);
        Setting::updateOrCreate(['key' => 'about_hero_title'], ['value' => $data['about_hero_title'] ?? '']);
        Setting::updateOrCreate(['key' => 'about_hero_desc'],  ['value' => $data['about_hero_desc'] ?? '']);

        // VALUES
        Setting::updateOrCreate(['key' => 'about_values_label'], ['value' => $data['about_values_label'] ?? '']);
        Setting::updateOrCreate(['key' => 'about_values_title'], ['value' => $data['about_values_title'] ?? '']);
        Setting::updateOrCreate(['key' => 'about_values_desc'],  ['value' => $data['about_values_desc'] ?? '']);

        for ($i = 1; $i <= 4; $i++) {
            Setting::updateOrCreate(['key' => "about_value{$i}_title"], ['value' => $data["about_value{$i}_title"] ?? '']);
            Setting::updateOrCreate(['key' => "about_value{$i}_desc"],  ['value' => $data["about_value{$i}_desc"] ?? '']);
        }

        // FLOW/STEPS
        Setting::updateOrCreate(['key' => 'about_flow_label'], ['value' => $data['about_flow_label'] ?? '']);
        Setting::updateOrCreate(['key' => 'about_flow_title'], ['value' => $data['about_flow_title'] ?? '']);
        Setting::updateOrCreate(['key' => 'about_flow_desc'],  ['value' => $data['about_flow_desc'] ?? '']);
        // FOOTER - Konten
        Setting::updateOrCreate(['key' => 'footer_tagline'], ['value' => $data['footer_tagline'] ?? '']);
        Setting::updateOrCreate(['key' => 'footer_quick_links_title'], ['value' => $data['footer_quick_links_title'] ?? '']);
        Setting::updateOrCreate(['key' => 'footer_copyright'], ['value' => $data['footer_copyright'] ?? '']);

        for ($i = 1; $i <= 4; $i++) {
            Setting::updateOrCreate(['key' => "footer_link{$i}_label"], ['value' => $data["footer_link{$i}_label"] ?? '']);
            Setting::updateOrCreate(['key' => "footer_link{$i}_url"],   ['value' => $data["footer_link{$i}_url"] ?? '']);
        }

        // HALAMAN PAKET TOUR
        Setting::updateOrCreate(['key' => 'tour_hero_badge'], ['value' => $data['tour_hero_badge'] ?? '']);
        Setting::updateOrCreate(['key' => 'tour_hero_title'], ['value' => $data['tour_hero_title'] ?? '']);
        Setting::updateOrCreate(['key' => 'tour_hero_desc'],  ['value' => $data['tour_hero_desc'] ?? '']);

        Setting::updateOrCreate(['key' => 'tour_filter_dest_label'],  ['value' => $data['tour_filter_dest_label'] ?? '']);
        Setting::updateOrCreate(['key' => 'tour_filter_cat_label'],   ['value' => $data['tour_filter_cat_label'] ?? '']);
        Setting::updateOrCreate(['key' => 'tour_filter_dur_label'],   ['value' => $data['tour_filter_dur_label'] ?? '']);
        Setting::updateOrCreate(['key' => 'tour_filter_trans_label'], ['value' => $data['tour_filter_trans_label'] ?? '']);

        Setting::updateOrCreate(['key' => 'tour_tips_title'], ['value' => $data['tour_tips_title'] ?? '']);
        Setting::updateOrCreate(['key' => 'tour_tips_desc'],  ['value' => $data['tour_tips_desc'] ?? '']);

        Setting::updateOrCreate(['key' => 'tour_cta_title'],  ['value' => $data['tour_cta_title'] ?? '']);
        Setting::updateOrCreate(['key' => 'tour_cta_desc'],   ['value' => $data['tour_cta_desc'] ?? '']);
        Setting::updateOrCreate(['key' => 'tour_cta_button'], ['value' => $data['tour_cta_button'] ?? '']);

        for ($i = 1; $i <= 4; $i++) {
            Setting::updateOrCreate(['key' => "tour_tip{$i}_title"], ['value' => $data["tour_tip{$i}_title"] ?? '']);
            Setting::updateOrCreate(['key' => "tour_tip{$i}_desc"],  ['value' => $data["tour_tip{$i}_desc"] ?? '']);
        }

        // HALAMAN RENT CAR
        Setting::updateOrCreate(['key' => 'rentcar_hero_badge'], ['value' => $data['rentcar_hero_badge'] ?? '']);
        Setting::updateOrCreate(['key' => 'rentcar_hero_title'], ['value' => $data['rentcar_hero_title'] ?? '']);
        Setting::updateOrCreate(['key' => 'rentcar_hero_desc'],  ['value' => $data['rentcar_hero_desc'] ?? '']);

        Setting::updateOrCreate(['key' => 'rentcar_chip1'], ['value' => $data['rentcar_chip1'] ?? '']);
        Setting::updateOrCreate(['key' => 'rentcar_chip2'], ['value' => $data['rentcar_chip2'] ?? '']);
        Setting::updateOrCreate(['key' => 'rentcar_chip3'], ['value' => $data['rentcar_chip3'] ?? '']);
        Setting::updateOrCreate(['key' => 'rentcar_chip4'], ['value' => $data['rentcar_chip4'] ?? '']);

        Setting::updateOrCreate(['key' => 'rentcar_note_title'], ['value' => $data['rentcar_note_title'] ?? '']);
        Setting::updateOrCreate(['key' => 'rentcar_note_desc'],  ['value' => $data['rentcar_note_desc'] ?? '']);

        for ($i = 1; $i <= 4; $i++) {
            Setting::updateOrCreate(['key' => "rentcar_note{$i}_title"], ['value' => $data["rentcar_note{$i}_title"] ?? '']);
            Setting::updateOrCreate(['key' => "rentcar_note{$i}_desc"],  ['value' => $data["rentcar_note{$i}_desc"] ?? '']);
        }
// HALAMAN SEWA KAPAL
Setting::updateOrCreate(['key' => 'ship_hero_badge'], ['value' => $data['ship_hero_badge'] ?? '']);
Setting::updateOrCreate(['key' => 'ship_hero_title'], ['value' => $data['ship_hero_title'] ?? '']);
Setting::updateOrCreate(['key' => 'ship_hero_desc'],  ['value' => $data['ship_hero_desc'] ?? '']);

Setting::updateOrCreate(['key' => 'ship_tips_title'], ['value' => $data['ship_tips_title'] ?? '']);
Setting::updateOrCreate(['key' => 'ship_tips_desc'],  ['value' => $data['ship_tips_desc'] ?? '']);

for ($i = 1; $i <= 4; $i++) {
    Setting::updateOrCreate(['key' => "ship_tip{$i}_title"], ['value' => $data["ship_tip{$i}_title"] ?? '']);
    Setting::updateOrCreate(['key' => "ship_tip{$i}_desc"],  ['value' => $data["ship_tip{$i}_desc"] ?? '']);
}

// HALAMAN UMRAH
Setting::updateOrCreate(['key' => 'umrah_hero_badge'], ['value' => $data['umrah_hero_badge'] ?? '']);
Setting::updateOrCreate(['key' => 'umrah_hero_title'], ['value' => $data['umrah_hero_title'] ?? '']);
Setting::updateOrCreate(['key' => 'umrah_hero_desc'],  ['value' => $data['umrah_hero_desc'] ?? '']);

Setting::updateOrCreate(['key' => 'umrah_filter_dest_label'],  ['value' => $data['umrah_filter_dest_label'] ?? '']);
Setting::updateOrCreate(['key' => 'umrah_filter_cat_label'],   ['value' => $data['umrah_filter_cat_label'] ?? '']);
Setting::updateOrCreate(['key' => 'umrah_filter_dur_label'],   ['value' => $data['umrah_filter_dur_label'] ?? '']);
Setting::updateOrCreate(['key' => 'umrah_filter_trans_label'], ['value' => $data['umrah_filter_trans_label'] ?? '']);

Setting::updateOrCreate(['key' => 'umrah_tips_title'], ['value' => $data['umrah_tips_title'] ?? '']);
Setting::updateOrCreate(['key' => 'umrah_tips_desc'],  ['value' => $data['umrah_tips_desc'] ?? '']);

for ($i = 1; $i <= 4; $i++) {
    Setting::updateOrCreate(['key' => "umrah_tip{$i}_title"], ['value' => $data["umrah_tip{$i}_title"] ?? '']);
    Setting::updateOrCreate(['key' => "umrah_tip{$i}_desc"],  ['value' => $data["umrah_tip{$i}_desc"] ?? '']);
}

// DOKUMENTASI PER KATEGORI
Setting::updateOrCreate(['key' => 'docs_ship_hero_badge'], ['value' => $data['docs_ship_hero_badge'] ?? '']);
Setting::updateOrCreate(['key' => 'docs_ship_hero_title'], ['value' => $data['docs_ship_hero_title'] ?? '']);
Setting::updateOrCreate(['key' => 'docs_ship_hero_desc'],  ['value' => $data['docs_ship_hero_desc'] ?? '']);

Setting::updateOrCreate(['key' => 'docs_umrah_hero_badge'], ['value' => $data['docs_umrah_hero_badge'] ?? '']);
Setting::updateOrCreate(['key' => 'docs_umrah_hero_title'], ['value' => $data['docs_umrah_hero_title'] ?? '']);
Setting::updateOrCreate(['key' => 'docs_umrah_hero_desc'],  ['value' => $data['docs_umrah_hero_desc'] ?? '']);

        // HALAMAN DOKUMENTASI
        Setting::updateOrCreate(['key' => 'docs_hero_badge'], ['value' => $data['docs_hero_badge'] ?? '']);
        Setting::updateOrCreate(['key' => 'docs_hero_title'], ['value' => $data['docs_hero_title'] ?? '']);
        Setting::updateOrCreate(['key' => 'docs_hero_desc'],  ['value' => $data['docs_hero_desc'] ?? '']);

        Setting::updateOrCreate(['key' => 'docs_tab_photos'], ['value' => $data['docs_tab_photos'] ?? '']);
        Setting::updateOrCreate(['key' => 'docs_tab_videos'], ['value' => $data['docs_tab_videos'] ?? '']);

        Setting::updateOrCreate(['key' => 'docs_stat_photos'], ['value' => $data['docs_stat_photos'] ?? '']);
        Setting::updateOrCreate(['key' => 'docs_stat_videos'], ['value' => $data['docs_stat_videos'] ?? '']);

        Setting::updateOrCreate(['key' => 'docs_hint'], ['value' => $data['docs_hint'] ?? '']);

        for ($i = 1; $i <= 4; $i++) {
            Setting::updateOrCreate(['key' => "about_step{$i}_title"], ['value' => $data["about_step{$i}_title"] ?? '']);
            Setting::updateOrCreate(['key' => "about_step{$i}_desc"],  ['value' => $data["about_step{$i}_desc"] ?? '']);
        }

        foreach (
            [
                'home_highlight_label',
                'home_highlight_title',
                'home_highlight_desc',
                'home_highlight_cta_primary_text',
                'home_highlight_cta_secondary_text',
            ] as $k
        ) {
            Setting::updateOrCreate(['key' => $k], ['value' => $data[$k] ?? '']);
        }

        for ($i = 1; $i <= 4; $i++) {
            Setting::updateOrCreate(['key' => "home_highlight_left{$i}_title"],  ['value' => $data["home_highlight_left{$i}_title"] ?? '']);
            Setting::updateOrCreate(['key' => "home_highlight_left{$i}_desc"],   ['value' => $data["home_highlight_left{$i}_desc"] ?? '']);
            Setting::updateOrCreate(['key' => "home_highlight_right{$i}_title"], ['value' => $data["home_highlight_right{$i}_title"] ?? '']);
            Setting::updateOrCreate(['key' => "home_highlight_right{$i}_desc"],  ['value' => $data["home_highlight_right{$i}_desc"] ?? '']);
        }

        // why
        foreach (['home_why_label', 'home_why_title', 'home_why_desc'] as $k) {
            Setting::updateOrCreate(['key' => $k], ['value' => $data[$k] ?? '']);
        }
        for ($i = 1; $i <= 4; $i++) {
            Setting::updateOrCreate(['key' => "home_why{$i}_title"], ['value' => $data["home_why{$i}_title"] ?? '']);
            Setting::updateOrCreate(['key' => "home_why{$i}_desc"],  ['value' => $data["home_why{$i}_desc"] ?? '']);
        }

        // flow
        foreach (['home_flow_label', 'home_flow_title', 'home_flow_desc'] as $k) {
            Setting::updateOrCreate(['key' => $k], ['value' => $data[$k] ?? '']);
        }
        for ($i = 1; $i <= 4; $i++) {
            Setting::updateOrCreate(['key' => "home_flow{$i}_title"], ['value' => $data["home_flow{$i}_title"] ?? '']);
            Setting::updateOrCreate(['key' => "home_flow{$i}_desc"],  ['value' => $data["home_flow{$i}_desc"] ?? '']);
        }
// HOME: Logos header
foreach (['home_logos_badge','home_logos_title','home_logos_desc'] as $k) {
    Setting::updateOrCreate(['key' => $k], ['value' => $data[$k] ?? '']);
}

// HOME: Final CTA
foreach ([
    'home_final_cta_title',
    'home_final_cta_desc',
    'home_final_cta_primary_text',
    'home_final_cta_primary_url',
    'home_final_cta_secondary_text',
    'home_final_cta_secondary_url',
] as $k) {
    Setting::updateOrCreate(['key' => $k], ['value' => $data[$k] ?? '']);
}

// HOME: Partner CTA + cards
foreach ([
    'home_partner_badge',
    'home_partner_title',
    'home_partner_desc',
    'home_partner_button_text',
    'home_partner_button_url',
    'home_partner_card1_title','home_partner_card1_desc',
    'home_partner_card2_title','home_partner_card2_desc',
    'home_partner_card3_title','home_partner_card3_desc',
    'home_partner_card4_title','home_partner_card4_desc',
] as $k) {
    Setting::updateOrCreate(['key' => $k], ['value' => $data[$k] ?? '']);
}

// MICE: Hero + tips
foreach (['mice_hero_badge','mice_hero_title','mice_hero_desc','mice_cta_button'] as $k) {
    Setting::updateOrCreate(['key' => $k], ['value' => $data[$k] ?? '']);
}
for ($i = 1; $i <= 4; $i++) {
    Setting::updateOrCreate(['key' => "mice_tip{$i}_title"], ['value' => $data["mice_tip{$i}_title"] ?? '']);
    Setting::updateOrCreate(['key' => "mice_tip{$i}_desc"],  ['value' => $data["mice_tip{$i}_desc"] ?? '']);
}

        return back()->with('success', 'Settings berhasil disimpan.');
    }
}
