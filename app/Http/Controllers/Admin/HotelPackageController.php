<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HotelPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HotelPackageController extends Controller
{
    public function index()
    {
        

        $packages = HotelPackage::query()
            
            ->latest()
            ->get();

        return view('admin.hotel.index', compact('packages'));
    }

    public function create()
    {
        
        return view('admin.hotel.create');
    }

    public function store(Request $request)
    {
        

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'label' => 'nullable|string|max:50',
            'price_per_night' => 'required|numeric|min:0',
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
            $data['thumbnail_path'] = $request->file('thumbnail')->store('hotel', 'public');
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
        
        HotelPackage::create($data);

        return redirect()->route('admin.hotel-packages.index')
            ->with('success', 'Paket hotel/vila berhasil dibuat.');
    }

    public function edit(HotelPackage $hotel_package)
    {
        


        $package = $hotel_package;
        return view('admin.hotel.edit', compact('package'));
    }

    public function update(Request $request, HotelPackage $hotel_package)
    {
        


        $data = $request->validate([
            'title' => 'required|string|max:255',
            'label' => 'nullable|string|max:50',
            'price_per_night' => 'required|numeric|min:0',
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
            if ($hotel_package->thumbnail_path) {
                Storage::disk('public')->delete($hotel_package->thumbnail_path);
            }
            $data['thumbnail_path'] = $request->file('thumbnail')->store('hotel', 'public');
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
            if ($hotel_package->seo_image_path) {
                Storage::disk('public')->delete($hotel_package->seo_image_path);
            }
            $data['seo_image_path'] = $request->file('seo_image')->store('seo_images', 'public');
        }
        
        $hotel_package->update($data);

        return redirect()->route('admin.hotel-packages.index')
            ->with('success', 'Paket hotel/vila berhasil diperbarui.');
    }

    public function destroy(HotelPackage $hotel_package)
    {
        


        if ($hotel_package->thumbnail_path) {
            Storage::disk('public')->delete($hotel_package->thumbnail_path);
        }

        $hotel_package->delete();

        return redirect()->route('admin.hotel-packages.index')
            ->with('success', 'Paket hotel/vila berhasil dihapus.');
    }
}
