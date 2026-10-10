<?php

namespace App\Http\Controllers;

use App\Models\LinkClick;
use App\Models\ShortLink;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function go(Request $request, string $slug)
    {
        $link = ShortLink::where('slug', $slug)->firstOrFail();

        abort_unless($link->is_active, 410, 'Link disabled');
        abort_if($link->isExpired(), 410, 'Link expired');

        $link->increment('clicks_count');

        LinkClick::create([
            'short_link_id' => $link->id,
            'ip'            => $request->ip(),
            'user_agent'    => substr((string) $request->userAgent(), 0, 500),
            'referrer'      => substr((string) $request->header('referer'), 0, 500),
        ]);

        return redirect()->away($link->target_url);
    }

    public function show(string $slug)
    {
        $link = ShortLink::where('slug', $slug)->firstOrFail();

        return view('link.show', compact('link'));
    }
}
