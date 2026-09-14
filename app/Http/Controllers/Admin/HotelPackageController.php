<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HotelPackage;
use App\Models\HotelPackagePhoto;
use App\Models\HotelRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HotelPackageController extends Controller
{
    public function index()
    {
        $packages = HotelPackage::query()
            ->with(['photos', 'rooms'])
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
            'property_type' => 'nullable|string|max:100',
            'label' => 'nullable|string|max:50',
            'price_per_night' => 'required|numeric|min:0',
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
            'rooms' => 'nullable|array',
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
        while (HotelPackage::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }
        $data['slug'] = $slug;

        $data['property_type'] = $request->input('property_type', 'Hotel');
        $data['is_active'] = $request->input('is_active', 1);
        $data['created_by_partner_id'] = null;
        $data['partner_review_status'] = 'approved';

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_path'] = $request->file('thumbnail')->store('hotel', 'public');
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

        $package = HotelPackage::create($data);

        // Multiple gallery photos
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('hotel/gallery', 'public');
                    $package->photos()->create(['file_path' => $path]);
                }
            }
        }

        // Hotel Rooms
        if ($request->has('rooms') && is_array($request->rooms)) {
            foreach ($request->rooms as $idx => $r) {
                $roomName = trim($r['name'] ?? '');
                if (empty($roomName)) continue;

                $roomPhotos = [];
                if (!empty($r['existing_photos'])) {
                    $epList = is_array($r['existing_photos']) ? $r['existing_photos'] : json_decode($r['existing_photos'], true);
                    if (is_array($epList)) {
                        foreach ($epList as $ep) {
                            if (!empty($ep) && is_string($ep)) $roomPhotos[] = $ep;
                        }
                    }
                } elseif (!empty($r['existing_photo'])) {
                    $roomPhotos[] = $r['existing_photo'];
                }

                if ($request->hasFile("rooms.{$idx}.photos")) {
                    $uploaded = $request->file("rooms.{$idx}.photos");
                    if (!is_array($uploaded)) $uploaded = [$uploaded];
                    foreach ($uploaded as $file) {
                        if (count($roomPhotos) >= 6) break;
                        if ($file && $file->isValid()) {
                            $roomPhotos[] = $file->store('hotel/rooms', 'public');
                        }
                    }
                } elseif ($request->hasFile("rooms.{$idx}.photo")) {
                    $file = $request->file("rooms.{$idx}.photo");
                    if ($file && $file->isValid() && count($roomPhotos) < 6) {
                        $roomPhotos[] = $file->store('hotel/rooms', 'public');
                    }
                }

                $roomPhotos = array_values(array_slice($roomPhotos, 0, 6));
                $roomPhotoPath = $roomPhotos[0] ?? null;

                $hasShower = isset($r['has_shower']) && ($r['has_shower'] == 1 || $r['has_shower'] === '1' || $r['has_shower'] === 'on' || $r['has_shower'] === true);
                $hasWifi = isset($r['has_wifi']) && ($r['has_wifi'] == 1 || $r['has_wifi'] === '1' || $r['has_wifi'] === 'on' || $r['has_wifi'] === true);
                $hasBreakfast = isset($r['has_breakfast']) && ($r['has_breakfast'] == 1 || $r['has_breakfast'] === '1' || $r['has_breakfast'] === 'on' || $r['has_breakfast'] === true);
                $isReady = isset($r['is_ready']) && ($r['is_ready'] == 1 || $r['is_ready'] === '1' || $r['is_ready'] === 'on' || $r['is_ready'] === true);

                $roomFacilities = [];
                if (!empty($r['facilities'])) {
                    if (is_array($r['facilities'])) {
                        $roomFacilities = array_filter(array_map('trim', $r['facilities']));
                    } else {
                        $roomFacilities = array_filter(array_map('trim', explode(',', $r['facilities'])));
                    }
                }

                $package->rooms()->create([
                    'name' => $roomName,
                    'room_size' => trim($r['room_size'] ?? ''),
                    'bed_type' => trim($r['bed_type'] ?? ''),
                    'max_guests' => max(1, (int)($r['max_guests'] ?? 2)),
                    'has_shower' => $hasShower,
                    'has_wifi' => $hasWifi,
                    'has_breakfast' => $hasBreakfast,
                    'price' => (float)($r['price'] ?? 0),
                    'price_with_breakfast' => !empty($r['price_with_breakfast']) ? (float)$r['price_with_breakfast'] : null,
                    'original_price' => !empty($r['original_price']) ? (float)$r['original_price'] : null,
                    'available_rooms' => max(0, (int)($r['available_rooms'] ?? 1)),
                    'facilities' => $roomFacilities,
                    'description' => trim($r['description'] ?? '') ?: null,
                    'photo_path' => $roomPhotoPath,
                    'photos' => $roomPhotos,
                    'is_ready' => $isReady,
                    'sort_order' => $idx,
                ]);
            }
        }

        return redirect()->route('admin.hotel-packages.index')
            ->with('success', 'Paket hotel/vila berhasil dibuat.');
    }

    public function edit(HotelPackage $hotel_package)
    {
        $package = $hotel_package->load(['photos', 'rooms']);
        return view('admin.hotel.edit', compact('package'));
    }

    public function update(Request $request, HotelPackage $hotel_package)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'property_type' => 'nullable|string|max:100',
            'label' => 'nullable|string|max:50',
            'price_per_night' => 'required|numeric|min:0',
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
            'rooms' => 'nullable|array',
            'long_description' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_keywords' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'social_title' => 'nullable|string|max:255',
            'social_description' => 'nullable|string',
            'seo_image' => 'nullable|image|max:2048',
        ]);

        if ($data['title'] !== $hotel_package->title) {
            $slug = Str::slug($data['title']);
            $originalSlug = $slug;
            $counter = 1;
            while (HotelPackage::where('slug', $slug)->where('id', '!=', $hotel_package->id)->exists()) {
                $slug = "{$originalSlug}-{$counter}";
                $counter++;
            }
            $data['slug'] = $slug;
        }

        $data['property_type'] = $request->input('property_type', $hotel_package->property_type ?: 'Hotel');
        $data['is_active'] = $request->input('is_active', $hotel_package->is_active);

        if ($request->hasFile('thumbnail')) {
            if (!empty($hotel_package->thumbnail_path)) {
                Storage::disk('public')->delete($hotel_package->thumbnail_path);
            }
            $data['thumbnail_path'] = $request->file('thumbnail')->store('hotel', 'public');
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
            if (!empty($hotel_package->seo_image_path)) {
                Storage::disk('public')->delete($hotel_package->seo_image_path);
            }
            $data['seo_image_path'] = $request->file('seo_image')->store('seo_images', 'public');
        }

        $hotel_package->update($data);

        // Upload additional gallery photos
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('hotel/gallery', 'public');
                    $hotel_package->photos()->create(['file_path' => $path]);
                }
            }
        }

        // Hotel Rooms synchronization
        $submittedRoomIds = [];
        if ($request->has('rooms') && is_array($request->rooms)) {
            foreach ($request->rooms as $idx => $r) {
                $roomName = trim($r['name'] ?? '');
                if (empty($roomName)) continue;

                $roomId = !empty($r['id']) ? (int)$r['id'] : null;
                $hasShower = isset($r['has_shower']) && ($r['has_shower'] == 1 || $r['has_shower'] === '1' || $r['has_shower'] === 'on' || $r['has_shower'] === true);
                $hasWifi = isset($r['has_wifi']) && ($r['has_wifi'] == 1 || $r['has_wifi'] === '1' || $r['has_wifi'] === 'on' || $r['has_wifi'] === true);
                $hasBreakfast = isset($r['has_breakfast']) && ($r['has_breakfast'] == 1 || $r['has_breakfast'] === '1' || $r['has_breakfast'] === 'on' || $r['has_breakfast'] === true);
                $isReady = isset($r['is_ready']) && ($r['is_ready'] == 1 || $r['is_ready'] === '1' || $r['is_ready'] === 'on' || $r['is_ready'] === true);

                $roomFacilities = [];
                if (!empty($r['facilities'])) {
                    if (is_array($r['facilities'])) {
                        $roomFacilities = array_filter(array_map('trim', $r['facilities']));
                    } else {
                        $roomFacilities = array_filter(array_map('trim', explode(',', $r['facilities'])));
                    }
                }

                $roomData = [
                    'name' => $roomName,
                    'room_size' => trim($r['room_size'] ?? ''),
                    'bed_type' => trim($r['bed_type'] ?? ''),
                    'max_guests' => max(1, (int)($r['max_guests'] ?? 2)),
                    'has_shower' => $hasShower,
                    'has_wifi' => $hasWifi,
                    'has_breakfast' => $hasBreakfast,
                    'price' => (float)($r['price'] ?? 0),
                    'price_with_breakfast' => !empty($r['price_with_breakfast']) ? (float)$r['price_with_breakfast'] : null,
                    'original_price' => !empty($r['original_price']) ? (float)$r['original_price'] : null,
                    'available_rooms' => max(0, (int)($r['available_rooms'] ?? 1)),
                    'facilities' => $roomFacilities,
                    'description' => trim($r['description'] ?? '') ?: null,
                    'is_ready' => $isReady,
                    'sort_order' => $idx,
                ];

                $roomPhotos = [];
                if (!empty($r['existing_photos'])) {
                    $epList = is_array($r['existing_photos']) ? $r['existing_photos'] : json_decode($r['existing_photos'], true);
                    if (is_array($epList)) {
                        foreach ($epList as $ep) {
                            if (!empty($ep) && is_string($ep)) $roomPhotos[] = $ep;
                        }
                    }
                } elseif (!empty($r['existing_photo'])) {
                    $roomPhotos[] = $r['existing_photo'];
                }

                if ($request->hasFile("rooms.{$idx}.photos")) {
                    $uploaded = $request->file("rooms.{$idx}.photos");
                    if (!is_array($uploaded)) $uploaded = [$uploaded];
                    foreach ($uploaded as $file) {
                        if (count($roomPhotos) >= 6) break;
                        if ($file && $file->isValid()) {
                            $roomPhotos[] = $file->store('hotel/rooms', 'public');
                        }
                    }
                } elseif ($request->hasFile("rooms.{$idx}.photo")) {
                    $file = $request->file("rooms.{$idx}.photo");
                    if ($file && $file->isValid() && count($roomPhotos) < 6) {
                        $roomPhotos[] = $file->store('hotel/rooms', 'public');
                    }
                }

                $roomPhotos = array_values(array_slice($roomPhotos, 0, 6));
                $roomData['photos'] = $roomPhotos;
                $roomData['photo_path'] = $roomPhotos[0] ?? null;

                if ($roomId && ($existingRoom = HotelRoom::where('hotel_package_id', $hotel_package->id)->find($roomId))) {
                    // Delete removed photo files
                    $oldPhotos = $existingRoom->all_photos;
                    foreach ($oldPhotos as $op) {
                        if (!in_array($op, $roomPhotos) && !empty($op)) {
                            Storage::disk('public')->delete($op);
                        }
                    }
                    $existingRoom->update($roomData);
                    $submittedRoomIds[] = $existingRoom->id;
                } else {
                    $newRoom = $hotel_package->rooms()->create($roomData);
                    $submittedRoomIds[] = $newRoom->id;
                }
            }
        }

        // Delete omitted rooms
        $roomsToDelete = HotelRoom::where('hotel_package_id', $hotel_package->id)
            ->whereNotIn('id', $submittedRoomIds)
            ->get();
        foreach ($roomsToDelete as $delRoom) {
            foreach ($delRoom->all_photos as $p) {
                if (!empty($p)) {
                    Storage::disk('public')->delete($p);
                }
            }
            $delRoom->delete();
        }

        return redirect()->route('admin.hotel-packages.index')
            ->with('success', 'Paket hotel/vila berhasil diperbarui.');
    }

    public function deletePhoto($photo)
    {
        $photoItem = HotelPackagePhoto::findOrFail($photo);

        if ($photoItem->file_path) {
            Storage::disk('public')->delete($photoItem->file_path);
        }
        $photoItem->delete();

        return back()->with('success', 'Foto galeri berhasil dihapus.');
    }

    public function destroy(HotelPackage $hotel_package)
    {
        if (!empty($hotel_package->thumbnail_path)) {
            Storage::disk('public')->delete($hotel_package->thumbnail_path);
        }
        if (!empty($hotel_package->seo_image_path)) {
            Storage::disk('public')->delete($hotel_package->seo_image_path);
        }

        foreach ($hotel_package->photos as $photo) {
            if (!empty($photo->file_path)) {
                Storage::disk('public')->delete($photo->file_path);
            }
            $photo->delete();
        }

        foreach ($hotel_package->rooms as $room) {
            if (!empty($room->photo_path)) {
                Storage::disk('public')->delete($room->photo_path);
            }
            $room->delete();
        }

        $hotel_package->delete();

        return redirect()->route('admin.hotel-packages.index')
            ->with('success', 'Paket hotel/vila berhasil dihapus.');
    }
}
