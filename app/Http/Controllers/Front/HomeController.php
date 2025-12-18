<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClientLogo;

class HomeController extends Controller
{
    public function index()
    {
        $clientLogos = ClientLogo::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('front.home', compact('clientLogos'));
    }
}
