<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

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
}
