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
        // Ambil semua setting dalam bentuk key => value
        $settings = Setting::pluck('value', 'key');

        return view('admin.settings.general', compact('settings'));
    }

    public function saveGeneral(Request $request)
    {
        $data = $request->validate([
            'hero_title'       => ['required', 'string', 'max:120'],
            'hero_subtitle'    => ['required', 'string', 'max:180'],
            'hero_image'       => ['nullable', 'image', 'max:2048'],

            // Footer (Kontak)
            'footer_address'   => ['nullable', 'string'],
            'footer_phone'     => ['nullable', 'string', 'max:50'],
            'footer_email'     => ['nullable', 'email', 'max:255'],
            'footer_whatsapp'  => ['nullable', 'string', 'max:30'],
        ]);

        // === HERO SETTINGS ===
        Setting::updateOrCreate(['key' => 'hero_title'], ['value' => $data['hero_title']]);
        Setting::updateOrCreate(['key' => 'hero_subtitle'], ['value' => $data['hero_subtitle']]);

        // Hero image upload
        if ($request->hasFile('hero_image')) {
            // OPTIONAL: hapus file lama kalau mau (kalau value-nya format /storage/...)
            $old = Setting::where('key', 'hero_image')->value('value');
            if ($old && str_starts_with($old, '/storage/')) {
                $oldPath = str_replace('/storage/', 'public/', $old);
                Storage::delete($oldPath);
            }

            $path = $request->file('hero_image')->store('public/settings');
            $url = Storage::url($path);

            Setting::updateOrCreate(['key' => 'hero_image'], ['value' => $url]);
        }

        // === FOOTER SETTINGS (Kontak) ===
        Setting::updateOrCreate(['key' => 'footer_address'],  ['value' => $data['footer_address'] ?? '']);
        Setting::updateOrCreate(['key' => 'footer_phone'],    ['value' => $data['footer_phone'] ?? '']);
        Setting::updateOrCreate(['key' => 'footer_email'],    ['value' => $data['footer_email'] ?? '']);
        Setting::updateOrCreate(['key' => 'footer_whatsapp'], ['value' => $data['footer_whatsapp'] ?? '']);

        return back()->with('success', 'Settings berhasil disimpan.');
    }
}
