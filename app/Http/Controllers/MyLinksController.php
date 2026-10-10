<?php

namespace App\Http\Controllers;

use App\Models\ShortLink;
use Illuminate\View\View;

class MyLinksController extends Controller
{
    public function index(): View
    {
        $links = ShortLink::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('my-links', compact('links'));
    }
}
