<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\TravelBusPackage;
use App\Models\TravelBusPackagePhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TravelBusPackageController extends Controller
{
    public function index()
    {
        $packages = TravelBusPackage::query()
            ->where('created_by_partner_id', auth()->id())
            ->with(['photos'])
            ->latest()
            ->paginate(15);

        return view('partner.travel-bus.index', compact('packages'));
    }

    public function create()
    {
        return view('partner.travel-bus.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'armada_name' => 'required|string|max:255',
            'armada_type' => 'required|string|max:255',
            'travel_type' => 'required|string|max:50',
            'departure_location' => 'required|string|max:255',
            'arrival_location' => 'required|string|max:255',
            'route_details' => 'nullable|array',
            'departure_time' => 'required',
            'arrival_time' => 'required',
            'arrival_days_offset' => 'nullable|integer|min:0|max:14',
            'duration_text' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'facilities' => 'nullable|array',
            'thumbnail' => 'nullable|image|max:2048',
            'gallery' => 'nullable|array',
            'gallery.*' => 'nullable|image|max:3072',
            'description' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
        ]);

        // Anti double-submit check
        $recent = TravelBusPackage::where('created_by_partner_id', auth()->id())
            ->where('armada_name', $data['armada_name'])
            ->where('departure_location', $data['departure_location'])
            ->where('arrival_location', $data['arrival_location'])
            ->where('departure_time', $data['departure_time'])
            ->where('created_at', '>=', now()->subSeconds(15))
            ->first();
        if ($recent) {
            return redirect()->route('partner.travel-bus.index')->with('success', 'Paket berhasil dikirim dan menunggu persetujuan admin.');
        }

        $slug = Str::slug($data['armada_name'] . ' ' . $data['departure_location'] . ' ' . $data['arrival_location']);
        $originalSlug = $slug;
        $counter = 1;
        while (TravelBusPackage::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }
        $data['slug'] = $slug;

        // Filter route_details
        $rawRoutes = (array) ($request->input('route_details', []));
        $cleanRoutes = array_values(array_filter($rawRoutes, fn($r) => !empty(trim((string)$r))));
        $data['route_details'] = count($cleanRoutes) > 0 ? $cleanRoutes : null;

        // Hitung durasi dan jam tiba dengan offset hari
        $daysOffset = (int) ($request->input('arrival_days_offset', 0));
        try {
            $dep = Carbon::parse($data['departure_time']);
            $arr = Carbon::parse($data['arrival_time'])->addDays($daysOffset);
            if ($daysOffset === 0 && $arr->lessThan($dep)) {
                $arr->addDay();
                $daysOffset = 1;
            }
            $data['duration_minutes'] = $dep->diffInMinutes($arr);
            $data['arrival_days_offset'] = $daysOffset;
        } catch (\Throwable $e) {
            $data['duration_minutes'] = 0;
            $data['arrival_days_offset'] = $daysOffset;
        }

        $data['created_by_partner_id'] = auth()->id();
        $data['is_active'] = false; // Memerlukan review admin
        $data['partner_review_status'] = 'pending';

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_path'] = $request->file('thumbnail')->store('travel-bus', 'public');
        }

        $package = TravelBusPackage::create($data);

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                if ($file) {
                    $path = $file->store('travel-bus/gallery', 'public');
                    $package->photos()->create(['photo_path' => $path]);
                }
            }
        }

        return redirect()->route('partner.travel-bus.index')->with('success', 'Paket berhasil dikirim dan menunggu persetujuan admin.');
    }

    public function edit(TravelBusPackage $travelBus)
    {
        if ($travelBus->created_by_partner_id !== auth()->id()) {
            abort(403);
        }

        $package = $travelBus->load('photos');
        return view('partner.travel-bus.edit', compact('package'));
    }

    public function update(Request $request, TravelBusPackage $travelBus)
    {
        if ($travelBus->created_by_partner_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'armada_name' => 'required|string|max:255',
            'armada_type' => 'required|string|max:255',
            'travel_type' => 'required|string|max:50',
            'departure_location' => 'required|string|max:255',
            'arrival_location' => 'required|string|max:255',
            'route_details' => 'nullable|array',
            'departure_time' => 'required',
            'arrival_time' => 'required',
            'arrival_days_offset' => 'nullable|integer|min:0|max:14',
            'duration_text' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'facilities' => 'nullable|array',
            'thumbnail' => 'nullable|image|max:2048',
            'gallery' => 'nullable|array',
            'gallery.*' => 'nullable|image|max:3072',
            'description' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
        ]);

        // Filter route_details
        $rawRoutes = (array) ($request->input('route_details', []));
        $cleanRoutes = array_values(array_filter($rawRoutes, fn($r) => !empty(trim((string)$r))));
        $data['route_details'] = count($cleanRoutes) > 0 ? $cleanRoutes : null;

        $daysOffset = (int) ($request->input('arrival_days_offset', 0));
        try {
            $dep = Carbon::parse($data['departure_time']);
            $arr = Carbon::parse($data['arrival_time'])->addDays($daysOffset);
            if ($daysOffset === 0 && $arr->lessThan($dep)) {
                $arr->addDay();
                $daysOffset = 1;
            }
            $data['duration_minutes'] = $dep->diffInMinutes($arr);
            $data['arrival_days_offset'] = $daysOffset;
        } catch (\Throwable $e) {
            $data['duration_minutes'] = 0;
            $data['arrival_days_offset'] = $daysOffset;
        }

        if ($request->hasFile('thumbnail')) {
            if ($travelBus->thumbnail_path) {
                Storage::disk('public')->delete($travelBus->thumbnail_path);
            }
            $data['thumbnail_path'] = $request->file('thumbnail')->store('travel-bus', 'public');
        }

        $travelBus->update($data);

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                if ($file) {
                    $path = $file->store('travel-bus/gallery', 'public');
                    $travelBus->photos()->create(['photo_path' => $path]);
                }
            }
        }

        return redirect()->route('partner.travel-bus.index')->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroy(TravelBusPackage $travelBus)
    {
        if ($travelBus->created_by_partner_id !== auth()->id()) {
            abort(403);
        }

        if ($travelBus->thumbnail_path) {
            Storage::disk('public')->delete($travelBus->thumbnail_path);
        }

        foreach ($travelBus->photos as $photo) {
            Storage::disk('public')->delete($photo->photo_path);
            $photo->delete();
        }

        $travelBus->delete();

        return redirect()->route('partner.travel-bus.index')->with('success', 'Paket berhasil dihapus.');
    }

    public function deletePhoto(TravelBusPackagePhoto $photo)
    {
        if ($photo->package->created_by_partner_id !== auth()->id()) {
            abort(403);
        }

        Storage::disk('public')->delete($photo->photo_path);
        $photo->delete();

        return back()->with('success', 'Foto galeri berhasil dihapus.');
    }
}
