<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class LegalPagesController extends Controller
{
    public function edit()
    {
        $settings = Setting::pluck('value', 'key');

        return view('admin.legal-pages.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'legal_privacy_title' => ['nullable', 'string', 'max:120'],
            'legal_privacy_html'  => ['nullable', 'string'],

            'legal_terms_title'   => ['nullable', 'string', 'max:120'],
            'legal_terms_html'    => ['nullable', 'string'],

            'legal_contact_title' => ['nullable', 'string', 'max:120'],
            'legal_contact_html'  => ['nullable', 'string'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '']
            );
        }

        return back()->with('success', 'Konten halaman legal berhasil disimpan.');
    }
}
