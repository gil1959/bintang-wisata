<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTourPackageRequest;
use App\Http\Requests\Admin\UpdateTourPackageRequest;
use App\Models\TourCategory;
use App\Models\TourPackage;
use App\Models\TourPackagePhoto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TourPackageController extends Controller
{
    public function index()
    {
        $packages = TourPackage::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.tour-packages.index', compact('packages'));
    }

    public function create()
    {
        $categories = TourCategory::orderBy('name')->get();
        return view('admin.tour-packages.create', compact('categories'));
    }

    public function store(StoreTourPackageRequest $request)
    {

        DB::transaction(function () use ($request) {

            $package = TourPackage::create([
                'title'            => $request->title,
                'label' => $request->label,

                'rating_value' => $request->rating_value ?? 5,
                'rating_count' => $request->rating_count ?? 0,
                'slug'             => $request->slug,
                'category_id'      => $request->category_id,
                'duration_text'    => $request->duration_text,
                'destination'      => $request->destination,
                'long_description' => $request->long_description,
                'includes'         => $request->includes ?? [],
                'excludes'         => $request->excludes ?? [],
                'flight_info'      => $request->flight_info,
                'seo_title'        => $request->seo_title,
                'seo_description'  => $request->seo_description,
                'seo_keywords'     => $request->seo_keywords,
            ]);

            // =====================
            // SAVE THUMBNAIL
            // =====================
            if ($request->hasFile('thumbnail')) {
                $thumbPath = $request->file('thumbnail')->store('tour-packages', 'public');
                $package->update(['thumbnail_path' => $thumbPath]);
            }

            // =====================
            // SAVE GALLERY
            // =====================
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $img) {
                    $path = $img->store('tour-packages', 'public');
                    $package->photos()->create([
                        'file_path' => $path   // FIX: field berubah
                    ]);
                }
            }

            // =====================
            // SAVE ITINERARIES
            // =====================
            if (!empty($request->itineraries)) {
                foreach ($request->itineraries as $row) {
                    $package->itineraries()->create([
                        'title' => $row['title'],
                    ]);
                }
            }

            // =====================
            // SAVE TIERS
            // =====================
            $this->syncTiers($package, $request->tiers);
        });

        return redirect()->route('admin.tour-packages.index')
            ->with('success', 'Paket berhasil dibuat.');
    }

    public function edit(TourPackage $tour_package)
    {
        $categories = TourCategory::orderBy('name')->get();
        $package = $tour_package->load(['tiers', 'itineraries', 'photos']);
        return view('admin.tour-packages.edit', compact('package', 'categories'));
    }

    public function update(UpdateTourPackageRequest $request, TourPackage $tour_package)
    {
        DB::transaction(function () use ($request, $tour_package) {

            $tour_package->update([
                'title'            => $request->title,
                'label' => $request->label,

                'rating_value' => $request->rating_value ?? $tour_package->rating_value ?? 5,
                'rating_count' => $request->rating_count ?? $tour_package->rating_count ?? 0,
                'slug'             => $request->slug,
                'category_id'      => $request->category_id,
                'duration_text'    => $request->duration_text,
                'destination'      => $request->destination,
                'long_description' => $request->long_description,
                'includes'         => $request->includes ?? [],
                'excludes'         => $request->excludes ?? [],
                'flight_info'      => $request->flight_info,
                'seo_title'        => $request->seo_title,
                'seo_description'  => $request->seo_description,
                'seo_keywords'     => $request->seo_keywords,
            ]);

            // UPDATE THUMBNAIL
            if ($request->hasFile('thumbnail')) {
                if ($tour_package->thumbnail_path) {
                    Storage::disk('public')->delete($tour_package->thumbnail_path);
                }
                $newThumb = $request->file('thumbnail')->store('tour-packages', 'public');
                $tour_package->update(['thumbnail_path' => $newThumb]);
            }

            // ADD NEW GALLERY PHOTOS
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $img) {
                    $path = $img->store('tour-packages', 'public');
                    $tour_package->photos()->create(['file_path' => $path]);
                }
            }

            $this->syncItineraries($tour_package, $request->itineraries);
            $this->syncTiers($tour_package, $request->tiers);
        });

        return redirect()->route('admin.tour-packages.index')
            ->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroy(TourPackage $tour_package)
    {
        $tour_package->delete();
        return redirect()->route('admin.tour-packages.index')
            ->with('success', 'Paket berhasil dihapus.');
    }

    private function syncItineraries(TourPackage $package, ?array $items)
    {
        $existing = $package->itineraries()->pluck('id')->toArray();
        $submitted = [];

        if ($items) {
            foreach ($items as $row) {
                if (!empty($row['id'])) {
                    $submitted[] = (int)$row['id'];
                    $package->itineraries()->where('id', $row['id'])->update([
                        'title' => $row['title'],
                    ]);
                } else {
                    $new = $package->itineraries()->create([
                        'title' => $row['title'],
                    ]);
                    $submitted[] = $new->id;
                }
            }
        }

        $toDelete = array_diff($existing, $submitted);

        if ($toDelete) {
            $package->itineraries()->whereIn('id', $toDelete)->delete();
        }
    }

    private function syncTiers(TourPackage $package, array $tiers)
    {
        $existing = $package->tiers()->pluck('id')->toArray();
        $submitted = [];

        foreach (['domestic', 'international'] as $type) {

            foreach ($tiers[$type] ?? [] as $row) {

                if (!empty($row['id'])) {
                    $submitted[] = (int)$row['id'];

                    $package->tiers()->where('id', $row['id'])->update([
                        'type'       => $row['type'],
                        'is_custom'  => (bool)$row['is_custom'],
                        'min_people' => $row['is_custom'] ? 2 : $row['min_people'],
                        'max_people' => $row['is_custom'] ? null : $row['max_people'],
                        'price'      => $row['price'],
                    ]);
                } else {
                    $new = $package->tiers()->create([
                        'type'       => $row['type'],
                        'is_custom'  => (bool)$row['is_custom'],
                        'min_people' => $row['is_custom'] ? 2 : $row['min_people'],
                        'max_people' => $row['is_custom'] ? null : $row['max_people'],
                        'price'      => $row['price'],
                    ]);
                    $submitted[] = $new->id;
                }
            }
        }

        $toDelete = array_diff($existing, $submitted);
        if ($toDelete) {
            $package->tiers()->whereIn('id', $toDelete)->delete();
        }
    }

    public function deletePhoto(TourPackagePhoto $photo)
    {
        if (Storage::disk('public')->exists($photo->file_path)) {
            Storage::disk('public')->delete($photo->file_path);
        }

        $photo->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }
}
