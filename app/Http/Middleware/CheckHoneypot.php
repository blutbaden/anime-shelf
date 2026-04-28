<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckHoneypot
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('POST') && $request->filled('_hp')) {
            return back();
        }

        return $next($request);
    }
}
