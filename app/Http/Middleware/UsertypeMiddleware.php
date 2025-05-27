<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UsertypeMiddleware
{
    public function handle(Request $request, Closure $next, ...$usertypes): Response
    {
        $user = auth()->user();

        if (!$user || !in_array($user->usertype, $usertypes)) {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
