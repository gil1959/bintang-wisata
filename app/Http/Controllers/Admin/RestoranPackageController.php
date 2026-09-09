<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestoranPackage;
use App\Models\RestoranPackagePhoto;
use App\Models\RestoranMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RestoranPackageController extends Controller
{
    public function index()
    {
        $packages = RestoranPackage::query()
            ->with(['photos', 'menus'])
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
            'gallery' => 'nullable|array',
            'gallery.*' => 'nullable|image|max:3072',
            'address' => 'nullable|string',
            'maps_url' => 'nullable|string',
            'nearby_places' => 'nullable|array',
            'facilities' => 'nullable|array',
            'keunggulan' => 'nullable|array',
            'note' => 'nullable|string',
            'cs_contact' => 'nullable|string|max:50',
            'features' => 'nullable|array',
            'menus' => 'nullable|array',
            'long_description' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_keywords' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'social_title' => 'nullable|string|max:255',
            'social_description' => 'nullable|string',
            'seo_image' => 'nullable|image|max:2048',
        ]);

        $slug = Str::slug($data['title']);
        $originalSlug = $slug;
        $counter = 1;
        while (RestoranPackage::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }
        $data['slug'] = $slug;

        $data['is_active'] = $request->input('is_active', 1);
        $data['created_by_partner_id'] = null;
        $data['partner_review_status'] = 'approved';

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_path'] = $request->file('thumbnail')->store('restoran', 'public');
        }

        // Clean nearby places
        $cleanNearby = [];
        foreach ($request->nearby_places ?? [] as $np) {
            $name = trim($np['name'] ?? '');
            if (!empty($name)) {
                $cleanNearby[] = [
                    'name' => $name,
                    'distance' => trim($np['distance'] ?? ''),
                ];
            }
        }
        $data['nearby_places'] = $cleanNearby;

        // Clean facilities
        $cleanFacilities = [];
        foreach ($request->facilities ?? [] as $f) {
            $fName = is_array($f) ? trim($f['name'] ?? '') : trim($f);
            if (!empty($fName)) {
                $cleanFacilities[] = $fName;
            }
        }
        $data['facilities'] = $cleanFacilities;

        // Clean keunggulan
        $cleanKeunggulan = [];
        foreach ($request->keunggulan ?? [] as $k) {
            $kText = is_array($k) ? trim($k['text'] ?? '') : trim($k);
            if (!empty($kText)) {
                $cleanKeunggulan[] = $kText;
            }
        }
        $data['keunggulan'] = $cleanKeunggulan;

        // Clean features
        $cleanFeatures = [];
        foreach ($request->features ?? [] as $feat) {
            if (!empty(trim($feat['name'] ?? ''))) {
                $cleanFeatures[] = [
                    'name' => trim($feat['name']),
                    'available' => isset($feat['available']) ? true : false,
                ];
            }
        }
        $data['features'] = $cleanFeatures;

        if ($request->hasFile('seo_image')) {
            $data['seo_image_path'] = $request->file('seo_image')->store('seo_images', 'public');
        }

        $package = RestoranPackage::create($data);

        // Multiple gallery photos
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('restoran/gallery', 'public');
                    $package->photos()->create(['file_path' => $path]);
                }
            }
        }

        // Restaurant Menus
        if ($request->has('menus') && is_array($request->menus)) {
            foreach ($request->menus as $idx => $m) {
                $menuName = trim($m['name'] ?? '');
                if (empty($menuName)) continue;

                $menuThumbPath = null;
                if ($request->hasFile("menus.{$idx}.thumbnail")) {
                    $menuThumbPath = $request->file("menus.{$idx}.thumbnail")->store('restoran/menus', 'public');
                }

                $isReady = isset($m['is_ready']) && ($m['is_ready'] == 1 || $m['is_ready'] === '1' || $m['is_ready'] === 'on' || $m['is_ready'] === true);

                $package->menus()->create([
                    'name' => $menuName,
                    'category' => trim($m['category'] ?? ''),
                    'price' => (float)($m['price'] ?? 0),
                    'thumbnail_path' => $menuThumbPath,
                    'is_ready' => $isReady,
                    'sort_order' => $idx,
                ]);
            }
        }

        return redirect()->route('admin.restoran-packages.index')
            ->with('success', 'Paket restoran berhasil dibuat.');
    }

    public function edit(RestoranPackage $restoran_package)
    {
        $package = $restoran_package->load(['photos', 'menus']);
        return view('admin.restoran.edit', compact('package'));
    }

    public function update(Request $request, RestoranPackage $restoran_package)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'label' => 'nullable|string|max:50',
            'price_per_pax' => 'required|numeric|min:0',
            'thumbnail' => 'nullable|image|max:2048',
            'gallery' => 'nullable|array',
            'gallery.*' => 'nullable|image|max:3072',
            'address' => 'nullable|string',
            'maps_url' => 'nullable|string',
            'nearby_places' => 'nullable|array',
            'facilities' => 'nullable|array',
            'keunggulan' => 'nullable|array',
            'note' => 'nullable|string',
            'cs_contact' => 'nullable|string|max:50',
            'features' => 'nullable|array',
            'menus' => 'nullable|array',
            'long_description' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_keywords' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'social_title' => 'nullable|string|max:255',
            'social_description' => 'nullable|string',
            'seo_image' => 'nullable|image|max:2048',
        ]);

        if ($data['title'] !== $restoran_package->title) {
            $slug = Str::slug($data['title']);
            $originalSlug = $slug;
            $counter = 1;
            while (RestoranPackage::where('slug', $slug)->where('id', '!=', $restoran_package->id)->exists()) {
                $slug = "{$originalSlug}-{$counter}";
                $counter++;
            }
            $data['slug'] = $slug;
        }

        if ($request->hasFile('thumbnail')) {
            if ($restoran_package->thumbnail_path) {
                Storage::disk('public')->delete($restoran_package->thumbnail_path);
            }
            $data['thumbnail_path'] = $request->file('thumbnail')->store('restoran', 'public');
        }

        // Clean nearby places
        $cleanNearby = [];
        foreach ($request->nearby_places ?? [] as $np) {
            $name = trim($np['name'] ?? '');
            if (!empty($name)) {
                $cleanNearby[] = [
                    'name' => $name,
                    'distance' => trim($np['distance'] ?? ''),
                ];
            }
        }
        $data['nearby_places'] = $cleanNearby;

        // Clean facilities
        $cleanFacilities = [];
        foreach ($request->facilities ?? [] as $f) {
            $fName = is_array($f) ? trim($f['name'] ?? '') : trim($f);
            if (!empty($fName)) {
                $cleanFacilities[] = $fName;
            }
        }
        $data['facilities'] = $cleanFacilities;

        // Clean keunggulan
        $cleanKeunggulan = [];
        foreach ($request->keunggulan ?? [] as $k) {
            $kText = is_array($k) ? trim($k['text'] ?? '') : trim($k);
            if (!empty($kText)) {
                $cleanKeunggulan[] = $kText;
            }
        }
        $data['keunggulan'] = $cleanKeunggulan;

        // Clean features
        $cleanFeatures = [];
        foreach ($request->features ?? [] as $feat) {
            if (!empty(trim($feat['name'] ?? ''))) {
                $cleanFeatures[] = [
                    'name' => trim($feat['name']),
                    'available' => isset($feat['available']) ? true : false,
                ];
            }
        }
        $data['features'] = $cleanFeatures;

        $data['is_active'] = $request->input('is_active', 1);

        if ($request->hasFile('seo_image')) {
            if ($restoran_package->seo_image_path) {
                Storage::disk('public')->delete($restoran_package->seo_image_path);
            }
            $data['seo_image_path'] = $request->file('seo_image')->store('seo_images', 'public');
        }

        $restoran_package->update($data);

        // Multiple gallery photos (add to existing)
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('restoran/gallery', 'public');
                    $restoran_package->photos()->create(['file_path' => $path]);
                }
            }
        }

        // Restaurant Menus Sync
        $existingMenuIds = [];
        if ($request->has('menus') && is_array($request->menus)) {
            foreach ($request->menus as $idx => $m) {
                $menuName = trim($m['name'] ?? '');
                if (empty($menuName)) continue;

                $menuId = !empty($m['id']) ? (int)$m['id'] : null;
                $thumbPath = $m['existing_thumbnail'] ?? null;

                if ($request->hasFile("menus.{$idx}.thumbnail")) {
                    if ($menuId) {
                        $oldMenu = RestoranMenu::find($menuId);
                        if ($oldMenu && $oldMenu->thumbnail_path) {
                            Storage::disk('public')->delete($oldMenu->thumbnail_path);
                        }
                    }
                    $thumbPath = $request->file("menus.{$idx}.thumbnail")->store('restoran/menus', 'public');
                }

                $isReady = isset($m['is_ready']) && ($m['is_ready'] == 1 || $m['is_ready'] === '1' || $m['is_ready'] === 'on' || $m['is_ready'] === true);

                if ($menuId && $menu = RestoranMenu::where('restoran_package_id', $restoran_package->id)->find($menuId)) {
                    $menu->update([
                        'name' => $menuName,
                        'category' => trim($m['category'] ?? ''),
                        'price' => (float)($m['price'] ?? 0),
                        'thumbnail_path' => $thumbPath,
                        'is_ready' => $isReady,
                        'sort_order' => $idx,
                    ]);
                    $existingMenuIds[] = $menu->id;
                } else {
                    $newMenu = $restoran_package->menus()->create([
                        'name' => $menuName,
                        'category' => trim($m['category'] ?? ''),
                        'price' => (float)($m['price'] ?? 0),
                        'thumbnail_path' => $thumbPath,
                        'is_ready' => $isReady,
                        'sort_order' => $idx,
                    ]);
                    $existingMenuIds[] = $newMenu->id;
                }
            }
        }

        // Delete removed menus
        $deletedMenus = $restoran_package->menus()->whereNotIn('id', $existingMenuIds)->get();
        foreach ($deletedMenus as $dm) {
            if ($dm->thumbnail_path) {
                Storage::disk('public')->delete($dm->thumbnail_path);
            }
            $dm->delete();
        }

        return redirect()->route('admin.restoran-packages.index')
            ->with('success', 'Paket restoran berhasil diperbarui.');
    }

    public function deletePhoto($photo)
    {
        $photoItem = RestoranPackagePhoto::findOrFail($photo);

        if ($photoItem->file_path) {
            Storage::disk('public')->delete($photoItem->file_path);
        }

        $photoItem->delete();

        return back()->with('success', 'Foto galeri berhasil dihapus.');
    }

    public function destroy(RestoranPackage $restoran_package)
    {
        if ($restoran_package->thumbnail_path) {
            Storage::disk('public')->delete($restoran_package->thumbnail_path);
        }

        if ($restoran_package->seo_image_path) {
            Storage::disk('public')->delete($restoran_package->seo_image_path);
        }

        foreach ($restoran_package->photos as $p) {
            if ($p->file_path) {
                Storage::disk('public')->delete($p->file_path);
            }
            $p->delete();
        }

        foreach ($restoran_package->menus as $m) {
            if ($m->thumbnail_path) {
                Storage::disk('public')->delete($m->thumbnail_path);
            }
            $m->delete();
        }

        $restoran_package->delete();

        return redirect()->route('admin.restoran-packages.index')
            ->with('success', 'Paket restoran berhasil dihapus.');
    }
}
