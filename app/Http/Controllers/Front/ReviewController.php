<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\RentCarPackage;
use App\Models\TourPackage;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        // Honeypot
        if ($request->filled('website')) {
            abort(422, 'Invalid submission.');
        }

        // Time-trap (anti bot submit super cepat)
        $startedAt = (int) $request->input('form_started_at', 0);
        if ($startedAt > 0) {
            $elapsed = time() - $startedAt;
            if ($elapsed < 3) {
                abort(422, 'Submission too fast.');
            }
        }

        $data = $request->validate([
            'reviewable_type' => ['required', 'in:tour,rent'],
            'reviewable_id'   => ['required', 'integer'],
            'name'            => ['required', 'string', 'max:80'],
            'email'           => ['required', 'email', 'max:120'],
            'rating'          => ['required', 'integer', 'between:1,5'],
            'comment'         => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $model = match ($data['reviewable_type']) {
            'tour' => TourPackage::findOrFail($data['reviewable_id']),
            'rent' => RentCarPackage::findOrFail($data['reviewable_id']),
        };

        // Anti spam soft: email yang sama untuk item yang sama dalam 10 menit diblok
        $exists = $model->reviews()
            ->where('email', $data['email'])
            ->where('created_at', '>=', now()->subMinutes(10))
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['email' => 'Email ini baru saja mengirim ulasan untuk paket ini. Coba lagi beberapa menit.'])
                ->withInput();
        }

        $model->reviews()->create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'rating'     => $data['rating'],
            'comment'    => $data['comment'],
            'status'     => 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 512),
        ]);

        return back()->with('success', 'Ulasan berhasil dikirim dan menunggu persetujuan admin.');
    }
}
