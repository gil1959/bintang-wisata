<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestoranPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RestoranPackageController extends Controller
{
    public function index()
    {
        

        $packages = RestoranPackage::query()
            
            ->latest()
            ->get();

        return view('admin.restoran.index', compact('packages'));
    }

    public function create()
    {
        
        return view('admin.restoran.create');
    }

    public function store(Request $request)
    {
        

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'label' => 'nullable|string|max:50',
            'price_per_pax' => 'required|numeric|min:0',
            'thumbnail' => 'nullable|image|max:2048',
            'features' => 'nullable|array',
            'long_description' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_keywords' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'social_title' => 'nullable|string|max:255',
            'social_description' => 'nullable|string',
            'seo_image' => 'nullable|image|max:2048',
        ]);

        $data['slug'] = Str::slug($data['title']);
        $data['is_active'] = $request->input('is_active', 1);
        $data['created_by_partner_id'] = null;
        $data['partner_review_status'] = 'approved';

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_path'] = $request->file('thumbnail')->store('restoran', 'public');
        }

        $cleanFeatures = [];
        foreach ($request->features ?? [] as $f) {
            $cleanFeatures[] = [
                'name' => $f['name'] ?? '',
                'available' => isset($f['available']) ? true : false,
            ];
        }
        $data['features'] = $cleanFeatures;

        
        if ($request->hasFile('seo_image')) {
            $data['seo_image_path'] = $request->file('seo_image')->store('seo_images', 'public');
        }
        
        RestoranPackage::create($data);

        return redirect()->route('admin.restoran-packages.index')
            ->with('success', 'Paket restoran berhasil dibuat.');
    }

    public function edit(RestoranPackage $restoran_package)
    {
        


        $package = $restoran_package;
        return view('admin.restoran.edit', compact('package'));
    }

    public function update(Request $request, RestoranPackage $restoran_package)
    {
        


        $data = $request->validate([
            'title' => 'required|string|max:255',
            'label' => 'nullable|string|max:50',
            'price_per_pax' => 'required|numeric|min:0',
            'thumbnail' => 'nullable|image|max:2048',
            'features' => 'nullable|array',
            'long_description' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_keywords' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'social_title' => 'nullable|string|max:255',
            'social_description' => 'nullable|string',
            'seo_image' => 'nullable|image|max:2048',
        ]);

        $data['slug'] = Str::slug($data['title']);

        if ($request->hasFile('thumbnail')) {
            if ($restoran_package->thumbnail_path) {
                Storage::disk('public')->delete($restoran_package->thumbnail_path);
            }
            $data['thumbnail_path'] = $request->file('thumbnail')->store('restoran', 'public');
        }

        $cleanFeatures = [];
        foreach ($request->features ?? [] as $f) {
            $cleanFeatures[] = [
                'name' => $f['name'] ?? '',
                'available' => isset($f['available']) ? true : false,
            ];
        }
        $data['features'] = $cleanFeatures;

        // Optional: admin might change is_active, which is handled in $data.
        $data['is_active'] = $request->input('is_active', 1);
        
        if ($request->hasFile('seo_image')) {
            if ($restoran_package->seo_image_path) {
                Storage::disk('public')->delete($restoran_package->seo_image_path);
            }
            $data['seo_image_path'] = $request->file('seo_image')->store('seo_images', 'public');
        }
        
        $restoran_package->update($data);

        return redirect()->route('admin.restoran-packages.index')
            ->with('success', 'Paket restoran berhasil diperbarui.');
    }

    public function destroy(RestoranPackage $restoran_package)
    {
        


        if ($restoran_package->thumbnail_path) {
            Storage::disk('public')->delete($restoran_package->thumbnail_path);
        }

        $restoran_package->delete();

        return redirect()->route('admin.restoran-packages.index')
            ->with('success', 'Paket restoran berhasil dihapus.');
    }
}
