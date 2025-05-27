<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'Akses ditolak.');
        }

        // Jika user Super Admin, izinkan semua akses
        if ($user->role && $user->role->name === 'Super Admin') {
            return $next($request);
        }

        // Cek apakah role user sesuai parameter middleware
        foreach ($roles as $role) {
            if ($user->role && $user->role->name === $role) {
                return $next($request);
            }
        }

        abort(403, 'Akses ditolak.');
    }
}
