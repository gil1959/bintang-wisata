<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class LegalController extends Controller
{
    public function privacy()
    {
        $title = Setting::getValue('legal_privacy_title', 'Privacy Policy');
        $html  = Setting::getValue('legal_privacy_html', view('front.pages.legal-defaults.privacy')->render());

        return view('front.pages.privacy-policy', compact('title', 'html'));
    }

    public function terms()
    {
        $title = Setting::getValue('legal_terms_title', 'Terms & Conditions');
        $html  = Setting::getValue('legal_terms_html', view('front.pages.legal-defaults.terms')->render());

        return view('front.pages.terms-conditions', compact('title', 'html'));
    }

    public function contact()
    {
        $title = Setting::getValue('legal_contact_title', 'Contact');
        $html  = Setting::getValue('legal_contact_html', view('front.pages.legal-defaults.contact')->render());

        return view('front.pages.contact', compact('title', 'html'));
    }
}
