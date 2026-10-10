<?php

namespace App\Http\Controllers;

use App\Models\ShortLink;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'target_url'  => ['required', 'url', 'max:2000'],
            'slug'        => ['nullable', 'alpha_dash', 'min:3', 'max:64', 'unique:short_links,slug'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $slug = $data['slug'] ?? $this->generateSlug();

        $link = ShortLink::create([
            'user_id'     => auth()->id(),
            'slug'        => $slug,
            'target_url'  => $data['target_url'],
            'description' => $data['description'] ?? null,
            'is_active'   => true,
            'source'      => auth()->check() ? 'manual' : 'anonymous',
        ]);

        return redirect()->route('link.show', $link->slug)
            ->with('created', true);
    }

    private function generateSlug(): string
    {
        do {
            $slug = Str::random(7);
        } while (ShortLink::where('slug', $slug)->exists());

        return $slug;
    }
}
