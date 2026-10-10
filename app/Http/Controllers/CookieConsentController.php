<?php

namespace App\Http\Controllers;

use App\Models\CookieConsent;
use Illuminate\Http\Request;

class CookieConsentController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'action'     => ['required', 'in:accept_all,reject_all,custom'],
            'categories' => ['required', 'array'],
            'categories.necessary' => ['required', 'boolean'],
            'categories.analytics' => ['required', 'boolean'],
            'categories.marketing' => ['required', 'boolean'],
        ]);

        CookieConsent::create([
            'user_id'     => auth()->id(),
            'session_id'  => substr(session()->getId(), 0, 64),
            'categories'  => $data['categories'],
            'version'     => '1.0',
            'action'      => $data['action'],
            'ip_address'  => $request->ip(),
            'user_agent'  => substr((string) $request->userAgent(), 0, 500),
            'locale'      => app()->getLocale(),
            'accepted_at' => now(),
        ]);

        return response()->json(['ok' => true]);
    }
}