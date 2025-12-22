<?php

namespace App\Http\Controllers\Admin;

use App\Models\TourPackage;
use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Carbon\Carbon;


class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        if (!in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $status = 'pending';
        }

        $page = (int) $request->query('page', 1);
        if ($page < 1) $page = 1;

        $reviews = Review::query()
            ->where('status', $status)
            ->latest()
            ->paginate(20, ['*'], 'page', $page);

        $reviews->appends($request->query());

        return view('admin.reviews.index', compact('reviews', 'status'));
    }

    public function create()
    {
        // khusus paket tour (sesuai request lu)
        $packages = TourPackage::query()
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('admin.reviews.create', compact('packages'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tour_package_id' => ['required', 'exists:tour_packages,id'],
            'name'            => ['required', 'string', 'max:120'],
            'email'           => ['required', 'email', 'max:190'],
            'rating'          => ['required', 'integer', 'min:1', 'max:5'],
            'comment'         => ['required', 'string', 'max:2000'],
        ]);

        $package = TourPackage::findOrFail($data['tour_package_id']);

        // admin bikin review → langsung approved
        $package->reviews()->create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'rating'     => $data['rating'],
            'comment'    => $data['comment'],
            'status'     => 'approved',
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 512),
        ]);

        return redirect()
            ->route('admin.reviews.index', ['status' => 'approved'])
            ->with('success', 'Review admin berhasil ditambahkan dan langsung tampil.');
    }


    public function approve(Review $review)
    {
        $review->update(['status' => 'approved']);
        return back()->with('success', 'Review di-approve.');
    }

    public function reject(Review $review)
    {
        $review->update(['status' => 'rejected']);
        return back()->with('success', 'Review di-decline.');
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return back()->with('success', 'Review dihapus.');
    }
    public function edit(Review $review)
    {
        return view('admin.reviews.edit', compact('review'));
    }

    public function update(Request $request, Review $review)
    {
        // datetime-local dari admin dianggap WIB (Asia/Jakarta),
        // disimpan ke DB sebagai UTC (default config app timezone).
        $data = $request->validate([
            'comment'    => ['required', 'string', 'min:1', 'max:1000'],
            'created_at' => ['required', 'date_format:Y-m-d\TH:i'],
        ]);

        $review->comment = $data['comment'];

        $review->created_at = Carbon::createFromFormat('Y-m-d\TH:i', $data['created_at'], 'Asia/Jakarta')
            ->utc();

        $review->save();

        return redirect()
            ->route('admin.reviews.index', ['status' => $review->status])
            ->with('success', 'Review berhasil diupdate.');
    }
}
