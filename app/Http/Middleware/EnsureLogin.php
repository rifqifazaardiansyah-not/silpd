<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLogin
{
    /**
     * Middleware untuk memastikan user sudah login.
     *
     * Mengecek session 'role' dan 'ref_id'.
     * Jika belum login, redirect ke halaman login.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('role') || !session()->has('ref_id')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        return $next($request);
    }
}
