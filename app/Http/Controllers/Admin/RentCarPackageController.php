<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RentCarPackage;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\StoreRentCarPackageRequest;
use App\Http\Requests\Admin\UpdateRentCarPackageRequest;
use App\Models\RentCarCategory;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class RentCarPackageController extends Controller
{
    public function index(Request $request)
    {
        $q        = trim((string) $request->query('q', ''));
        $category = $request->query('category');
        $status   = $request->query('status');

        $query = RentCarPackage::query()->with('category');

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('title', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%");
            });
        }

        if (!empty($category)) {
            $query->where('category_id', $category);
        }

        if ($status === 'active') {
            $query->where('is_active', 1);
        } elseif ($status === 'inactive') {
            $query->where('is_active', 0);
        }

        $packages = $query->latest()->paginate(20)->withQueryString();
        $categories = RentCarCategory::orderBy('name')->get();

        return view('admin.rentcar.index', compact('packages', 'categories', 'q', 'category', 'status'));
    }


    public function create()
    {
        $categories = RentCarCategory::orderBy('name')->get();
        return view('admin.rentcar.create', compact('categories'));
    }


    public function store(StoreRentCarPackageRequest $request)
    {
        $data = $request->validated();

        $customSlug = $request->filled('slug') ? Str::slug($request->input('slug')) : null;
        $slug = !empty($customSlug) ? $customSlug : Str::slug($data['title']);
        $originalSlug = $slug;
        $counter = 1;
        while (RentCarPackage::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }
        $data['slug'] = $slug;

        $package = null;

        DB::transaction(function () use ($request, &$data, &$package) {

            // Simpan gambar
            if ($request->hasFile('thumbnail')) {
                $data['thumbnail_path'] = $request->file('thumbnail')->store('rentcar', 'public');
            }

            // Simpan fitur sebagai JSON
            $cleanFeatures = [];
            foreach ($request->features ?? [] as $f) {
                $cleanFeatures[] = [
                    'name' => $f['name'],
                    'available' => isset($f['available']) ? true : false,
                ];
            }
            $data['features'] = $cleanFeatures;

            $package = RentCarPackage::create($data);

            // Simpan gallery foto
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $file) {
                    if ($file->isValid()) {
                        $path = $file->store('rentcar/gallery', 'public');
                        $package->photos()->create(['file_path' => $path]);
                    }
                }
            }

            if ($request->hasFile('seo_image')) {
                if ($package->seo_image_path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($package->seo_image_path);
                }
                $package->update(['seo_image_path' => $request->file('seo_image')->store('seo_images', 'public')]);
            }

            \App\Jobs\Translate\RentCarPackageToEn::dispatch($package->id)
                ->onQueue('translations')
                ->afterCommit();
        });

        return redirect()->route('admin.rent-car-packages.index')
            ->with('success', 'Package created successfully.');
    }


    public function edit(RentCarPackage $rent_car_package)
    {
        $package = $rent_car_package->load('photos');
        $categories = RentCarCategory::orderBy('name')->get();
        return view('admin.rentcar.edit', compact('package', 'categories'));
    }


    public function update(UpdateRentCarPackageRequest $request, RentCarPackage $rent_car_package)
    {
        $data = $request->validated();

        $customSlug = $request->filled('slug') ? Str::slug($request->input('slug')) : null;
        if (!empty($customSlug) && $customSlug !== $rent_car_package->slug) {
            $slug = $customSlug;
            $originalSlug = $slug;
            $counter = 1;
            while (RentCarPackage::where('slug', $slug)->where('id', '!=', $rent_car_package->id)->exists()) {
                $slug = "{$originalSlug}-{$counter}";
                $counter++;
            }
            $data['slug'] = $slug;
        } elseif ($data['title'] !== $rent_car_package->title || empty($rent_car_package->slug)) {
            $slug = Str::slug($data['title']);
            $originalSlug = $slug;
            $counter = 1;
            while (RentCarPackage::where('slug', $slug)->where('id', '!=', $rent_car_package->id)->exists()) {
                $slug = "{$originalSlug}-{$counter}";
                $counter++;
            }
            $data['slug'] = $slug;
        } else {
            $data['slug'] = $rent_car_package->slug;
        }

        DB::transaction(function () use ($request, &$data, $rent_car_package) {

            // Update thumbnail
            if ($request->hasFile('thumbnail')) {
                if ($rent_car_package->thumbnail_path) {
                    Storage::disk('public')->delete($rent_car_package->thumbnail_path);
                }
                $data['thumbnail_path'] = $request->file('thumbnail')->store('rentcar', 'public');
            }

            // Simpan foto gallery baru
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $file) {
                    if ($file->isValid()) {
                        $path = $file->store('rentcar/gallery', 'public');
                        $rent_car_package->photos()->create(['file_path' => $path]);
                    }
                }
            }

            // Update features
            $cleanFeatures = [];
            foreach ($request->features ?? [] as $f) {
                $cleanFeatures[] = [
                    'name' => $f['name'],
                    'available' => isset($f['available']) ? true : false,
                ];
            }
            $data['features'] = $cleanFeatures;

            $rent_car_package->update($data);


            if ($request->hasFile('seo_image')) {
                if ($rent_car_package->seo_image_path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($rent_car_package->seo_image_path);
                }
                $rent_car_package->update(['seo_image_path' => $request->file('seo_image')->store('seo_images', 'public')]);
            }

            \App\Jobs\Translate\RentCarPackageToEn::dispatch($rent_car_package->id)
                ->onQueue('translations')
                ->afterCommit();
        });

        return redirect()->route('admin.rent-car-packages.index')
            ->with('success', 'Package updated successfully.');
    }

    public function deletePhoto(\App\Models\RentCarPackagePhoto $photo)
    {
        if ($photo->file_path && Storage::disk('public')->exists($photo->file_path)) {
            Storage::disk('public')->delete($photo->file_path);
        }

        $photo->delete();

        return back()->with('success', 'Foto galeri berhasil dihapus.');
    }

    public function destroy(RentCarPackage $rent_car_package)
    {
        if ($rent_car_package->thumbnail_path) {
            Storage::disk('public')->delete($rent_car_package->thumbnail_path);
        }

        foreach ($rent_car_package->photos as $photo) {
            if ($photo->file_path && Storage::disk('public')->exists($photo->file_path)) {
                Storage::disk('public')->delete($photo->file_path);
            }
        }

        $rent_car_package->delete();

        return redirect()->route('admin.rent-car-packages.index')
            ->with('success', 'Package deleted.');
    }
}
