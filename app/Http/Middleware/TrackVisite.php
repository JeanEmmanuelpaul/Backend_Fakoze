<?php

namespace App\Http\Middleware;

use App\Models\Visite;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisite
{
    public function handle(Request $request, Closure $next)
    {
        // Ne pas tracker les routes admin/api
        if (!$request->is('api/*')) {
            Visite::create([
                'ip'         => $request->ip(),
                'page'       => $request->path(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return $next($request);
    }
}
