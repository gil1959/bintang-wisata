<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\TravelBusPackage;
use Illuminate\Http\Request;

class TravelBusController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $sort = $request->query('sort', 'latest');

        $query = TravelBusPackage::query()
            ->where('is_active', true)
            ->where('partner_review_status', 'approved')
            ->with([
                'photos',
                'reviews' => function ($r) {
                    $r->where('status', 'approved');
                }
            ]);

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('armada_name', 'like', "%{$q}%")
                  ->orWhere('departure_location', 'like', "%{$q}%")
                  ->orWhere('arrival_location', 'like', "%{$q}%")
                  ->orWhere('armada_type', 'like', "%{$q}%");
            });
        }

        if ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->latest();
        }

        $packages = $query->paginate(10)->withQueryString();

        return view('front.travel-bus.index', compact('packages', 'q', 'sort'));
    }

    public function show($slug)
    {
        $package = TravelBusPackage::with([
                'photos',
                'partner',
                'reviews' => function ($q) {
                    $q->where('status', 'approved')->latest();
                }
            ])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('front.travel-bus.show', compact('package'));
    }
}
