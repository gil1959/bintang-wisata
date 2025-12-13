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
            'hero_title' => ['required', 'string', 'max:120'],
            'hero_subtitle' => ['required', 'string', 'max:180'],
            'hero_image' => ['nullable', 'image', 'max:2048'],
        ]);

        Setting::updateOrCreate(['key' => 'hero_title'], ['value' => $data['hero_title']]);
        Setting::updateOrCreate(['key' => 'hero_subtitle'], ['value' => $data['hero_subtitle']]);

        if ($request->hasFile('hero_image')) {
            $path = $request->file('hero_image')->store('public/settings');
            $url = Storage::url($path);
            Setting::updateOrCreate(['key' => 'hero_image'], ['value' => $url]);
        }

        return back()->with('success', 'Settings berhasil disimpan.');
    }
}
