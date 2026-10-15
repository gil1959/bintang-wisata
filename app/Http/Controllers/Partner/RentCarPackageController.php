<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\RentCarPackage;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\StoreRentCarPackageRequest;
use App\Http\Requests\Admin\UpdateRentCarPackageRequest;
use App\Models\RentCarCategory;
use Illuminate\Support\Str;

class RentCarPackageController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->partner_type === 'agency_rental_mobil', 403);

        $packages = RentCarPackage::query()
            ->where('created_by_partner_id', auth()->id())
            ->latest()
            ->get();

        return view('partner.rentcar.index', compact('packages'));
    }

    public function create()
    {
        abort_unless(auth()->user()->partner_type === 'agency_rental_mobil', 403);

        $categories = RentCarCategory::query()
    ->where(function ($q) {
        $q->whereNull('created_by_partner_id')
          ->orWhere('created_by_partner_id', auth()->id());
    })
    ->orderBy('name')
    ->get();

return view('partner.rentcar.create', compact('categories'));

    }

    public function store(StoreRentCarPackageRequest $request)
    {
        abort_unless(auth()->user()->partner_type === 'agency_rental_mobil', 403);

        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']);

        // wajib
        $data['is_active'] = 0;
        $data['created_by_partner_id'] = auth()->id();
        $data['partner_review_status'] = 'pending';

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_path'] = $request->file('thumbnail')->store('rentcar', 'public');
        }

        $cleanFeatures = [];
        foreach ($request->features ?? [] as $f) {
            $cleanFeatures[] = [
                'name' => $f['name'],
                'available' => isset($f['available']) ? true : false,
            ];
        }
        $data['features'] = $cleanFeatures;

        if ($request->hasFile('seo_image')) {
            $data['seo_image_path'] = $request->file('seo_image')->store('seo_images', 'public');
        }

        $package = RentCarPackage::create($data);

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('rentcar/gallery', 'public');
                    $package->photos()->create(['file_path' => $path]);
                }
            }
        }

        return redirect()->route('partner.rent-car-packages.index')
            ->with('success', 'Package created and waiting admin review.');
    }

    public function edit(RentCarPackage $rent_car_package)
    {
        abort_unless(auth()->user()->partner_type === 'agency_rental_mobil', 403);

        abort_unless((int)$rent_car_package->created_by_partner_id === (int)auth()->id(), 403);

        $package = $rent_car_package->load('photos');
        $categories = RentCarCategory::query()
    ->where(function ($q) {
        $q->whereNull('created_by_partner_id')
          ->orWhere('created_by_partner_id', auth()->id());
    })
    ->orderBy('name')
    ->get();

return view('partner.rentcar.edit', compact('package', 'categories'));

    }

    public function update(UpdateRentCarPackageRequest $request, RentCarPackage $rent_car_package)
    {
        abort_unless(auth()->user()->partner_type === 'agency_rental_mobil', 403);
        abort_unless((int)$rent_car_package->created_by_partner_id === (int)auth()->id(), 403);

        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']);

        if ($request->hasFile('thumbnail')) {
            if ($rent_car_package->thumbnail_path) {
                Storage::disk('public')->delete($rent_car_package->thumbnail_path);
            }
            $data['thumbnail_path'] = $request->file('thumbnail')->store('rentcar', 'public');
        }

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('rentcar/gallery', 'public');
                    $rent_car_package->photos()->create(['file_path' => $path]);
                }
            }
        }

        $cleanFeatures = [];
        foreach ($request->features ?? [] as $f) {
            $cleanFeatures[] = [
                'name' => $f['name'],
                'available' => isset($f['available']) ? true : false,
            ];
        }
        $data['features'] = $cleanFeatures;

        if ($request->hasFile('seo_image')) {
            if ($rent_car_package->seo_image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($rent_car_package->seo_image_path);
            }
            $data['seo_image_path'] = $request->file('seo_image')->store('seo_images', 'public');
        }

        $rent_car_package->update($data);

        // paksa pending lagi
        $rent_car_package->update([
            'is_active'            => 0,
            'partner_review_status'=> 'pending',
            'partner_review_note'  => null,
            'partner_reviewed_by'  => null,
            'partner_reviewed_at'  => null,
        ]);

        return redirect()->route('partner.rent-car-packages.index')
            ->with('success', 'Package updated and waiting admin review.');
    }

    public function deletePhoto(\App\Models\RentCarPackagePhoto $photo)
    {
        abort_unless(auth()->user()->partner_type === 'agency_rental_mobil', 403);
        abort_unless((int)$photo->package->created_by_partner_id === (int)auth()->id(), 403);

        if ($photo->file_path && Storage::disk('public')->exists($photo->file_path)) {
            Storage::disk('public')->delete($photo->file_path);
        }

        $photo->delete();

        return back()->with('success', 'Foto galeri berhasil dihapus.');
    }

    public function destroy(RentCarPackage $rent_car_package)
    {
        abort_unless(auth()->user()->partner_type === 'agency_rental_mobil', 403);
        abort_unless((int)$rent_car_package->created_by_partner_id === (int)auth()->id(), 403);

        if ($rent_car_package->thumbnail_path) {
            Storage::disk('public')->delete($rent_car_package->thumbnail_path);
        }

        foreach ($rent_car_package->photos as $photo) {
            if ($photo->file_path && Storage::disk('public')->exists($photo->file_path)) {
                Storage::disk('public')->delete($photo->file_path);
            }
        }

        $rent_car_package->delete();

        return redirect()->route('partner.rent-car-packages.index')
            ->with('success', 'Package deleted.');
    }
}
