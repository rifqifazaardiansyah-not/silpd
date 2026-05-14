<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Middleware untuk mengecek role pengguna.
     *
     * Penggunaan: middleware('role:admin,pengelola')
     * Memastikan hanya pengguna dengan role yang disebutkan yang bisa akses route.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $userRole = session('role');

        if (!in_array($userRole, $roles)) {
            return response()->view('errors.403', [], 403);
        }

        return $next($request);
    }
}
