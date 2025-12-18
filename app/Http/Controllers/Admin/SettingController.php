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

            // Footer
            'footer_address'   => ['nullable', 'string', 'max:1000'],
            'footer_phone'     => ['nullable', 'string', 'max:50'],
            'footer_email'     => ['nullable', 'email', 'max:255'],
            'footer_whatsapp'  => ['nullable', 'string', 'max:30'],

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
        ]);

        // HERO
        Setting::updateOrCreate(['key' => 'hero_title'], ['value' => $data['hero_title']]);
        Setting::updateOrCreate(['key' => 'hero_subtitle'], ['value' => $data['hero_subtitle']]);

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

        // FOOTER
        Setting::updateOrCreate(['key' => 'footer_address'],  ['value' => $data['footer_address'] ?? '']);
        Setting::updateOrCreate(['key' => 'footer_phone'],    ['value' => $data['footer_phone'] ?? '']);
        Setting::updateOrCreate(['key' => 'footer_email'],    ['value' => $data['footer_email'] ?? '']);
        Setting::updateOrCreate(['key' => 'footer_whatsapp'], ['value' => $data['footer_whatsapp'] ?? '']);

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

        for ($i = 1; $i <= 4; $i++) {
            Setting::updateOrCreate(['key' => "about_step{$i}_title"], ['value' => $data["about_step{$i}_title"] ?? '']);
            Setting::updateOrCreate(['key' => "about_step{$i}_desc"],  ['value' => $data["about_step{$i}_desc"] ?? '']);
        }

        return back()->with('success', 'Settings berhasil disimpan.');
    }
}
