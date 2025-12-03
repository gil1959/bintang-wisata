<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TourPackageController extends Controller
{
    public function index(Request $request)
    {
        $query = TourPackage::query()->orderByDesc('created_at');

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('destination', 'like', "%{$search}%");
            });
        }

        $packages = $query->paginate(10);

        return view('admin.tour-packages.index', compact('packages'));
    }

    public function create()
    {
        $package = new TourPackage([
            'category' => 'domestic',
            'include_flight_option' => false,
            'is_active' => true,
        ]);

        // relasi kosong, nanti di-view dikasih default 1 baris
        $package->setRelation('priceTiers', collect());

        return view('admin.tour-packages.form', compact('package'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $package = TourPackage::create($data);

        // ===== SIMPAN HARGA TIER =====
        $this->syncPriceTiers($request, $package);

        return redirect()
            ->route('admin.tour-packages.edit', $package)
            ->with('success', 'Paket wisata berhasil dibuat. Lanjut lengkapi itinerary & gambar jika perlu.');
    }

    public function edit(TourPackage $tourPackage)
    {
        $package = $tourPackage->load('priceTiers');

        return view('admin.tour-packages.form', compact('package'));
    }

    public function update(Request $request, TourPackage $tourPackage)
    {
        $data = $this->validateData($request, $tourPackage->id);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $tourPackage->update($data);

        // reset & isi ulang harga tier
        $tourPackage->priceTiers()->delete();
        $this->syncPriceTiers($request, $tourPackage);

        return redirect()
            ->route('admin.tour-packages.edit', $tourPackage)
            ->with('success', 'Data paket wisata berhasil diperbarui.');
    }

    public function destroy(TourPackage $tourPackage)
    {
        $tourPackage->delete();

        return redirect()
            ->route('admin.tour-packages.index')
            ->with('success', 'Paket wisata berhasil dihapus.');
    }

    protected function validateData(Request $request, $ignoreId = null): array
    {
        $data = $request->validate([
            'title'   => ['required', 'string', 'max:255'],
            'slug'    => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('tour_packages', 'slug')->ignore($ignoreId),
            ],
            'category'              => ['required', 'in:domestic,international'],
            'destination'           => ['nullable', 'string', 'max:255'],
            'duration_text'         => ['nullable', 'string', 'max:255'],
            'short_description'     => ['nullable', 'string'],
            'description'           => ['nullable', 'string'],

            'include_flight_option'     => ['nullable', 'boolean'],
            'flight_surcharge_per_pax' => ['nullable', 'numeric', 'min:0'],
            'is_active'                 => ['nullable', 'boolean'],

            'thumbnail_path'        => ['nullable', 'string', 'max:255'],
            'meta_title'            => ['nullable', 'string', 'max:255'],
            'meta_description'      => ['nullable', 'string'],

            'price_tiers'                       => ['array'],
            'price_tiers.*.audience_type'       => ['nullable', 'in:domestic,wna'],
            'price_tiers.*.min_pax'             => ['nullable', 'integer', 'min:1'],
            'price_tiers.*.max_pax'             => ['nullable', 'integer', 'min:1'],
            'price_tiers.*.price_per_pax'       => ['nullable', 'numeric', 'min:0'],
        ]);

        // checkbox → boolean
        $data['include_flight_option'] = $request->boolean('include_flight_option');
        $data['is_active']             = $request->boolean('is_active');

        return $data;
    }


    /**
     * Simpan harga DOM & WNA (tanpa tiket + dengan tiket).
     */
    protected function syncPriceTiers(Request $request, TourPackage $package): void
    {
        $priceTiers = $request->input('price_tiers', []);

        foreach ($priceTiers as $tier) {
            $audience = $tier['audience_type'] ?? null;
            $minPax   = $tier['min_pax'] ?? null;
            $maxPax   = $tier['max_pax'] ?? null;
            $price    = $tier['price_per_pax'] ?? null;

            // skip baris kosong
            if (! $audience || ! $minPax || ! $maxPax || $price === null || $price === '') {
                continue;
            }

            $package->priceTiers()->create([
                'audience_type' => $audience,           // domestic / wna
                'min_pax'       => (int) $minPax,
                'max_pax'       => (int) $maxPax,
                'price_per_pax' => (float) $price,      // HARGA PER PAKET
            ]);
        }
    }
}
