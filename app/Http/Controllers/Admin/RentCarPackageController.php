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
        $data['slug'] = Str::slug($data['title']);
        // Simpan gambar
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_path'] =
                $request->file('thumbnail')->store('rentcar', 'public');
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


        RentCarPackage::create($data);

        return redirect()->route('admin.rent-car-packages.index')
            ->with('success', 'Package created successfully.');
    }

    public function edit(RentCarPackage $rent_car_package)
    {
        $package = $rent_car_package;
        $categories = RentCarCategory::orderBy('name')->get();
        return view('admin.rentcar.edit', compact('package', 'categories'));
    }


    public function update(UpdateRentCarPackageRequest $request, RentCarPackage $rent_car_package)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']);
        // Update thumbnail
        if ($request->hasFile('thumbnail')) {
            if ($rent_car_package->thumbnail_path) {
                Storage::disk('public')->delete($rent_car_package->thumbnail_path);
            }
            $data['thumbnail_path'] =
                $request->file('thumbnail')->store('rentcar', 'public');
        }

        $cleanFeatures = [];

        foreach ($request->features ?? [] as $f) {
            $cleanFeatures[] = [
                'name' => $f['name'],
                'available' => isset($f['available']) ? true : false,
            ];
        }

        $data['features'] = $cleanFeatures;


        $rent_car_package->update($data);

        return redirect()->route('admin.rent-car-packages.index')
            ->with('success', 'Package updated successfully.');
    }

    public function destroy(RentCarPackage $rent_car_package)
    {
        if ($rent_car_package->thumbnail_path) {
            Storage::disk('public')->delete($rent_car_package->thumbnail_path);
        }

        $rent_car_package->delete();

        return redirect()->route('admin.rent-car-packages.index')
            ->with('success', 'Package deleted.');
    }
}
