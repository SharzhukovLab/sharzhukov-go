<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectToAccountForAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            $return = urlencode($request->fullUrl());

            return redirect()->away(
                'https://account-main.sharzhukov.com/login?return=' . $return
            );
        }

        return $next($request);
    }
}
